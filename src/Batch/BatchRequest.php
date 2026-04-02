<?php

namespace IFresh\FileMakerODataApi\Batch;

use IFresh\FileMakerODataApi\Batch\Body\BatchBody;
use IFresh\FileMakerODataApi\Batch\Body\BatchPart;
use IFresh\FileMakerODataApi\Batch\Body\ChangeSet;
use Saloon\Contracts\Body\BodyRepository;
use Saloon\Contracts\Body\HasBody;
use Saloon\Http\Request;
use Saloon\Enums\Method;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasStringBody;

final class BatchRequest extends Request implements HasBody
{
    use HasStringBody;

    protected Method $method = Method::POST;

    protected BatchBody $bodyBuilder;

    protected string $boundary;

    public function __construct(
        private readonly string $baseUrl,
        private readonly string $database,
    ) {
        $this->boundary = sprintf('batch_%s', uniqid());
        $this->bodyBuilder = new BatchBody($this->boundary);
    }

    public function resolveEndpoint(): string
    {
        return sprintf('/%s/$batch', $this->database);
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => sprintf('multipart/mixed; boundary=%s', $this->boundary),
            'OData-Version' => '4.0',
        ];
    }

    public function add(Request $request): static
    {
        $this->bodyBuilder->addPart(
            new BatchPart(
                method: strtoupper($request->getMethod()->value),
                url: $this->baseUrl . $request->resolveEndpoint(),
            )
        );

        return $this;
    }

    public function addChangeSet(callable $callback): static
    {
        $changeSet = new ChangeSet($this->baseUrl);

        $callback($changeSet);

        $this->bodyBuilder->addChangeSet($changeSet);

        return $this;
    }

    public function defaultBody(): ?string
    {
        return $this->bodyBuilder->toString();
    }

    public function resolveResponseClass(): string
    {
        return BatchResponse::class;
    }
}