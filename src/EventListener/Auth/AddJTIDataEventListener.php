<?php

namespace App\EventListener\Auth;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener('lexik_jwt_authentication.on_jwt_created')]
class AddJTIDataEventListener
{
    public function __invoke(JWTCreatedEvent $event)
    {
//        $data = $event->getData();
//        \dump($data);
//        $data['jti'] = '';
//        $event->setData($data);
    }
}
