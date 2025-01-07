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

        return $uri->withQuery(
            http_build_query(
                data: array_merge($existingQuery, $this->query()->all()),
                encoding_type: PHP_QUERY_RFC3986,
            )
        );
    }
}
