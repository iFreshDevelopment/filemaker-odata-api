<?php

namespace IFresh\FileMakerODataApi;

class QueryOptions
{
    public function __construct(
        public ?string $filter = null,
        public ?string $sort = null,
        public ?int $limit = null,
        public ?int $offset = null,
        public bool $withCount = false,
        public ?string $select = null,
    ) {
        //
    }

    public function toArray(): array
    {
        $parameters = [
            '$filter' => $this->filter,
            '$orderby' => $this->sort,
            '$top' => $this->limit,
            '$skip' => $this->offset,
            '$count' => $this->withCount ? 'true' : null,
            '$select' => $this->select,
        ];

        return array_filter(
            array: $parameters,
            callback: function ($value) {
                return filled($value);
            });
    }
}
