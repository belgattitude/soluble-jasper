<?php

declare(strict_types=1);

/*
 * Jasper report integration for PHP
 *
 * @link      https://github.com/belgattitude/soluble-jasper
 * @author    Vanvelthem Sébastien
 * @copyright Copyright (c) 2017 Vanvelthem Sébastien
 * @license   MIT
 */

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

    /**
     * Return the JVM exception backtrace.
     */
    public function getJvmStackTrace(): string;
}
