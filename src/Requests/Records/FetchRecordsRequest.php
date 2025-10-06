<?php

namespace IFresh\FileMakerODataApi\Requests\Records;

use IFresh\FileMakerODataApi\QueryOptions;
use Psr\Http\Message\RequestInterface;
use Saloon\Enums\Method;
use Saloon\Http\PendingRequest;
use Saloon\Http\Request;
use Saloon\Http\Response;

class FetchRecordsRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $database,
        private readonly string $table,
        private readonly ?QueryOptions $queryOptions = null,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return "/{$this->database}/{$this->table}";
    }

    public function createDtoFromResponse(Response $response): array
    {
        return $response->json('value');
    }

    public function handlePsrRequest(RequestInterface $request, PendingRequest $pendingRequest): RequestInterface
    {
        $queryOptions = $this->queryOptions->toArray();

        $queryString = collect($queryOptions)
            ->map(fn ($value, $key) => "{$key}={$value}")
            ->join('&');

        $uri = $request->getUri()->withQuery($queryString);

        return $request->withUri($uri);
    }

    protected function defaultQuery(): array
    {
        if (filled($this->queryOptions)) {
            return $this->queryOptions->toArray();
        }

        return [];
    }
}
