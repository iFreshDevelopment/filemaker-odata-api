<?php

namespace IFresh\FileMakerODataApi\Requests\Metadata;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetDatabaseMetadataRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $database
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return "/{$this->database}/\$metadata";
    }
}
