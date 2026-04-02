<?php

namespace IFresh\FileMakerODataApi\Batch\Body;

use Saloon\Http\Request;

final class ChangeSet
{
    protected array $parts = [];

    protected string $boundary;

    public function __construct(
        private readonly string $baseUrl,
        ?string $boundary = null,
    ) {
        $this->boundary = $boundary ?? sprintf('changeset_%s', uniqid());
    }

    public function add(Request $request): self
    {
        $this->parts[] = ChangeSetPartFactory::fromRequest(
            $request,
            $this->baseUrl
        );

        return $this;
    }

    public function toString(): string
    {
        $eol = "\r\n";
        $body = '';

        /** @var ChangeSetPart $part */
        foreach ($this->parts as $part) {
            $body .= sprintf('--%s%s', $this->boundary, $eol);
            $body .= $part->toString();
        }

        $body .= sprintf('--%s--%s', $this->boundary, $eol);

        return implode($eol, [
            sprintf('Content-Type: multipart/mixed; boundary=%s', $this->boundary),
            '',
            $body
        ]);
    }
}
