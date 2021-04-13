<?php
declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use App\Infrastructure\CommandBus\CommandBusInterface;
use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\Locator\InMemoryLocator;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\HandleClassNameInflector;
use App\Domain\Handlers\User\CreateUserHandler;
use App\Domain\Commands\User\CreateUserCommand;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
        CommandBusInterface::class => function (ContainerInterface $c) {
            $locator = new InMemoryLocator();

            //Add here your Command handlers binding
            $locator->addHandler($c->get(CreateUserHandler::class), CreateUserCommand::class);

            $handlerMiddleware = new CommandHandlerMiddleware(
                new ClassNameExtractor(),
                $locator,
                new HandleClassNameInflector()
            );

            return new CommandBus([$handlerMiddleware]);
        },
        AMQPStreamConnection::class => function (ContainerInterface $c) {
            return new AMQPStreamConnection(
                getenv('QUEUE_IP'),
                getenv('QUEUE_PORT'),
                getenv('QUEUE_USER'),
                getenv('QUEUE_PASSWORD')
            );
        },
    ]);
};
