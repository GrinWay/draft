<?php

namespace App\Tests;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;

trait InitTrait {
	
    protected function setUp(): void
    {
		parent::setUp();
		
		$container = self::getContainer();
		$requestStack = $container->get(RequestStack::class);
		
		$session = new Session(new MockFileSessionStorage());
		$request = new Request();
		$request->setSession($session);
		
		$requestStack->push($request);
		static::ensureKernelShutdown();
    }
	
	protected function tearDown(): void
    {
        parent::tearDown();
    }
}
