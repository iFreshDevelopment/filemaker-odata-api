<?php

namespace IFresh\FileMakerODataApi;

use DateTimeInterface;
use IFresh\FileMakerODataApi\Resources\Resources\RecordsResource;
use InvalidArgumentException;

class RecordsQueryBuilder
{
    private const string BOOLEAN_AND = 'and';

    private const string BOOLEAN_OR = 'or';

    /**
     * @var list<string>
     */
    private const array SUPPORTED_OPERATORS = [
        '=',
        '==',
        '!=',
        '<>',
        '>',
        '>=',
        '<',
        '<=',
        'eq',
        'ne',
        'gt',
        'ge',
        'lt',
        'le',
    ];

    /**
     * @var list<array{boolean: 'and'|'or', expression: string}>
     */
    private array $filters = [];

    /**
     * @var list<string>
     */
    private array $orderBys = [];

    /**
     * @var list<string>
     */
    private array $selectedFields = [];

    private ?int $limit = null;

    private ?int $offset = null;

    private bool $withCount = false;

    public function __construct(
        private readonly RecordsResource $resource,
    ) {
        //
    }

    public function where(string|callable $field, mixed $operator = null, mixed $value = null): self
    {
        if (is_callable($field)) {
            return $this->addNestedWhere(self::BOOLEAN_AND, $field);
        }

        return $this->addWhere(self::BOOLEAN_AND, $field, $operator, $value);
    }

    public function orWhere(string|callable $field, mixed $operator = null, mixed $value = null): self
    {
        if (is_callable($field)) {
            return $this->addNestedWhere(self::BOOLEAN_OR, $field);
        }

        return $this->addWhere(self::BOOLEAN_OR, $field, $operator, $value);
    }

    public function whereRaw(string $expression): self
    {
        return $this->addRawFilter(self::BOOLEAN_AND, $expression);
    }

    public function orWhereRaw(string $expression): self
    {
        return $this->addRawFilter(self::BOOLEAN_OR, $expression);
    }

    public function whereStartsWith(string $field, string $value): self
    {
        return $this->addFunctionFilter(self::BOOLEAN_AND, 'startswith', $field, $value);
    }

    public function orWhereStartsWith(string $field, string $value): self
    {
        return $this->addFunctionFilter(self::BOOLEAN_OR, 'startswith', $field, $value);
    }

    public function whereContains(string $field, string $value): self
    {
        return $this->addFunctionFilter(self::BOOLEAN_AND, 'contains', $field, $value);
    }

    public function orWhereContains(string $field, string $value): self
    {
        return $this->addFunctionFilter(self::BOOLEAN_OR, 'contains', $field, $value);
    }

    public function orderBy(string $field, string $direction = 'asc'): self
    {
        $direction = strtolower($direction);

        if (! in_array($direction, ['asc', 'desc'], true)) {
            throw new InvalidArgumentException('The order by direction must be asc or desc.');
        }

        $this->orderBys[] = sprintf('%s %s', $this->formatField($field), $direction);

        return $this;
    }

    public function select(string ...$fields): self
    {
        $this->selectedFields = array_values(array_filter(array_map(
            fn (string $field): string => $this->formatField($field),
            $this->parseSelectFields($fields),
        )));

        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    public function withCount(bool $withCount = true): self
    {
        $this->withCount = $withCount;

        return $this;
    }

    public function get(): array
    {
        return $this->resource->fetchRecords($this->toQueryOptions());
    }

    public function count(): int
    {
        return $this->resource->getRecordCount($this->toQueryOptions());
    }

    public function first(): ?array
    {
        $results = (clone $this)
            ->limit(1)
            ->get();

        return $results[0] ?? null;
    }

    public function toQueryOptions(): QueryOptions
    {
        return new QueryOptions(
            filter: $this->compileFilter(),
            sort: $this->orderBys !== [] ? implode(', ', $this->orderBys) : null,
            limit: $this->limit,
            offset: $this->offset,
            withCount: $this->withCount,
            select: $this->selectedFields !== [] ? implode(',', $this->selectedFields) : null,
        );
    }

    private function addRawFilter(string $boolean, string $expression): self
    {
        $this->filters[] = [
            'boolean' => $boolean,
            'expression' => $expression,
        ];

        return $this;
    }

    private function addWhere(string $boolean, string $field, mixed $operator, mixed $value): self
    {
        [$resolvedOperator, $resolvedValue] = $this->normalizeWhereArguments($operator, $value);

        return $this->addRawFilter($boolean, sprintf(
            '%s %s %s',
            $this->formatField($field),
            $resolvedOperator,
            $this->formatValue($resolvedValue),
        ));
    }

    private function addNestedWhere(string $boolean, callable $callback): self
    {
        $nestedQuery = new self($this->resource);

        $callback($nestedQuery);

        $expression = $nestedQuery->compileFilter();

        if ($expression === null) {
            return $this;
        }

        return $this->addRawFilter($boolean, sprintf('(%s)', $expression));
    }

    private function addFunctionFilter(string $boolean, string $function, string $field, string $value): self
    {
        return $this->addRawFilter($boolean, sprintf(
            '%s(%s,%s)',
            $function,
            $this->formatField($field),
            $this->formatValue($value),
        ));
    }

    /**
     * @return array{string, mixed}
     */
    private function normalizeWhereArguments(mixed $operator, mixed $value): array
    {
        if ($value === null && ! $this->isSupportedOperator($operator)) {
            return ['eq', $operator];
        }

        $operator = $this->mapOperator((string) $operator);

        return [$operator, $value];
    }

    private function isSupportedOperator(mixed $operator): bool
    {
        return is_string($operator) && in_array(strtolower($operator), self::SUPPORTED_OPERATORS, true);
    }

    private function mapOperator(string $operator): string
    {
        return match (strtolower($operator)) {
            '=', '==' => 'eq',
            '!=', '<>' => 'ne',
            '>' => 'gt',
            '>=' => 'ge',
            '<' => 'lt',
            '<=' => 'le',
            'eq', 'ne', 'gt', 'ge', 'lt', 'le' => strtolower($operator),
            default => throw new InvalidArgumentException("Unsupported operator [{$operator}]."),
        };
    }

    private function compileFilter(): ?string
    {
        if ($this->filters === []) {
            return null;
        }

        $expressions = array_map(
            fn (array $filter): string => $filter['expression'],
            $this->filters,
        );

        $compiled = [array_shift($expressions)];

        foreach ($expressions as $index => $expression) {
            $compiled[] = $this->filters[$index + 1]['boolean'];
            $compiled[] = $expression;
        }

        return implode(' ', $compiled);
    }

    /**
     * @param  list<string>  $fields
     * @return list<string>
     */
    private function parseSelectFields(array $fields): array
    {
        return array_merge(...array_map(
            fn (string $field): array => array_values(array_filter(array_map('trim', explode(',', $field)))),
            $fields,
        ));
    }

    private function formatField(string $field): string
    {
        $segments = array_map(function (string $segment): string {
            $segment = trim($segment);

            if ($segment === '*' || $segment === '') {
                return $segment;
            }

            if (preg_match('/[\s_]/', $segment) === 1) {
                return '"'.$segment.'"';
            }

            return $segment;
        }, explode('/', $field));

        return implode('/', $segments);
    }

    private function formatValue(mixed $value): string
    {
        return match (true) {
            $value instanceof DateTimeInterface => $value->format(DateTimeInterface::ATOM),
            is_string($value) => "'".str_replace("'", "''", $value)."'",
            is_bool($value) => $value ? 'true' : 'false',
            $value === null => 'null',
            default => (string) $value,
        };
    }
}
