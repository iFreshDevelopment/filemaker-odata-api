<?php

namespace IFresh\FileMakerODataApi\Batch\Body;

final class BatchPart
{
    private const string CONTENT_TYPE = 'Content-Type: application/http';
    private const string CONTENT_TRANSFER_ENCODING = 'Content-Transfer-Encoding: binary';

    public function __construct(
        public string $method,
        public string $url,
        public array $headers = [],
        public ?string $body = null,
    ) {}

    public function toString(): string
    {
        $eol = "\r\n";
        $lines = [];

        $lines[] = self::CONTENT_TYPE;
        $lines[] = self::CONTENT_TRANSFER_ENCODING;
        $lines[] = '';

        $lines[] = sprintf('%s %s HTTP/1.1', $this->method, $this->url);

        foreach ($this->headers as $k => $v) {
            $lines[] = sprintf('%s: %s', $k, $v);
        }

        $lines[] = '';

        if ($this->body !== null) {
            $lines[] = $this->body;
        }

        return implode($eol, $lines) . $eol . $eol;
    }
}
