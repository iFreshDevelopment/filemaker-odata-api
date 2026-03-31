<?php

namespace IFresh\FileMakerODataApi\Requests;

use Psr\Http\Message\UriInterface;
use Saloon\Helpers\URLHelper;
use Saloon\Http\PendingRequest;

class PendingFileMakerRequest extends PendingRequest
{
    public function getUri(): UriInterface
    {
        $uri = $this->factoryCollection->uriFactory->createUri($this->getUrl());

        // We'll parse the existing query parameters from the URL (if they have been defined)
        // and then we'll merge in Saloon's query parameters. Our query parameters will take
        // priority over any that were defined in the URL.

        $existingQuery = URLHelper::parseQueryString($uri->getQuery());

        $query = collect(array_merge($existingQuery, $this->query()->all()))
            ->map(fn (mixed $value, string $key): string => sprintf(
                '%s=%s',
                rawurlencode($key),
                rawurlencode((string) $value),
            ))
            ->implode('&');

        return $uri->withQuery($query);
    }
}
