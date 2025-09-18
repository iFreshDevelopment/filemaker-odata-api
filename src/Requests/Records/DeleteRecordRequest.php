<?php

namespace IFresh\FileMakerODataApi\Requests\Records;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class DeleteRecordRequest extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        public readonly string $database,
        public readonly string $table,
        public readonly string $primaryKeyValue,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        if (ctype_digit($this->primaryKeyValue)) {
            return "/{$this->database}/{$this->table}({$this->primaryKeyValue})";
        }

        return "/{$this->database}/{$this->table}('{$this->primaryKeyValue}')";
    }
}
