<?php

declare(strict_types=1);

namespace Soluble\Jasper\Exception;

use Soluble\Japha\Bridge\Exception\JavaException;

class JavaProxiedException extends \RuntimeException implements ExceptionInterface
{
    /**
     * @var JavaException
     */
    protected $javaException;

    public function __construct(JavaException $javaException)
    {
        $this->javaException = $javaException;
        parent::__construct($javaException->getMessage(), $javaException->getCode());
    }

    /**
     * Return java exception as return by the bridge server.
     *
     * @return JavaException
     */
    public function getJavaException(): JavaException
    {
        return $this->javaException;
    }
}
