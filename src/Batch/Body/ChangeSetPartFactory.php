<?php

namespace IFresh\FileMakerODataApi\Batch\Body;

use Saloon\Http\Request;

class ChangeSetPartFactory
{
    public static function fromRequest(Request $request, string $baseUrl): ChangeSetPart
    {
        $url = sprintf('%s%s', rtrim($baseUrl, '/'), $request->resolveEndpoint());

        return new ChangeSetPart(
            method: strtoupper($request->getMethod()->value),
            url: $url,
            headers: $request->headers()->all(),
            body: self::extractBody($request),
            contentId: self::generateContentId(),
        );
    }

    private static function extractBody(Request $request): ?string
    {
        $body = $request->body();

        if ($body === null) {
            return null;
        }

        return (string) $body;
    }

    private static function generateContentId(): string
    {
        static $id = 1;
        return (string) $id++;
    }
}
