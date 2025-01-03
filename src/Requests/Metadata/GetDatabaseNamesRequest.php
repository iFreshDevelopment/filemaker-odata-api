<?php

namespace IFresh\FileMakerODataApi\Requests\Metadata;

use Illuminate\Support\Arr;
use JsonException;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetDatabaseNamesRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/';
    }

    /**
     * @return string[]
     *
     * @throws JsonException
     */
    public function createDtoFromResponse(Response $response): array
    {
        $value = $response->json('value');

        return Arr::pluck($value, 'name');
    }
}
