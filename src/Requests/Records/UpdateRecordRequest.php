<?php

namespace IFresh\FileMakerODataApi\Requests\Records;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class UpdateRecordRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    public function __construct(
        public readonly string $database,
        public readonly string $table,
        public readonly string $primaryKeyValue,
        public readonly array $data
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return "/{$this->database}/{$this->table}('{$this->primaryKeyValue}')";
    }

    protected function defaultBody(): array
    {
        return $this->data;
    }

    public function createDtoFromResponse(Response $response): array
    {
        return $response->json();
    }
}
