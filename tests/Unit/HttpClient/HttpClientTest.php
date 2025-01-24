<?php

namespace App\Tests\Unit\HttpClient;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpClientTest extends WebTestCase
{
//    public function testScopedClientHeadersAreOverwrittenByManuallyPassedOnes()
//    {
//        $container = self::getContainer();
//        $profiler = $container->get('profiler');
//        $profiler->enable();
//        $testHeadersClient = $container->get(HttpClientInterface::class . ' $testHeadersClient');
//
//        assert($testHeadersClient instanceof HttpClientInterface);
//        $response = $testHeadersClient->request('GET', '/', [
//            'headers' => [
//                'Accept' => 'true',
//            ],
//        ]);
//
//        \dump($profiler->get('http_client')->getRequestCount());
//    }
}
