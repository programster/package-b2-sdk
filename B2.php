<?php

/*
 * Class for interfacing with the backblaze b2 api.
 * https://www.backblaze.com/b2/docs/b2_create_bucket.html
 */

namespace Programster\B2;


class B2
{
    private $m_accountId;
    private $m_applicationKey;
    private $m_authorizationToken;
    private $m_apiUrl;
    private $m_downloadUrl;
    
    
    /**
     * 
     * @param type $accountId
     * @param type $applicationKey
     */
    public function __construct($accountId, $applicationKey)
    {
        $this->m_accountId = $accountId;
        $this->m_applicationKey = $applicationKey;
        $response = $this->authorizeAccount();
        $this->m_apiUrl = $response->apiUrl;
        $this->m_downloadUrl = $response->downloadUrl;
        $this->m_authorizationToken = $response->authorizationToken;
    }
    
    
    /**
     * 
     * @return type
     */
    public function authorizeAccount()
    {
        $params = array(
            "action" => "b2_authorize_account",
        );
        
        return $this->sendRequest($params);
    }
    
    
    /**
     * https://www.backblaze.com/b2/docs/b2_create_bucket.html
     * @param string $bucketName - the name for the bucket
     * @param bool $isPrivate - specify true if should be private, false if public.
     */
    public function createBucket($bucketName, $isPrivate)
    {
        $bucketType = "allPublic";
        
        if ($isPrivate)
        {
            $bucketType = "allPrivate";
        }
        
        $params = array(
            "action" => "b2_create_bucket",
            "accountId" => $this->m_accountId,
            "bucketName" => $bucketName,
            "bucketType" => $bucketType,
        );
        
        $response = $this->sendAuthTokenRequest($params);
        $bucket = new Bucket(
            $this, 
            $response->bucketId, 
            $response->bucketName, 
            $response->bucketType
        );
        
        return $bucket;
    }
    
    
    /**
     * https://www.backblaze.com/b2/docs/b2_delete_bucket.html
     * @param type $bucketId - The ID of the bucket to delete.
     */
    public function deleteBucket($bucketId)
    {
        $params = array(
            "action" => "b2_delete_bucket",
            "accountId" => $this->m_accountId,
            "bucketId" => $bucketId
        );
        
        return $this->sendAuthTokenRequest($params);
    }
    
    
    /**
     * Custom method to delete all files within a bucket
     * @param type $bucketId - The ID of the bucket to delete.
     */
    public function emptyBucket($bucketId)
    {
        do
        {
            $files = $this->listFileVersions($bucketId, $maxFileCount=1000);
            
            foreach ($files as $file)
            {
                /* @var $file \Programster\B2\B2File */
                $file->delete();
            }
        } while(count($files) > 0);
    }
    
    
    /**
     * Deletes one version of a file from B2.
     * https://www.backblaze.com/b2/docs/b2_delete_file_version.html
     * @param string $fileName - The name of the file.
     * @param type $fileId - The ID of the file, as returned by b2_upload_file, 
     *                        b2_list_file_names, or b2_list_file_versions.
     */
    public function deleteFileVersion($fileName, $fileId)
    {
        $params = array(
            "action"    => "b2_delete_file_version",
            "fileName"  => $fileName,
            "fileId"    => $fileId
        );
        
        $response = $this->sendAuthTokenRequest($params);
        /* @todo error checking  */
    }
    
    
    /**
     * Downloads one file from B2.
     * https://www.backblaze.com/b2/docs/b2_download_file_by_id.html
     */
    public function downloadFileById($fileId)
    {        
        $params = array(
            "action" => "b2_download_file_by_id",
            "fileId" => $fileId,
        );
        
        $response = $this->sendAuthTokenRequest($params);
        var_dump($response);
    }
    
    
    /**
     * 
     * @return type
     */
    public function downloadFileByName()
    {
        $params = array(
            "action" => "b2_download_file_by_name",
        );
        
        return $this->sendAuthTokenRequest($params);
    }
    
    
    /**
     * https://www.backblaze.com/b2/docs/b2_get_file_info.html
     * @param type $fileId
     */
    public function getFileInfo($fileId)
    {
        $params = array(
            "action" => "b2_get_file_info",
            "fileId" => $fileId
        );
        
        return $this->sendAuthTokenRequest($params);
    }
    
    
    /**
     * Gets a URL to use for uploading files.
     * When you upload a file to B2, you must call b2_get_upload_url first to get the URL 
     * for uploading directly to the place where the file will be stored.
     * @return UploadUrlResponse
     */
    public function getUploadUrl($bucketId)
    {
        $params = array(
            "action" => "b2_get_upload_url",
            "bucketId" => $bucketId
        );
        
        $response = $this->sendAuthTokenRequest($params);
        $responseObj = new UploadUrlResponse($response);
        
        return $responseObj;
    }
    
    
    /**
     * Hides a file so that downloading by name will not find the file, 
     * but previous versions of the file are still stored. See File Versions 
     * about what it means to hide a file.
     * @param type $bucketId
     * @param string $fileName - the name of the file to hide.
     */
    public function hideFile($bucketId, $fileName)
    {
        $params = array(
            "action" => "b2_hide_file",
            "bucketId" => $bucketId,
            "fileName" => $fileName
        );
        
        return $this->sendAuthTokenRequest($params);
    }
    
    
    /**
     * Lists buckets associated with an account, in alphabetical order by bucket ID.
     * https://www.backblaze.com/b2/docs/b2_list_buckets.html
     */
    public function listBuckets()
    {
        $buckets = array();
        
        $params = array(
            "action" => "b2_list_buckets",
            "accountId" => $this->m_accountId
        );
        
        $response = $this->sendAuthTokenRequest($params);
        
        if (isset($response->buckets))
        {
            foreach ($response->buckets as $bucketStdClass)
            {
                $buckets[] = new Bucket($this, 
                    $bucketStdClass->bucketId, 
                    $bucketStdClass->bucketName, 
                    $bucketStdClass->bucketType
                );
            }
        }
        else
        {
            throw new \Exception("Error fetching bucket list: " . json_encode($response));
        }
        
        return $buckets;
    }
    
    
    /**
     * Lists the names of all files in a bucket, starting at a given name.
     * https://www.backblaze.com/b2/docs/b2_list_file_names.html
     */
    public function listFileNames()
    {
        $params = array(
            "action"    => "b2_list_file_names",
            "accountId" => $this->m_accountId
        );
        
        return $this->sendAuthTokenRequest($params);
    }
    
    
    /**
     * Lists all of the versions of all of the files contained in 
     * one bucket, in alphabetical order by file name, and by reverse 
     * of date/time uploaded for versions of files with the same name.
     * https://www.backblaze.com/b2/docs/b2_list_file_versions.html
     */
    public function listFileVersions($bucketId, $maxFileCount=100)
    {
        $params = array(
            "action"       => "b2_list_file_versions",
            "bucketId"     => $bucketId,
            "maxFileCount" => $maxFileCount
        );
        
        $response = $this->sendAuthTokenRequest($params);
        
        $fileStdClasses = $response->files;
        $files = array();
        
        foreach ($fileStdClasses as $fileStdClass)
        {
            $files[] = B2File::createFromListFileVersions($this, $bucketId, $fileStdClass);
        }
        
        return $files;
    }
    
    
    /**
     * Update an existing bucket.
     * Modifies the bucketType of an existing bucket. Can be used to allow everyone to download 
     * the contents of the bucket without providing any authorization, or to prevent anyone from 
     * downloading the contents of the bucket without providing a bucket auth token.
     * https://www.backblaze.com/b2/docs/b2_update_bucket.html
     */
    public function updateBucket($bucketId, $isPrivate)
    {
        $bucketType = "allPublic";
        
        if ($isPrivate)
        {
            $bucketType = "allPrivate";
        }
        
        $params = array(
            "action" => "b2_update_bucket",
            "bucketId" => $bucketId,
            "bucketType" => $bucketType
        );
        
        return $this->sendAuthTokenRequest($params);
    }
    
    
    /**
     * Uploads one file to B2, returning its unique file ID.
     * https://www.backblaze.com/b2/docs/b2_upload_file.html
     * @param string $uploadFileName - the name we wish to have for the file once uploaded.
     * @param string $filepath - the path to the file we wish to uplod
     * @param string $uploadAuthToken - // Provided by b2_get_upload_url
     * @param string $contentType - specify the mime type of the content. If not specified then
     *                              PHP will try to auto-detect it.
     * @return B2File
     */
    public function uploadFile($uploadFileName, $filepath, UploadUrlResponse $uploadUrlResponse, $contentType="")
    {
        if (!file_exists($filepath))
        {
            throw new Exception("File [" . $filepath . "] does not exist.");
        }
        
        if ($contentType === "")
        {
            $contentType = mime_content_type($filepath);
        }
        
        $handle = fopen($filepath, 'r');
        $read_file = fread($handle, filesize($filepath));
        
        $content_type = "text/plain";
        $sha1_of_file_data = sha1_file($filepath);
        
        $session = curl_init($uploadUrlResponse->getUploadUrl());
        
        // Add read file as post field
        curl_setopt($session, CURLOPT_POSTFIELDS, $read_file); 
        
        // Add headers
        $headers = array();
        $headers[] = "Authorization: " . $uploadUrlResponse->getAuthorizationToken();
        $headers[] = "X-Bz-File-Name: " . $uploadFileName;
        $headers[] = "Content-Type: " . $content_type;
        $headers[] = "X-Bz-Content-Sha1: " . $sha1_of_file_data;
        curl_setopt($session, CURLOPT_HTTPHEADER, $headers); 
        
        curl_setopt($session, CURLOPT_POST, true); // HTTP POST
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);  // Receive server response
        $server_output = curl_exec($session); // Let's do this!
        curl_close ($session); // Clean up
        $responseObj = json_decode($server_output);
        $file = B2File::createFromUploadResponse($this, $responseObj);
        
        return $file;
    }
    
    
    /**
     * Helper function to send the request to Backblaze.
     */
    private function sendRequest(array $params)
    {
        $credentials = base64_encode(ACCOUNT_ID . ":" . APPLICATION_KEY);
        $url = "https://api.backblaze.com/b2api/v1/" . $params['action'];
        
        unset($params['action']);
        $session = curl_init($url);
        
        // Add post fields
        $post_fields = json_encode($params);
        
        curl_setopt($session, CURLOPT_POSTFIELDS, $post_fields); 
        
        // Add headers
        $headers = array();
        $headers[] = "Accept: application/json";
        $headers[] = "Authorization: Basic " . $credentials;
        curl_setopt($session, CURLOPT_HTTPHEADER, $headers);  // Add headers
        
        curl_setopt($session, CURLOPT_HTTPGET, true);  // HTTP GET
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true); // Receive server response
        $server_output = curl_exec($session);
        curl_close ($session);
        $response = json_decode($server_output);
        
        if (isset($response->code) && $response->code === "bad_json")
        {
            throw new \Exception($response->message);
        }
        
        return $response; // Tell me about the rabbits, George!
    }
    
    
    /**
     * Helper function to send the request to Backblaze.
     * 
     */
    private function sendAuthTokenRequest(array $params)
    {
        $url = $this->m_apiUrl  . "/b2api/v1/" . $params['action'];
        
        unset($params['action']);
        $session = curl_init($url);
        
        // Add post fields
        $post_fields = json_encode($params);
        
        curl_setopt($session, CURLOPT_POSTFIELDS, $post_fields); 
        
        // Add headers
        $headers = array();
        $headers[] = "Authorization: " . $this->m_authorizationToken;
        curl_setopt($session, CURLOPT_HTTPHEADER, $headers); 
        
        curl_setopt($session, CURLOPT_POST, true); // HTTP POST
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);  // Receive server response
        $server_output = curl_exec($session); // Let's do this!
        curl_close ($session); // Clean up
        
        $response = json_decode($server_output);
        
        if ($response === null)
        {
            throw new \Exception($server_output);
        }
        
        if (isset($response->code) && $response->code === "bad_json")
        {
            throw new \Exception(json_encode($response));
        }
        
        return $response; // Tell me about the rabbits, George!
    }
}
