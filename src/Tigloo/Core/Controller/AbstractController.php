<?php
declare(strict_types = 1);

namespace Tigloo\Core\Controller;

use Psr\Http\Message\ResponseInterface;
use Tigloo\Container\Contracts\ContainerInterface;
use GuzzleHttp\Psr7\{Response, MimeType, Utils};
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

    public function json(array $data = [], int $code = 200, ?ResponseInterface $response = null): ResponseInterface
    {
        if (null === $response) {
            $response = new Response();
        }       

        $response = $response->withStatus($code);
        $response = $response->withHeader('Content-Type', MimeType::fromExtension('json'));
        $response = $response->withBody(Utils::streamFor($this->jsonEncode($data)));
        return $response;
    }

    private function jsonEncode(array $data = [])
    {
        try {
            $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); 
        } catch(\Exception $e) {
            throw new RuntimeException('', 500);
        }

        if (\JSON_THROW_ON_ERROR & JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) {
            return $json;
        }

        if (\JSON_ERROR_NONE !== json_last_error()) {
            throw new RuntimeException(json_last_error_msg(), 500);
        }

        return $json;
    }
}