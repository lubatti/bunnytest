<?php
declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use App\Infrastructure\Persistence\Doctrine\EntityManagerBuilder;
use DI\ContainerBuilder;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\Locator\InMemoryLocator;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\HandleClassNameInflector;
use App\Domain\Handlers\User\CreateUserHandler;
use App\Domain\Commands\User\CreateUserCommand;
use App\Infrastructure\Persistence\Doctrine\Repositories as Repositories;
use App\Domain\Entities as Entities;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;

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
        CommandBus::class => function (ContainerInterface $c) {
            $locator = new InMemoryLocator();

            //Add here your Command handlers binding
            $locator->addHandler($c->get(CreateUserHandler::class), CreateUserCommand::class);

            $handlerMiddleware = new CommandHandlerMiddleware(
                new ClassNameExtractor(),
                $locator,
                new HandleInflector()
            );

            return new CommandBus([$handlerMiddleware]);
        },
        EntityManager::class => DI\factory(function () {
            return EntityManagerBuilder::build();
        }),
        EntityManagerInterface::class => DI\factory(function (ContainerInterface $c) {
            return $c->get(EntityManager::class);
        }),
        Connection::class => DI\factory(function (ContainerInterface $c) {
            return $c->get(EntityManagerInterface::class)->getConnection();
        }),
        AMQPStreamConnection::class => function (ContainerInterface $c) {
            return new AMQPStreamConnection(
                getenv('QUEUE_IP'),
                getenv('QUEUE_PORT'),
                getenv('QUEUE_USER'),
                getenv('QUEUE_PASSWORD')
            );
        },
        Repositories\UserRepository::class => DI\factory(function (ContainerInterface $c) {
            return $c->get(EntityManager::class)->getRepository(Entities\User::class);
        }),
    ]);
};
