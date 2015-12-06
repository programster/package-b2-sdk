<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Programster\B2;

class UploadUrlResponse
{
    private $m_uploadUrl;
    private $m_authorizationToken;
    
    public function __construct(\stdClass $responseObj)
    {
        if (!isset($responseObj->uploadUrl) || !isset($responseObj->authorizationToken))
        {
            throw new \Exception("Failed to get upload url: " . json_encode($responseObj));
        }
        
        $this->m_uploadUrl = $responseObj->uploadUrl;
        $this->m_authorizationToken = $responseObj->authorizationToken;
    }
    
    public function getUploadUrl() { return $this->m_uploadUrl; }
    public function getAuthorizationToken() { return $this->m_authorizationToken; }
}