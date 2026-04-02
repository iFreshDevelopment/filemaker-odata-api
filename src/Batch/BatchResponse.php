<?php

namespace IFresh\FileMakerODataApi\Batch;

use IFresh\FileMakerODataApi\Batch\Parser\MultipartParser;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Saloon\Http\PendingRequest;
use Saloon\Http\Response;
use IFresh\FileMakerODataApi\Batch\Parser\BatchPartParser;

class BatchResponse extends Response
{
    protected array $parts = [];

    protected string $boundary;

    public function __construct(
        ResponseInterface $response,
        PendingRequest $pendingRequest,
        RequestInterface $psrRequest,
        ?\Throwable $senderException = null
    ) {
        parent::__construct(
            $response,
            $pendingRequest,
            $psrRequest,
            $senderException,
        );

        $this->boundary = $this->extractBoundary();
        $this->parse();
    }

    protected function extractBoundary(): string
    {
        $contentType = $this->psrResponse->getHeaders()['Content-Type'][0] ?? '';

        if (!preg_match('/boundary=([^;]+)/', $contentType, $matches)) {
            throw new \RuntimeException('Missing batch boundary in response.');
        }

        return trim($matches[1], '"');
    }

    protected function parse(): void
    {
        $raw = (string) $this->psrResponse->getBody();

        $sections = (new MultipartParser())->split($raw, $this->boundary);

        foreach ($sections as $section) {
            $part = (new BatchPartParser())->parse($section);

            if ($part !== null) {
                $this->parts[] = $part;
            }
        }
    }

    public function all(): array
    {
        return $this->parts;
    }
}
