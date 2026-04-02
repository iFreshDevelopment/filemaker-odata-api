<?php

namespace IFresh\FileMakerODataApi\Batch\Parser;

use IFresh\FileMakerODataApi\Batch\ResponsePart;

class BatchPartParser
{
    public function parse(string $raw): ?ResponsePart
    {
        // Split outer headers from inner HTTP message
        $segments = preg_split("/\r\n\r\n/", $raw, 2);

        if (count($segments) < 2) {
            return null;
        }

        [$outerHeadersRaw, $httpMessage] = $segments;

        // Now split HTTP headers from body
        $httpSegments = preg_split("/\r\n\r\n/", $httpMessage, 2);

        $httpHeaderBlock = $httpSegments[0] ?? '';
        $body = $httpSegments[1] ?? null;

        $lines = preg_split("/\r\n/", trim($httpHeaderBlock));

        if (empty($lines)) {
            return null;
        }

        // First line = status line
        $statusLine = array_shift($lines);

        if (!preg_match('#HTTP/\d\.\d\s+(\d+)#', $statusLine, $matches)) {
            return null;
        }

        $status = (int) $matches[1];

        // Parse headers
        $headers = [];

        foreach ($lines as $line) {
            if (str_contains($line, ':')) {
                [$key, $value] = explode(':', $line, 2);
                $headers[trim($key)] = trim($value);
            }
        }

        // Decode JSON if applicable
        $decodedBody = $body;

        if (
            isset($headers['Content-Type']) &&
            str_contains($headers['Content-Type'], 'application/json')
        ) {
            $decoded = json_decode($body, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $decodedBody = $decoded;
            }
        }

        return new ResponsePart(
            status: $status,
            headers: $headers,
            body: $decodedBody
        );
    }
}
