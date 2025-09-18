<?php

namespace IFresh\FileMakerODataApi\Requests\Records;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class FetchSingleRecordRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $database,
        private readonly string $table,
        private readonly string $primaryKeyValue,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        if (ctype_digit($this->primaryKeyValue)) {
            return "/{$this->database}/{$this->table}({$this->primaryKeyValue})";
        }

        return "/{$this->database}/{$this->table}('{$this->primaryKeyValue}')";
    }

    public function createDtoFromResponse(Response $response): array
    {
        return $response->json();
    }
}
