<?php

namespace IFresh\FileMakerODataApi\Resources\Resources;

use IFresh\FileMakerODataApi\QueryOptions;
use IFresh\FileMakerODataApi\RecordsQueryBuilder;
use IFresh\FileMakerODataApi\Requests\Records\CreateRecordRequest;
use IFresh\FileMakerODataApi\Requests\Records\DeleteRecordRequest;
use IFresh\FileMakerODataApi\Requests\Records\FetchBinaryValueRequest;
use IFresh\FileMakerODataApi\Requests\Records\FetchRecordsRequest;
use IFresh\FileMakerODataApi\Requests\Records\FetchSingleRecordRequest;
use IFresh\FileMakerODataApi\Requests\Records\UpdateRecordRequest;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Saloon\Http\Response;

class RecordsResource extends BaseResource
{
    public function __construct(
        Connector $connector,
        private readonly string $database,
        private readonly string $table,
    ) {
        parent::__construct($connector);
    }

    public function createRecord(array $data)
    {
        return $this->connector->send(new CreateRecordRequest(
            $this->database,
            $this->table,
            $data
        ))->dto();
    }

    public function query(): RecordsQueryBuilder
    {
        return new RecordsQueryBuilder($this);
    }

    public function where(string|callable $field, mixed $operator = null, mixed $value = null): RecordsQueryBuilder
    {
        return $this->query()->where($field, $operator, $value);
    }

    public function orWhere(string|callable $field, mixed $operator = null, mixed $value = null): RecordsQueryBuilder
    {
        return $this->query()->orWhere($field, $operator, $value);
    }

    public function whereRaw(string $expression): RecordsQueryBuilder
    {
        return $this->query()->whereRaw($expression);
    }

    public function whereContains(string $field, string $value): RecordsQueryBuilder
    {
        return $this->query()->whereContains($field, $value);
    }

    public function orWhereRaw(string $expression): RecordsQueryBuilder
    {
        return $this->query()->orWhereRaw($expression);
    }

    public function whereStartsWith(string $field, string $value): RecordsQueryBuilder
    {
        return $this->query()->whereStartsWith($field, $value);
    }

    public function orWhereStartsWith(string $field, string $value): RecordsQueryBuilder
    {
        return $this->query()->orWhereStartsWith($field, $value);
    }

    public function orWhereContains(string $field, string $value): RecordsQueryBuilder
    {
        return $this->query()->orWhereContains($field, $value);
    }

    public function orderBy(string $field, string $direction = 'asc'): RecordsQueryBuilder
    {
        return $this->query()->orderBy($field, $direction);
    }

    public function select(string ...$fields): RecordsQueryBuilder
    {
        return $this->query()->select(...$fields);
    }

    public function updateRecord(string $primaryKey, array $data)
    {
        return $this->connector->send(new UpdateRecordRequest(
            $this->database,
            $this->table,
            $primaryKey,
            $data
        ))->dto();
    }

    public function deleteRecord(string $key): Response
    {
        return $this->connector->send(new DeleteRecordRequest(
            $this->database,
            $this->table,
            $key
        ));
    }

    public function getRecordCount(?QueryOptions $queryOptions = null): int
    {
        $queryOptions ??= new QueryOptions;
        $queryOptions->withCount = true;
        $queryOptions->offset = 0;
        $queryOptions->limit = 1;

        return $this->connector->send($this->makeFetchRecordsRequest($queryOptions))->json('@count');
    }

    public function fetchRecords(?QueryOptions $queryOptions = null): array
    {
        $queryOptions ??= new QueryOptions;

        return $this->connector->send($this->makeFetchRecordsRequest($queryOptions))->dto();
    }

    public function fetchSingleRecord(string $primaryKey): array
    {
        return $this->connector->send(new FetchSingleRecordRequest(
            $this->database,
            $this->table,
            $primaryKey
        ))->dto();
    }

    public function fetchBinaryValue(string $primaryKey, string $fieldName): string
    {
        return $this->connector->send(new FetchBinaryValueRequest(
            $this->database,
            $this->table,
            $primaryKey,
            $fieldName
        ))->body();
    }

    private function makeFetchRecordsRequest(QueryOptions $queryOptions): FetchRecordsRequest
    {
        return new FetchRecordsRequest(
            $this->database,
            $this->table,
            $queryOptions,
        );
    }
}
