<?php

namespace Tests\Fixtures;

use Saloon\Http\Faking\Fixture;

class ODataFixture extends Fixture
{
    protected function defineSensitiveRegexPatterns(): array
    {
        if (! array_key_exists('FM_HOST', $_ENV)) {
            return parent::defineSensitiveRegexPatterns();
        }

        return [
            '/'.$_ENV['FM_HOST'].'/' => 'FM_HOST',
        ];
    }

    protected function defineSensitiveHeaders(): array
    {
        return [
            'Access-Control-Allow-Origin' => 'REDACTED',
            'Location' => 'REDACTED',
        ];
    }

}
