<?php

/*
 * Execute all the tests and report on their progress.
 */

namespace Programster\B2;

require_once(__DIR__ . '/Bootstrap.php');

# Load all the tests within the tests folder and create their objects in the tests array.
# All tests should be written so that order does not matter!
$testsDirectory = __DIR__ . '/tests';
$files = array_diff(scandir($testsDirectory), array('..', '.'));

foreach ($files as $file)
{
    $testFilepath = __DIR__ . '/tests/' . $file;
    require_once($testFilepath);
    $className = basename($file, '.php');
    $tests[] = new B2Test();
}

# Execute all of the tests.
$timeStart = microtime(true);

foreach ($tests as $test)
{
    /* @var $test AbstractTest */
    print "Running " . get_class($test) . PHP_EOL;
    $test->run();
}

$timeFinish = microtime(true);
$timeTaken = $timeFinish - $timeStart;

# Output reporting.
$failedTests = array();
foreach ($tests as $test)
{
    /* @var $test AbstractTest */
    if (!$test->getPassed())
    {
        $failedTests[] = $test;
    }
}

$result = "FAILURE";
if (count($failedTests) == 0)
{
    $result = "PASSED";
}


if (count($failedTests) > 0)
{
    print "The following tests failed:" . PHP_EOL;
    
    foreach ($failedTests as $test)
    {
        print get_class($test) . ": " . $test->getErrorMessage() . PHP_EOL;
    }
}
else
{
    print "" . PHP_EOL;
    print "" . PHP_EOL;
    print "Test performance:" . PHP_EOL;
    
    foreach ($tests as $test)
    {
        print get_class($test) . ": " . $test->getTimeTaken() . PHP_EOL;
    }
}


print "" . PHP_EOL;
print "SUMMARY" . PHP_EOL;
print "============" . PHP_EOL;
print "Result: " . $result . PHP_EOL;
print "Testing Time taken: " . $timeTaken . PHP_EOL;



