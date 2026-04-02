<?php

namespace IFresh\FileMakerODataApi\Batch\DTO;

use IFresh\FileMakerODataApi\Batch\ResponsePart;

final class BatchResult
{
    /** @param array<int, ResponsePart|null> $results */
    public function __construct(
        protected array $results,
        protected int $expectedCount,
    ) {}

    public function all(): array
    {
        return $this->results;
    }
}
