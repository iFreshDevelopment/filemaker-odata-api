<?php

namespace IFresh\FileMakerODataApi\Batch;

class ResponsePart
{
    public function __construct(
        protected int $status,
        protected array $headers = [],
        protected mixed $body = null,
    ) {}

    public function status(): int
    {
        return $this->status;
    }

    public function body(): mixed
    {
        return $this->body;
    }

    public function isSuccess(): bool
    {
        return $this->status >= 200 && $this->status < 300;
    }
}
