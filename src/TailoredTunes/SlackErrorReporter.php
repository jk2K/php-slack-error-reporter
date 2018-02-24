<?php

namespace TailoredTunes;

class SlackErrorReporter
{

    const DEFAULT_CHANNEL = '#errors';

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
    /**
     * @var string
     */
    private $username;

    public function __construct(
        SlackNotifier $slack,
        $username = '',
        $errorChannel = self::DEFAULT_CHANNEL,
        $exceptionChannel = self::DEFAULT_CHANNEL
    ) {
        $this->errorChannel = $errorChannel;
        $this->exceptionChannel = $exceptionChannel;
        set_error_handler([$this, "errorHandler"]);
        set_exception_handler([$this, "exceptionHandler"]);
        $this->slack = $slack;
        $this->username = $username;
    }

    public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if (error_reporting() === 0) {
            return;
        }

        $errorMsg = sprintf("ERROR - [%s] %s:%d - %s", $errno, $errfile, $errline, $errstr);
        $this->slack->send($errorMsg, $this->errorChannel, $this->username);
    }

    public function exceptionHandler(\Throwable $e)
    {
        if (error_reporting() === 0) {
            return;
        }

        $errorMsg = sprintf(
            "EXCEPTION - [%s] %s:%d - %s\nStacktrace:\n\n%s",
            $e->getCode(),
            $e->getFile(),
            $e->getLine(),
            $e->getMessage(),
            $e->getTraceAsString()
        );
        $this->slack->send($errorMsg, $this->exceptionChannel, $this->username);
    }
}
