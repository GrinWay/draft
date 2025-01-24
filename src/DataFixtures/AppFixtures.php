<?php

namespace App\DataFixtures;

use App\Controller\DynamicController;
use App\Factory\FoundryFactory;
use App\Factory\TodoFactory;
use App\Factory\UserFactory;
use App\Router\RouteContent\DefaultRouteContent;
use App\Router\RouteObject\DefaultRoute;
use App\Story\FoundryStory;
use App\Story\UserStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\Constraint\ExceptionMessageIsOrContains;
use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Orm\Route;
use Symfony\Cmf\Component\Routing\ContentRepositoryInterface;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class AppFixtures extends Fixture
{
    private $manager;

    public function __construct(#[Autowire('@cmf_routing.content_repository')] private readonly ContentRepositoryInterface $contentRepository, private readonly EntityManagerInterface $em)
    {

    }

    public function load(ObjectManager $manager): void
    {
        FoundryStory::load();
        UserStory::load();

        TodoFactory::createMany(10);
        UserFactory::createOne([
           'email' => 'test',
           'password' => 'test',
        ]);
    }

    /**
     * https://symfony.com/bundles/CMFRoutingBundle/current/routing-bundle/dynamic.html
     *
     * @return void
     */
    private function loadDynamicRoutes()
    {
        $idx = 0;

        foreach ([
                     [
                         'static_prefix' => '/first',
                         'content' => FoundryFactory::first()->_real(),
                         'controller_name' => null,
                         'route_name' => null,
                     ],
                     [
                         'static_prefix' => '/last',
                         'content' => FoundryFactory::last()->_real(),
                         'controller_name' => null,
                         'route_name' => null,
                     ],
                 ] as ['content' => $content, 'controller_name' => $controllerName, 'route_name' => $routeName, 'static_prefix' => $staticPrefix]) {
            $controllerName ??= DynamicController::class . '::__invoke';
            $routeName ??= 'dynamic_route_name_'.$idx++;

            $route = new Route();
            $route->setName($routeName);
            $route->setStaticPrefix($staticPrefix);
            $route->setDefault(RouteObjectInterface::CONTROLLER_NAME, $controllerName);
            $route->setDefault(RouteObjectInterface::CONTENT_ID, $this->contentRepository->getContentId($content));
            $route->setContent($content);
//            $route->setOption('add_locale_pattern', true);

            $this->em->persist($route);
        }
        $this->em->flush();
    }
}
