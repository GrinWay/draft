<?php

namespace App\DependencyInjection\Pass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

// TODO: TagServiceLocatorsPass

/**
 * Passes ServiceLocators grouped by "owner" format key to the collector service method
 *
 * At the same time expanded $dynamicTagFormat is a tag and a key passed to the service collector method
 *
 * Owner 1 (OWNER_TAG_NAME)
 *     - dynamic 1 |                     |
 *     - dynamic 2 | DYNAMIC_TAG_NAME 1  | DynamicServiceLocator 1
 *     - dynamic 3 |                     |
 * Owner 2 (OWNER_TAG_NAME)
 *      - dynamic 1 |                    |
 *      - dynamic 2 | DYNAMIC_TAG_NAME 2 | DynamicServiceLocator 2
 *      - dynamic 3 |                    |
 * Owner 3 (OWNER_TAG_NAME)
 *      - dynamic 1 |                    |
 *      - dynamic 2 | DYNAMIC_TAG_NAME 3 | DynamicServiceLocator 3
 *      - dynamic 3 |                    |
 * ...
 */
class TagServiceLocatorsPass implements CompilerPassInterface
{
    public function __construct(
        private readonly string $collectorServiceId,
        private readonly string $collectorServiceMethodCallName,
        /**
         * Each service with this tag has its own ServiceLocator by the other tag ($dynamicTagFormat)
         */
        private readonly string $ownerTagName,
        private readonly string $staticOwnerMethodNameForDynamicFormatCompletion,
        private readonly string $dynamicTagFormat,
    )
    {
    }

    public function process(ContainerBuilder $container)
    {
        $collectorDefinition = $container->findDefinition($this->collectorServiceId);

        foreach ($container->findTaggedServiceIds($this->ownerTagName) as $serviceId => $serviceTagAttributes) {
            $ownerDefinition = $container->findDefinition($serviceId);
            $ownerClass = $ownerDefinition->getClass();
            $dynamicParameter = [$ownerClass, $this->staticOwnerMethodNameForDynamicFormatCompletion]();
            $dynamicTagName = \sprintf($this->dynamicTagFormat, $dynamicParameter);

            $dynamicDefinitions = [];
            foreach ($container->findTaggedServiceIds($dynamicTagName) as $dynamicId => $dynamicTagAttributes) {
                $dynamicDefinition = $container->findDefinition($dynamicId);
                // array_pop instead [0] or array_shift, cuz if I use AutoconfigureTag, I want to use this but not previous priority
                $priority = \array_pop($dynamicTagAttributes)['priority'] ?? 0;
                $dynamicDefinitions[$priority][] = $dynamicDefinition;
            }
            $dynamicDefinitionsDepthDecreased = [];
            \krsort($dynamicDefinitions);
            \array_walk_recursive($dynamicDefinitions, static function ($val, $key) use (&$dynamicDefinitionsDepthDecreased) {
                $dynamicDefinitionsDepthDecreased[] = $val;
            });
            $dynamicLocator = ServiceLocatorTagPass::register($container, $dynamicDefinitionsDepthDecreased);
            $collectorDefinition->addMethodCall($this->collectorServiceMethodCallName, [
                $dynamicTagName,
                $dynamicLocator,
            ]);
        }
    }
}
