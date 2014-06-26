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

    public function setUp()
    {
        $this->slack = $this->getMockBuilder('TailoredTunes\SlackNotifier')
            ->disableOriginalClone()
            ->setMethods(array('send'))
            ->getMock();

        $this->errorChannel = $this->randomSring();
        $this->exceptionChannel = $this->randomSring();
        $this->errorHandler = new SlackErrorReporter($this->slack, $this->errorChannel, $this->exceptionChannel);

    }

    public function testErrorHandler()
    {
        $text = sprintf(
            'ERROR - [1024] %s:%d - error',
            realpath(__FILE__),
            __LINE__ + 3
        );
        $this->slack->expects($this->once())->method('send')->with($text, $this->errorChannel);
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
            'EXCEPTION - [0] %s:%d - %s',
            realpath(__FILE__),
            __LINE__+4,
            $msg
        );
        $this->slack->expects($this->once())->method('send')->with($text, $this->exceptionChannel);
        $this->errorHandler->exceptionHandler(new \Exception($msg));
    }

    private function randomSring()
    {
        return uniqid();
    }
}
