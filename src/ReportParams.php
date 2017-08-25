<?php

declare(strict_types=1);

namespace Soluble\Jasper;

use ArrayIterator;
use ArrayObject;
use Soluble\Jasper\Exception\InvalidArgumentException;

class ReportParams implements \ArrayAccess, \IteratorAggregate
{
    /**
     * @var ArrayObject
     */
    private $params;

    /**
     * ReportParams constructor.
     *
     * @param iterable $params Report parameters as array or any traversable type (IteratorAggregate, Iterator, ArrayObject...)
     *
     * @throws \Soluble\Jasper\Exception\InvalidArgumentException
     */
    public function __construct(iterable $params = [])
    {
        $this->params = new ArrayObject();
        $current_key = '';
        try {
            foreach ($params as $key => $value) {
                $current_key = $key;
                $this->offsetSet($key, $value);
            }
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException(sprintf(
                'Cannot construct ReportParams from provided $params, all keys must be non-empty strings (key: %s)',
                $current_key
            ));
        }
    }

    public function withMergedParams(ReportParams $params): ReportParams
    {
        $newParams = array_merge($this->toArray(), $params->toArray());

        return new self($newParams);
    }

    /**
     * @param string $param report parameter name ($P{} in jasper)
     * @param mixed  $value
     */
    public function put(string $param, $value): void
    {
        $this->params->offsetSet($param, $value);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->params);
    }

    /**
     * @throws \Soluble\Jasper\Exception\InvalidArgumentException
     */
    public function offsetExists($offset): bool
    {
        $this->checkStringOffset($offset);

        return $this->params->offsetExists($offset);
    }

    /**
     * @throws \Soluble\Jasper\Exception\InvalidArgumentException
     */
    public function offsetGet($offset)
    {
        $this->checkStringOffset($offset);

        return $this->params->offsetGet($offset);
    }

    /**
     * @throws \Soluble\Jasper\Exception\InvalidArgumentException
     */
    public function offsetSet($offset, $value): void
    {
        $this->checkStringOffset($offset);
        $this->params->offsetSet($offset, $value);
    }

    /**
     * @throws \Soluble\Jasper\Exception\InvalidArgumentException
     */
    public function offsetUnset($offset): void
    {
        $this->checkStringOffset($offset);
        if ($this->offsetExists($offset)) {
            $this->params->offsetUnset($offset);
        }
    }

    public function toArray(): array
    {
        return (array) $this->params;
    }

    /**
     * @param mixed $offset
     *
     * @throws \Soluble\Jasper\Exception\InvalidArgumentException
     */
    protected function checkStringOffset($offset): void
    {
        if (!is_string($offset)) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    'Report parameters must be a string (type: %s%s)',
                    gettype($offset),
                    is_scalar($offset) ? ', value:' . (string) $offset : ''
                )
            );
        } elseif (trim($offset) === '') {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    'Report parameters must be a non-empty string.'
                )
            );
        }
    }
}
