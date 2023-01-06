<?php
declare(strict_types = 1);

namespace Tigloo\Core\Controller;

use Psr\Http\Message\ResponseInterface;
use Tigloo\Container\Contracts\ContainerInterface;
use GuzzleHttp\Psr7\{Response, MimeType, Utils};
use Tigloo\Core\JsonResponse;
use RuntimeException;

abstract class AbstractController
{
    private ContainerInterface $app;

    public function setApp(ContainerInterface $app): AbstractController
    {
        $this->app = $app;
        return $this;
    }

    public function getApp(): ContainerInterface
    {
        return $this->app;
    }

    public function render(string $render, array $parameters = [], ?ResponseInterface $response = null): ResponseInterface
    {
        $twig = $this->app->get('twig')->render($render, $parameters);
        if (null === $response) {
            $response = new Response();
        }
        $response = $response->withBody(Utils::streamFor($twig));
        return $response;
    }

    public function redirect(string $name, array $parameters = [], ?ResponseInterface $response = null): ResponseInterface
    {
        if (null === $response) {
            $response = new Response();
        }

        $response = $response->withStatus(302);
        $response = $response->withHeader('location', $this->getApp()->get('router')->generate($name, $parameters));
        return $response;
    }

    public function json(array $data = [], int $code = 200, ?ResponseInterface $response = null): ResponseInterface
    {
        if (null === $response) {
            $response = new JsonResponse();
        }       

        $response = $response->withStatus($code);
        $response = $response->withHeader('Content-Type', MimeType::fromExtension('json'));
        $response = $response->withJson($data);
        
        return $response;
    }
}