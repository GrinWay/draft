<?php

namespace App\EnvVarProcessor;

use Symfony\Component\DependencyInjection\EnvVarProcessorInterface;

// TODO: HttpsToStringEnvVarProcessor
class HttpsToStringEnvVarProcessor implements EnvVarProcessorInterface
{
    const NAME = 'https_to_string';

    /**
     * @param string $prefix
     * @param string $name
     * @param \Closure $getEnv
     * @return mixed
     */
    public function getEnv(string $prefix, string $name, \Closure $getEnv): mixed
    {
        $env = $getEnv($name);

        if ('on' === \strtolower($env)) {
            return 'https';
        }

        return 'http';
    }

    /**
     * @return string[]
     */
    public static function getProvidedTypes(): array
    {
        return [
            self::NAME => 'string',
        ];
    }
}
