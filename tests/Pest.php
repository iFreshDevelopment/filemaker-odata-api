<?php

use IFresh\FileMakerODataApi\FileMakerODataConnector;
use Illuminate\Support\Str;
use Saloon\Http\Faking\Fixture;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\PendingRequest;
use Tests\Fixtures\ODataFixture;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function mockClient(): MockClient
{
    return new MockClient([
        FileMakerODataConnector::class => function (PendingRequest $pendingRequest): Fixture {
            $requestClass = $pendingRequest->getRequest()::class;

            $namespace = Str::beforeLast($requestClass, '\\');

            $resource = Str::afterLast($namespace, '\\');
            $request = Str::afterLast($requestClass, '\\');

            return new ODataFixture('/'.implode('/', [$resource, $request]));
        },
    ]);
}
