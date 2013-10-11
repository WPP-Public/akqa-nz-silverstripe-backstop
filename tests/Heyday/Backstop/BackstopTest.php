<?php

namespace Heyday\Backstop;

/**
 * Class BackstopTest
 * @package Heyday\Backstop
 */
class BackstopTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(
            'Heyday\Backstop\Backstop',
            $backstop = new Backstop(
                $logger = $this->getMock('Psr\Log\LoggerInterface')
            )
        );

        $this->assertEquals($logger, $backstop->getLogger());
    }
    
    public function testConstructStatusConfig()
    {
        $this->assertInstanceOf(
            'Heyday\Backstop\Backstop',
            $backstop = new Backstop(
                $this->getMock('Psr\Log\LoggerInterface'),
                array()
            )
        );
        
        $this->assertEquals(array(), $backstop->getStatusConfig());
    }
    /**
     *
     */
    public function testPostRequestLog400()
    {
        $backstop = new Backstop(
            $logger = $this->getMock('Psr\Log\LoggerInterface')
        );

        $logger->expects($this->once())
            ->method('error')
            ->with("'400' status code error on 'test'");
        
        $backstop->postRequest(
            new \SS_HTTPRequest('GET', '/test/'),
            new \SS_HTTPResponse(null, 400),
            new \DataModel()
        );
    }
    /**
     *
     */
    public function testPostRequestLog500()
    {
        $backstop = new Backstop(
            $logger = $this->getMock('Psr\Log\LoggerInterface')
        );

        $logger->expects($this->once())
            ->method('critical')
            ->with("'500' status code error on 'test'");

        $backstop->postRequest(
            new \SS_HTTPRequest('GET', '/test/'),
            new \SS_HTTPResponse(null, 500),
            new \DataModel()
        );
    }
    /**
     *
     */
    public function testPostRequestLog300()
    {
        $backstop = new Backstop(
            $logger = $this->getMock('Psr\Log\LoggerInterface')
        );

        $logger->expects($this->never())
            ->method('error');

        $backstop->postRequest(
            new \SS_HTTPRequest('GET', '/test/'),
            new \SS_HTTPResponse(null, 301),
            new \DataModel()
        );
    }
}