<?php

namespace App\Session;

use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy;

#[AsAlias('app.framework.session_handler')]
class EncryptedSessionProxy extends SessionHandlerProxy
{
    public function read($id): string|false
    {
        return parent::read($id);
    }

    public function write($id, $data): bool
    {
        return parent::write($id, $data);
    }
}
