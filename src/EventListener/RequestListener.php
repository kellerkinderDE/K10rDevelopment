<?php

declare(strict_types=1);

namespace K10rDevelopment\EventListener;

use RuntimeException;
use Shopware\Core\DevOps\Environment\EnvironmentHelper;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

class RequestListener implements EventSubscriberInterface
{
    private const VALID_MAILER_URL                  = 'smtp://127.0.0.1:1025';
    private const MAILER_CONFIGURATION_KEY          = 'core.mailerSettings.emailAgent';
    private const VALID_MAILER_CONFIGURATION_VALUES = [
        '',
        null,
    ];

    private SystemConfigService $systemConfigService;

    public function __construct(
        SystemConfigService $systemConfigService
    ) {
        $this->systemConfigService = $systemConfigService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.controller' => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $this->assertMailConfiguration();
    }

    private function assertMailConfiguration(): void
    {
        $mailerUrl = EnvironmentHelper::getVariable('MAILER_URL') ?? EnvironmentHelper::getVariable('MAILER_DSN');

        if (strpos($mailerUrl, self::VALID_MAILER_URL) === false) {
            throw new RuntimeException(sprintf('Fix your mailer URL, set it to %s', self::VALID_MAILER_URL));
        }

        $emailAgent = $this->systemConfigService->get(self::MAILER_CONFIGURATION_KEY);

        if (!in_array($emailAgent, self::VALID_MAILER_CONFIGURATION_VALUES)) {
            throw new RuntimeException('Fix your mailer URL, set it to use the environment');
        }
    }
}
