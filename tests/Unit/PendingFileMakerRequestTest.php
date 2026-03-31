<?php

use IFresh\FileMakerODataApi\FileMakerODataConnector;
use IFresh\FileMakerODataApi\QueryOptions;
use IFresh\FileMakerODataApi\Requests\PendingFileMakerRequest;
use IFresh\FileMakerODataApi\Requests\Records\FetchRecordsRequest;

it('encodes filemaker query values using rawurlencode', function () {
    $connector = new FileMakerODataConnector(
        host: 'https://example.com',
        username: 'user',
        password: 'pass',
        database: 'Tasks',
    );

    $request = new FetchRecordsRequest(
        database: 'Tasks',
        table: 'Assignees',
        queryOptions: new QueryOptions(
            filter: "name eq 'test' and id eq 1",
        ),
    );

    $pendingRequest = new PendingFileMakerRequest($connector, $request);

    expect((string) $pendingRequest->getUri())
        ->toBe('https://example.com/fmi/odata/v4/Tasks/Assignees?%24filter=name%20eq%20%27test%27%20and%20id%20eq%201');
});
