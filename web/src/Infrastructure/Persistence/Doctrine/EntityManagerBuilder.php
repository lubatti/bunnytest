<?php

namespace App\Infrastructure\Persistence\Doctrine;

use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\Events;
use Doctrine\ORM\Tools\ResolveTargetEntityListener;

class EntityManagerBuilder
{
    public static function build(): EntityManager
    {
        $paths = [
            __DIR__ . '../../../Domain/Entities',
        ];

        $config = Setup::createAnnotationMetadataConfiguration(
            $paths,
            true,
            __DIR__ . '../../../../var/tmp/proxies',
            null,
            false
        );

        $config->setAutoGenerateProxyClasses(true);

        $dbParams = self::getDbParams();

        $evm  = new EventManager();
        $rtel = new ResolveTargetEntityListener();

        $evm->addEventListener(Events::loadClassMetadata, $rtel);

        return EntityManager::create($dbParams, $config, $evm);
    }

    private static function getDbParams(): array
    {
        return [
            'driver' => 'pdo_mysql',
            'host' => getenv('DB_HOST') ?: 'localhost',
            'dbname' => getenv('DB_NAME'),
            'user' => getenv('DB_USER'),
            'password' => getenv('DB_PASSWORD'),
        ];
    }
}
