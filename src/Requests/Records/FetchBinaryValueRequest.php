<?php

namespace IFresh\FileMakerODataApi\Requests\Records;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class FetchBinaryValueRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $database,
        private readonly string $table,
        private readonly string $primaryKeyValue,
        private readonly string $fieldName,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        if (ctype_digit($this->primaryKeyValue)) {
            return "/{$this->database}/{$this->table}({$this->primaryKeyValue})/{$this->fieldName}/\$value";
        }

        return "/{$this->database}/{$this->table}('{$this->primaryKeyValue}')/{$this->fieldName}/\$value";
    }
}
