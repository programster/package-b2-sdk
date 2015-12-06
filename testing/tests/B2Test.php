<?php

/* 
 * Test the B2 class
 */


namespace Programster\B2;

class B2Test extends AbstractTest
{
    private $m_b2;
    private $m_authorizationToken;
    
    protected function test() 
    {
        $this->m_b2 = new B2(ACCOUNT_ID, APPLICATION_KEY);
        $this->m_authorizationToken = $this->m_b2->authorizeAccount();
        $this->deleteExistingBuckets();
        $bucket = $this->createBucketTest();
        $this->listBuckets();
        $uploadedFile = $this->uploadFile($bucket);
        $this->downloadFile($uploadedFile);
        $this->listFileVersions($bucket);
        $this->deleteBucket($bucket);
        
        $this->m_passed = true;
    }
    
    
    private function downloadFile(B2File $file)
    {
        $file->download();
        die();
    }
    
    
    /**
     * Delete all existing buckets in the system.
     */
    private function deleteExistingBuckets()
    {
        print "Deleting existing buckets..." . PHP_EOL;
        $buckets = $this->m_b2->listBuckets();
        
        foreach ($buckets as $bucket)
        {
            /* @var $bucket \Programster\B2\Bucket */
            $bucket->delete();
        }
        print "Finished deleting existin buckets." . PHP_EOL;
    }
    
    
    private function createBucketTest()
    {
        print "creating a bucket." . PHP_EOL;
        $bucketName = "bucket-" . time();
        $bucket = $this->m_b2->createBucket($bucketName, $isPrivate=true);
        return $bucket;
    }
    
    
    private function listBuckets()
    {
        print "Your buckets: " . print_r($this->m_b2->listBuckets(), true) . PHP_EOL;
    }
    
    
    private function listFileVersions(Bucket $bucket)
    {
        $this->m_b2->listFileVersions($bucket->getId());
    }
    
    
    private function deleteBucket(Bucket $bucket)
    {
        print "Deleting bucket: " . $bucket->getName() . PHP_EOL;
        $bucket->delete();
    }
    
    
    private function uploadFile(Bucket $bucket)
    {
        print "Uploading hello world file to bucket: " . $bucket->getName() . PHP_EOL;
        $contents = "Hello world";
        $uploadFilename = "hello_world_" . time() . ".txt";
        $filepath = __DIR__ . '/upload_file.txt';
        file_put_contents($filepath, $contents);
        $uploadUrlResponse = $this->m_b2->getUploadUrl($bucket->getId());
        $b2File = $this->m_b2->uploadFile($uploadFilename, $filepath, $uploadUrlResponse);
        return $b2File;
    }
}



