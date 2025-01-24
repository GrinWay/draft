<?php

namespace App;

use App\DependencyInjection\Pass\TagServiceLocatorsPass;
use App\Service\Telegram\Telegram;
use App\Trait\Telegram\TelegramAwareTrait;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;
    use TelegramAwareTrait;

    protected function build(ContainerBuilder $container): void
    {
        $this->telegramUpdateHandlerPass($container);
    }

    /**
     * Helper
     */
    private function telegramUpdateHandlerPass(ContainerBuilder $container): void
    {
        $dynamicTagFormat = $this->getUpdateHandlerKey('%s');

        $container->addCompilerPass(new TagServiceLocatorsPass(
            Telegram::class,
            'setUpdateHandlerIterator',
            'grinway.telegram_handler',
            'updateField',
            $dynamicTagFormat,
        ));
    }
}
