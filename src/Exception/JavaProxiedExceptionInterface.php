<?php

declare(strict_types=1);

namespace Soluble\Jasper\Exception;

use Soluble\Japha\Bridge\Exception\JavaException;

interface JavaProxiedExceptionInterface
{
    /**
     * Return java exception as return by the bridge server.
     *
     * @return JavaException
     */
    public function getJavaException(): JavaException;
}
