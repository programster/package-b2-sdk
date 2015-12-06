<?php

/* 
 * Abstract test that all tests should extend.
 */

namespace Programster\B2;

abstract class AbstractTest
{
    protected $m_timeTaken;
    protected $m_passed = false;
    protected $m_errorMessage = "";
    
    /**
     * Run the test.
     * If any exception is thrown then the test is considered a failure.
     */
    protected abstract function test();
    
    
    
    public function run()
    {
        $start = microtime(true);
        
        try
        {
            $this->test();
        } 
        catch (Exception $ex) 
        {
            $this->m_passed = false;
            $this->m_errorMessage = $ex->getMessage();
        }
        
        $finish = microtime(true);
        $this->m_timeTaken = $finish = $start;
    }
    
    # Accessors
    public function getTimeTaken() { return $this->m_timeTaken; }
    public final function getPassed() { return $this->m_passed; }
    public final function getErrorMessage() { return $this->m_errorMessage; }
}

