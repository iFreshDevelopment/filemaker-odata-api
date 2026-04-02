<?php

namespace IFresh\FileMakerODataApi\Batch\Body;

class ChangeSetPart
{
    private const string CONTENT_TYPE = 'Content-Type: application/http';

    public function __construct(
        public string $method,
        public string $url,
        public array $headers = [],
        public ?string $body = null,
        public ?string $contentId = null,
    ) {}

    public function toString(): string
    {
        $eol = "\r\n";
        $lines = [];

        $lines[] = self::CONTENT_TYPE;

        if ($this->contentId !== null) {
            $lines[] = sprintf('Content-ID: %s', $this->contentId);
        }

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
