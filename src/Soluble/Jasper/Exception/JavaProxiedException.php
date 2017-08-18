<?php

declare(strict_types=1);

namespace Soluble\Jasper\Exception;

use Soluble\Japha\Bridge\Exception\JavaException;

class JavaProxiedException extends RuntimeException implements JavaProxiedExceptionInterface
{
    /**
     * @var JavaException
     */
    protected $javaException;

    public function __construct(JavaException $javaException, ?string $msg = null, ?int $code = null)
    {
        $this->javaException = $javaException;
        $message = sprintf(
            '%s[%s]: %s (%s)',
            $msg !== null ? "$msg. " : '',
            $javaException->getJavaClassName(),
            $javaException->getMessage(),
            $javaException->getCause()
        );
        parent::__construct($message, $code ?? $javaException->getCode());
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
