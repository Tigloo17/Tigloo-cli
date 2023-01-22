<?php
declare(strict_types = 1);

namespace Tigloo\Provider;

use Tigloo\Container\Contracts\ContainerInterface;
use Tigloo\Core\Contracts\ServiceProviderInterface;
use Medoo\Medoo;

final class DatabaseServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $app): void
    {
        $app->set('db', function ($app) {
            return $app->get('db.factory');
        });

        $app->set('db.factory', $app->factory(function ($app) {
            return new Medoo([
                'type' => 'mysql',
                'host' => $app->get('environment')->DB_HOST,
                'database' => $app->get('environment')->DB_DATABASE,
                'username' => $app->get('environment')->DB_USERNAME,
                'password' => $app->get('environment')->DB_PASSWORD,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ]);
        }));
    }
}