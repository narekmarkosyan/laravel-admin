<?php

use Encore\Admin\Middleware\Pjax;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class PjaxTest extends TestCase
{
    public function testItExtractsTheRequestedContainerAndTitle()
    {
        $response = new Response('<html><head><title>Dashboard</title></head><body><div id="pjax-container"><p>Updated</p></div></body></html>');

        $middleware = new class extends Pjax {
            public function filter(Response $response, string $container)
            {
                $this->filterResponse($response, $container);
            }
        };

        $middleware->filter($response, '#pjax-container');

        $this->assertSame('<title>Dashboard</title><p>Updated</p>', $response->getContent());
    }
}
