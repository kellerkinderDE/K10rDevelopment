<?php

declare(strict_types=1);

namespace K10rDevelopment\EventListener;

use Shopware\Core\DevOps\Environment\EnvironmentHelper;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

class RequestListener implements EventSubscriberInterface
{
    private const VALID_MAILER_URLS = [
        'smtp://127.0.0.1:1025',
        'smtp://localhost:1025',
    ];
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
        $mailerUrl        = (string) (EnvironmentHelper::getVariable('MAILER_URL') ?? EnvironmentHelper::getVariable('MAILER_DSN'));
        $mailerUrlIsValid = false;
        foreach (self::VALID_MAILER_URLS as $validMailerUrl) {
            if (strpos($mailerUrl, $validMailerUrl) === 0) {
                $mailerUrlIsValid = true;

                break;
            }
        }

        if (!$mailerUrlIsValid) {
            throw new \RuntimeException(sprintf('Fix your mailer URL, set it to one of: %s', implode(', ', self::VALID_MAILER_URLS)));
        }

        $emailAgent = $this->systemConfigService->get(self::MAILER_CONFIGURATION_KEY);

        if (!in_array($emailAgent, self::VALID_MAILER_CONFIGURATION_VALUES)) {
            throw new \RuntimeException('Fix your mailer configuration, set it to use the environment');
        }
    }
}
