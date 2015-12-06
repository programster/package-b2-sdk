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
        $bucket = $this->createBucketTest();
        $this->listBuckets();
        $this->deleteBucket($bucket);
        
        $this->m_passed = true;
    }
    
    
    private function createBucketTest()
    {
        $bucketName = "bucket-" . time();
        $bucket = $this->m_b2->createBucket($bucketName, $isPrivate=true);
        return $bucket;
    }
    
    
    private function listBuckets()
    {
        print "Your buckets: " . print_r($this->m_b2->listBuckets(), true) . PHP_EOL;
    }
    
    
    private function deleteBucket(Bucket $bucket)
    {
        $response = $this->m_b2->deleteBucket($bucket->getId());
        print print_r($response,true);
    }
    
    
    
}



