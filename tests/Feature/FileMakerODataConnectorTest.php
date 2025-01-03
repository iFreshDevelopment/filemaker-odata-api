<?php

use IFresh\FileMakerODataApi\FileMakerODataConnector;
use Saloon\Http\Auth\BasicAuthenticator;

it('uses basic auth', function () {
    $connector = new FileMakerODataConnector(
        host: 'example.com',
        username: 'username',
        password: 'password'
    );

    expect($connector->getAuthenticator())
        ->toBeInstanceOf(BasicAuthenticator::class)
        ->toHaveProperty('username', 'username')
        ->toHaveProperty('password', 'password');
});
