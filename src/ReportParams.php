<?php

declare(strict_types=1);

/*
 * Jasper report integration for PHP
 *
 * @link      https://github.com/belgattitude/soluble-jasper
 * @author    Vanvelthem Sébastien
 * @copyright Copyright (c) 2017-2019 Vanvelthem Sébastien
 * @license   MIT
 */

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
     * @throws InvalidArgumentException
     */
    public function __construct(iterable $params = [])
    {
        $this->params = new ArrayObject();

        try {
            foreach ($params as $key => $value) {
                $current_key = $key;
                $this->offsetSet($key, $value);
            }
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException(sprintf(
                'Cannot construct ReportParams from provided $params, all keys must be non-empty strings (key: %s)',
                $current_key ?? ''
            ), $e->getCode(), $e);
        }
    }

    public function addParams(iterable $params): void
    {
        foreach ($params as $key => $value) {
            $this->offsetSet($key, $value);
        }
    }

    public function withMergedParams(self $params): self
    {
        return new self(array_merge($this->toArray(), $params->toArray()));
    }

    /**
     * @param string $param report parameter name ($P{} in jasper)
     * @param mixed  $value
     */
    public function put(string $param, $value): void
    {
        $this->params->offsetSet($param, $value);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator((array) $this->params);
    }

    /**
     * @param mixed $offset
     *
     * @throws \Soluble\Jasper\Exception\InvalidArgumentException
     */
    public function offsetExists($offset): bool
    {
        $this->checkStringOffset($offset);

        return $this->params->offsetExists($offset);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     *
     * @throws \Soluble\Jasper\Exception\InvalidArgumentException
     */
    public function offsetGet($offset)
    {
        $this->checkStringOffset($offset);

        return $this->params->offsetGet($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     *
     * @throws \Soluble\Jasper\Exception\InvalidArgumentException
     */
    public function offsetSet($offset, $value): void
    {
        $this->checkStringOffset($offset);
        $this->params->offsetSet($offset, $value);
    }

    /**
     * @param mixed $offset
     *
     * @throws \Soluble\Jasper\Exception\InvalidArgumentException
     */
    public function offsetUnset($offset): void
    {
        $this->checkStringOffset($offset);
        if ($this->offsetExists($offset)) {
            $this->params->offsetUnset($offset);
        }
    }

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        return (array) $this->params;
    }

    /**
     * @param string|mixed $offset
     *
     * @throws Exception\InvalidArgumentException
     */
    private function checkStringOffset($offset): void
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
