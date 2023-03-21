<?php

declare(strict_types=1);

namespace K10rDevelopment\EventListener;

use Doctrine\DBAL\Connection;
use Shopware\Core\DevOps\Environment\EnvironmentHelper;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

class MailListener implements EventSubscriberInterface
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
    private Connection $connection;

    public function __construct(
        SystemConfigService $systemConfigService,
        Connection $connection
    ) {
        $this->systemConfigService = $systemConfigService;
        $this->connection          = $connection;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.controller'    => 'onKernelController',
            ConsoleEvents::COMMAND => 'onCommand',
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        if ($this->connection->isConnected()) {
            $this->assertMailConfiguration();
        }
    }

    public function onCommand(ConsoleCommandEvent $event): void
    {
        if ($this->connection->isConnected()) {
            $this->assertMailConfiguration();
        }
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
