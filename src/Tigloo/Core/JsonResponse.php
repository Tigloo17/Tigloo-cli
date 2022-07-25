<?php
declare(strict_types = 1);

namespace Tigloo\Core;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use RuntimeException;

final class JsonResponse extends Response
{
    public function withJson(array $data = []) {
        $jsonResponse = [
            'csrf_name' => (new Session())->get('csrf_name') ?? null,
            'status' => $this->getStatusCode(),
            'message' => $this->getReasonPhrase(),
            'data' => $data
        ];

        return $this->withBody(Utils::streamFor($this->jsonEncode($jsonResponse)));
    }

    private function jsonEncode(array $data)
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