<?php

namespace IFresh\FileMakerODataApi\Resources\Resources;

use IFresh\FileMakerODataApi\QueryOptions;
use IFresh\FileMakerODataApi\Requests\Records\CreateRecordRequest;
use IFresh\FileMakerODataApi\Requests\Records\DeleteRecordRequest;
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
        $request = new CreateRecordRequest(
            $this->database,
            $this->table,
            $data
        );

        return $this->connector->send($request)->dto();
    }

    public function updateRecord(string $primaryKey, array $data)
    {
        $request = new UpdateRecordRequest(
            $this->database,
            $this->table,
            $primaryKey,
            $data
        );

        return $this->connector->send($request)->dto();
    }

    public function deleteRecord(string $key): Response
    {
        $request = new DeleteRecordRequest(
            $this->database,
            $this->table,
            $key
        );

        return $this->connector->send($request);
    }

    public function fetchRecords(?QueryOptions $queryOptions = null): array
    {
        $request = new FetchRecordsRequest(
            $this->database,
            $this->table,
            $queryOptions
        );

        return $this->connector->send($request)->dto();
    }

    public function fetchSingleRecord(string $primaryKey): array
    {
        $request = new FetchSingleRecordRequest(
            $this->database,
            $this->table,
            $primaryKey
        );

        return $this->connector->send($request)->dto();
    }
}
