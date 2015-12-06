<?php

/* 
 * Class to represent a bucket in B2
 */

namespace Programster\B2;

class Bucket
{
    private $m_id;
    private $m_name;
    private $m_type;
    
    public function __construct($id, $name, $type)
    {
        $this->m_id = $id;
        $this->m_name = $name;
        $this->m_type = $type;
    }
    
    
    # Accessors
    public function getId() { return $this->m_id; }
    public function getName() { return $this->m_name; }
    public function getType() { return $this->m_type; }

}

