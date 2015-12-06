<?php

/* 
 * Class to represent a file in the Backblaze system.
 */

namespace Programster\B2;

class B2File
{   
    private $m_b2; /* @var $m_b2 B2 */
    private $m_id;
    private $m_fileName;
    private $m_contentLength;
    private $m_bucketId;
    
    # these may or may not be set.
    private $m_fileInfo;
    private $m_contentSha1;
    
    
    /**
     * Please use one of the static creation methods to create one of these objects.
     */
    private function __construct(){}
    
    
    /**
     * 
     * @param \Programster\B2\B2 $b2
     * @param \stdClass $response
     * @return \Programster\B2\B2File
     * @throws \Exception
     */
    public static function createFromUploadResponse(B2 $b2, \stdClass $response)
    {
        if
        (
            !isset($response->fileId) ||
            !isset($response->fileName) ||
            !isset($response->bucketId) ||
            !isset($response->fileInfo) ||
            !isset($response->contentSha1) ||
            !isset($response->contentLength)
        )
        {
            throw new \Exception("Upload response error: " . json_encode($response));
        }
        
        $file = new B2File();
        $file->m_b2 = $b2;
        $file->m_id = $response->fileId;
        $file->m_fileName = $response->fileName;
        $file->m_bucketId = $response->bucketId;
        $file->m_fileInfo = $response->fileInfo;
        $file->m_contentSha1 = $response->contentSha1;
        $file->m_contentLength = $response->contentLength;
        
        return $file;
    }
    
    
    /**
     * Create a B2File object from the stdclass provided from listFileVersions.
     * @param type $bucketId
     * @param \stdClass $stdClass
     */
    public static function createFromListFileVersions(B2 $b2, $bucketId, \stdClass $stdClass)
    {
        $file = new B2File();
        $file->m_b2 = $b2;
        $file->m_id = $stdClass->fileId;
        $file->m_fileName = $stdClass->fileName;
        $file->m_contentLength = $stdClass->size;
        $file->m_bucketId = $bucketId;
        return $file;
    }
    
    
    /**
     * Delete this file from B2
     */
    public function delete()
    {
        $this->m_b2->deleteFileVersion($this->m_fileName, $this->m_id);
    }
    
    
    /**
     * Download this file.
     */
    public function download()
    {
        $this->m_b2->downloadFileById($this->m_id);
    }
}

