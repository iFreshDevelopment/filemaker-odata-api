<?php

namespace IFresh\FileMakerODataApi;

use GuzzleHttp\RequestOptions;
use IFresh\FileMakerODataApi\Resources\Resources\MetadataResource;
use IFresh\FileMakerODataApi\Resources\Resources\RecordsResource;
use Saloon\Contracts\Authenticator;
use Saloon\Http\Auth\BasicAuthenticator;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;

class FileMakerODataConnector extends Connector
{
    const ODATA_VERSION = 'v4';

    use AcceptsJson;
    use AlwaysThrowOnErrors;

    public function __construct(
        public readonly string $host,
        public readonly string $username,
        public readonly string $password,
    ) {
        //
    }

    protected function defaultConfig(): array
    {
        return [
            RequestOptions::VERIFY => false,
            RequestOptions::TIMEOUT => 10,
        ];
    }

    public function resolveBaseUrl(): string
    {
        return "{$this->host}/fmi/odata/".self::ODATA_VERSION;
    }

    protected function defaultAuth(): ?Authenticator
    {
        return new BasicAuthenticator(
            username: $this->username,
            password: $this->password
        );
    }

    public function metadata(): MetadataResource
    {
        return new MetadataResource($this);
    }

    public function records(
        string $database,
        string $table
    ): RecordsResource {
        return new RecordsResource($this, $database, $table);
    }
}
