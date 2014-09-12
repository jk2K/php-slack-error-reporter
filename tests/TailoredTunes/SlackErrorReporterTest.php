<?php

namespace TailoredTunes;

class SlackErrorReporterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var SlackNotifier
     */
    private $slack;

    /**
     * @var SlackErrorReporter
     */
    private $errorHandler;

    private $errorChannel = '#errors';
    private $exceptionChannel = '#exceptions';
    private $username = 'test';

    public function setUp()
    {
        $this->slack = $this->getMockBuilder('TailoredTunes\SlackNotifier')
            ->disableOriginalClone()
            ->disableOriginalConstructor()
            ->setMethods(array('send'))
            ->getMock();

        $this->errorChannel = $this->randomSring();
        $this->exceptionChannel = $this->randomSring();
        $this->username = $this->randomSring();
        $this->errorHandler = new SlackErrorReporter(
            $this->slack,
            $this->username,
            $this->errorChannel,
            $this->exceptionChannel
        );

    }

    public function testErrorHandler()
    {
        $text = sprintf(
            'ERROR - [1024] %s:%d - error',
            realpath(__FILE__),
            __LINE__ + 3
        );
        $this->slack->expects($this->once())->method('send')->with($text, $this->errorChannel, $this->username);
        trigger_error('error');
    }


    public function testExceptionHandlerIsIset()
    {
        $cb = function () {

        };
        $this->assertEquals(array($this->errorHandler, 'exceptionHandler'), set_exception_handler($cb));
        restore_exception_handler();

    }

    public function testExceptionHandler()
    {
        $msg = $this->randomSring();
        $text = sprintf(
            "EXCEPTION - [0] %s:%d - %s\nStacktrace:\n\n%s",
            realpath(__FILE__),
            __LINE__ + 27,
            $msg,
            "#0 [internal function]: TailoredTunes\\SlackErrorReporterTest->testExceptionHandler()\n" .
            "#1 ".realpath(__DIR__.'/../../')."/vendor/phpunit/phpunit/src/Framework/TestCase.php(951): " .
            "ReflectionMethod->invokeArgs(Object(TailoredTunes\\SlackErrorReporterTest), Array)\n" .
            "#2 ".realpath(__DIR__.'/../../')."/vendor/phpunit/phpunit/src/Framework/TestCase.php(817): " .
            "PHPUnit_Framework_TestCase->runTest()\n" .
            "#3 ".realpath(__DIR__.'/../../')."/vendor/phpunit/phpunit/src/Framework/TestResult.php(686): " .
            "PHPUnit_Framework_TestCase->runBare()\n" .
            "#4 ".realpath(__DIR__.'/../../')."/vendor/phpunit/phpunit/src/Framework/TestCase.php(753): " .
            "PHPUnit_Framework_TestResult->run(Object(TailoredTunes\\SlackErrorReporterTest))\n" .
            "#5 ".realpath(__DIR__.'/../../')."/vendor/phpunit/phpunit/src/Framework/TestSuite.php(675): " .
            "PHPUnit_Framework_TestCase->run(Object(PHPUnit_Framework_TestResult))\n" .
            "#6 ".realpath(__DIR__.'/../../')."/vendor/phpunit/phpunit/src/Framework/TestSuite.php(675): " .
            "PHPUnit_Framework_TestSuite->run(Object(PHPUnit_Framework_TestResult))\n" .
            "#7 ".realpath(__DIR__.'/../../')."/vendor/phpunit/phpunit/src/TextUI/TestRunner.php(426): " .
            "PHPUnit_Framework_TestSuite->run(Object(PHPUnit_Framework_TestResult))\n" .
            "#8 ".realpath(__DIR__.'/../../')."/vendor/phpunit/phpunit/src/TextUI/Command.php(179): " .
            "PHPUnit_TextUI_TestRunner->doRun(Object(PHPUnit_Framework_TestSuite), Array)\n" .
            "#9 ".realpath(__DIR__.'/../../')."/vendor/phpunit/phpunit/src/TextUI/Command.php(132): " .
            "PHPUnit_TextUI_Command->run(Array, true)\n" .
            "#10 ".realpath(__DIR__.'/../../')."/vendor/phpunit/phpunit/phpunit(55): " .
            "PHPUnit_TextUI_Command::main()\n" .
            "#11 {main}"
        );
        $this->slack->expects($this->once())->method('send')
            ->with($text, $this->exceptionChannel, $this->username);
        $this->errorHandler->exceptionHandler(new \Exception($msg));
    }

    private function randomSring()
    {
        return uniqid();
    }
}
