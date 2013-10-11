<?php

namespace Heyday\Backstop;

use DataModel;
use RequestFilter;
use Session;
use SS_HTTPRequest;
use SS_HTTPResponse;
use Psr\Log\LoggerInterface;

/**
 * Class Backstop
 * @package Heyday\Backstop
 */
class Backstop implements RequestFilter
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * An array of status pattern to error log level
     * @var array
     */
    protected $statusConfig = array(
        '4??' => 'error',
        '5??' => 'critical'
    );

    /**
     * @param LoggerInterface $logger
     * @param null $statusConfig
     */
    public function __construct(LoggerInterface $logger, $statusConfig = null)
    {
        $this->logger = $logger;
        if (is_array($statusConfig)) {
            $this->statusConfig = $statusConfig;
        }
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return array
     */
    public function getStatusConfig()
    {
        return $this->statusConfig;
    }

    /**
     * Not used
     * @param SS_HTTPRequest $request
     * @param Session $session
     * @param DataModel $model
     */
    public function preRequest(SS_HTTPRequest $request, Session $session, DataModel $model)
    {
        //NOP
    }

    /**
     * If the response status code matches a code defined in self::$statusCofig, log using
     * the appropriate logging level
     * @param SS_HTTPRequest $request
     * @param SS_HTTPResponse $response
     * @param DataModel $model
     */
    public function postRequest(SS_HTTPRequest $request, SS_HTTPResponse $response, DataModel $model)
    {
        $statusCode = $response->getStatusCode();

        foreach ($this->statusConfig as $statusPattern => $errorLog) {
            if (fnmatch($statusPattern, (string) $statusCode)) {
                $this->logger->$errorLog(
                    sprintf(
                        "'%s' status code error on '%s'",
                        $statusCode,
                        $request->getURL()
                    ),
                    array(
                        'request' => $request,
                        'response' => $response
                    )
                );
            }
        }
    }
}
