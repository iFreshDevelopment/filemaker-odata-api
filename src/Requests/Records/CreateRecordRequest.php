<?php

namespace IFresh\FileMakerODataApi\Requests\Records;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class CreateRecordRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        private readonly string $database,
        private readonly string $table,
        private readonly array $data
    ) {}

    public function resolveEndpoint(): string
    {
        return "/{$this->database}/{$this->table}";
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
