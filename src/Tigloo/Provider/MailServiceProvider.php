<?php
declare(strict_types = 1);

namespace Tigloo\Provider;

use Tigloo\Adapters\AdapterMail;
use Tigloo\Container\Contracts\ContainerInterface;
use Tigloo\Core\Contracts\ServiceProviderInterface;

final class MailServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $app): void
    {
        $app->set('mail', function ($app) {
            return $app->get('mail.factory');
        });

        $app->set('mail.factory', $app->factory(function ($app) {
            return new AdapterMail([
                'host' => $app->get('environment')->MAIL_HOST ?? '127.0.0.1',
                'port' => $app->get('environment')->MAIL_PORT ?? 465,
                'username' => $app->get('environment')->MAIL_USERNAME ?? 'user',
                'password' => $app->get('environment')->MAIL_PASSWORD ?? 'root',
            ]);
        }));
    }
}