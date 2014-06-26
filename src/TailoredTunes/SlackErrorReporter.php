<?php

namespace TailoredTunes;

class SlackErrorReporter
{


    /**
     * @var string
     */
    private $errorChannel;
    /**
     * @var string
     */
    private $exceptionChannel;
    /**
     * @var SlackNotifier
     */
    private $slack;

    public function __construct(SlackNotifier $slack, $errorChannel = '#errors', $exceptionChannel = '#errors')
    {
        $this->errorChannel = $errorChannel;
        $this->exceptionChannel = $exceptionChannel;
        set_error_handler(array($this, "errorHandler"));
        set_exception_handler(array($this, "exceptionHandler"));
        $this->slack = $slack;
    }

    public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        $errorMsg = sprintf("ERROR - [%s] %s:%d - %s", $errno, $errfile, $errline, $errstr);
        $this->slack->send($errorMsg, $this->errorChannel);
    }

    public function exceptionHandler(\Exception $e)
    {
        $errorMsg = sprintf(
            "EXCEPTION - [%s] %s:%d - %s",
            $e->getCode(),
            $e->getFile(),
            $e->getLine(),
            $e->getMessage()
        );
        $this->slack->send($errorMsg, $this->exceptionChannel);
    }
}
