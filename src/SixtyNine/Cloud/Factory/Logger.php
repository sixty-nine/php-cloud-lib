<?php

namespace SixtyNine\Cloud\Factory;

use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;

class Logger
{
    const DEBUG = 100;
    const INFO = 200;
    const NOTICE = 250;
    const WARNING = 300;
    const ERROR = 400;
    const CRITICAL = 500;
    const ALERT = 550;
    const EMERGENCY = 600;

    /** @var Logger */
    protected static $instance;
    /** @var Logger */
    protected $logger;
    /** @var string */
    protected $filename;
    /** @var bool */
    protected $outputChosen = false;

    protected function __construct()
    {
        $this->logger = new MonologLogger('log');
    }

    public  static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function toConsole()
    {
        $this->outputChosen = true;
        $this->logger->pushHandler(new ErrorLogHandler());
        return $this;
    }

    public function toFile($filename, $level = MonologLogger::DEBUG)
    {
        if ($this->filename) {
            throw new \InvalidArgumentException('Log file already set');
        }
        $this->outputChosen = true;
        $this->logger->pushHandler(new StreamHandler($filename, $level));
        $this->filename = $filename;
        return $this;
    }

    public function log($message, $level = MonologLogger::INFO, array $context = array())
    {
        if (!$this->outputChosen) {
            $this->outputChosen = true;
            $this->logger->pushHandler(new NullHandler());
        }
        $this->logger->log($level, $message, $context);
    }

    public function getLogger()
    {
        return $this->logger;
    }
}
