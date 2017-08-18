<?php

declare(strict_types=1);

namespace Soluble\Jasper;

use ArrayObject;

class ReportParams implements \ArrayAccess
{
    protected $params;

    public function __construct()
    {
        $this->params = new ArrayObject();
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
                    'Report parameters must be a string (type: %s)',
                    gettype($offset)
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
