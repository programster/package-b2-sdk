<?php

/* 
 * Class to represent a bucket in B2
 */

namespace Programster\B2;

class Bucket
{
    private $m_b2;
    private $m_id;
    private $m_name;
    private $m_type;
    
    public function __construct(B2 $b2, $id, $name, $type)
    {
        $this->m_b2 = $b2;
        $this->m_id = $id;
        $this->m_name = $name;
        $this->m_type = $type;
    }
    
    
    /**
     * Delete the bucket, along with all its contents.
     */
    public function delete()
    {
        $this->m_b2->emptyBucket($this->m_id);
        $this->m_b2->deleteBucket($this->m_id);
    }
    
    
    # Accessors
    public function getId() { return $this->m_id; }
    public function getName() { return $this->m_name; }
    public function getType() { return $this->m_type; }

}

