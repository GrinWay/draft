<?php

namespace App\HttpClient;

use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\HttpClient\AsyncDecoratorTrait;
use Symfony\Component\HttpClient\Response\AsyncContext;
use Symfony\Component\HttpClient\Response\AsyncResponse;
use Symfony\Contracts\HttpClient\ChunkInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

#[AsDecorator('http_client')]
class DefaultHttpClientDecorator implements HttpClientInterface
{
    use AsyncDecoratorTrait;

    private int $idx = 0;

    public function request(string $method, string $url, array $options = []): AsyncResponse
    {
        $this->idx = 0;

        $passthru = function (ChunkInterface $chunk, AsyncContext $context): \Generator {
//            if (1 < $this->idx++) {
//                $context->cancel();
//            }
//            \dump('CHUNK', $chunk->getContent());

            yield $chunk;
        };

        return new AsyncResponse($this->client, $method, $url, $options, $passthru);
    }
}
