<?php

namespace IFresh\FileMakerODataApi\Resources\Resources;

use IFresh\FileMakerODataApi\Requests\Metadata\GetDatabaseMetadataRequest;
use IFresh\FileMakerODataApi\Requests\Metadata\GetDatabaseNamesRequest;
use IFresh\FileMakerODataApi\Requests\Metadata\GetTableListRequest;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\BaseResource;

class MetadataResource extends BaseResource
{
    /**
     * Get a list of databases available on the host
     *
     * @return string[]
     *
     * @throws FatalRequestException
     * @throws RequestException
     */
    public function getDatabaseNames(): array
    {
        $request = new GetDatabaseNamesRequest;

        return $this->connector->send($request)->dto();
    }

    public function getTablesList(string $database): array
    {
        $request = new GetTableListRequest($database);

        return $this->connector->send($request)->dto();
    }

    public function getDatabaseMetadata(string $database)
    {
        $request = new GetDatabaseMetadataRequest($database);

        return $this->connector->send($request)->json();
    }
}
