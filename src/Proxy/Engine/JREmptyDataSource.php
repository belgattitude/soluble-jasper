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

namespace Soluble\Jasper\Proxy\Engine;

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Japha\Interfaces\JavaObject;

class JREmptyDataSource implements JRDataSourceInterface
{
    /**
     * @var BridgeAdapter
     */
    private $ba;

    /**
     * @var \Soluble\Japha\Interfaces\JavaObject
     */
    private $jrEmptyDataSource;

    public function __construct(BridgeAdapter $bridgeAdapter)
    {
        $this->ba = $bridgeAdapter;
    }

    /**
     * @return JavaObject Java('net.sf.jasperreports.engine.JREmptyDataSource')
     */
    public function getJavaProxiedObject(): JavaObject
    {
        if ($this->jrEmptyDataSource === null) {
            $this->jrEmptyDataSource = $this->ba->java('net.sf.jasperreports.engine.JREmptyDataSource');
        }

        return $this->jrEmptyDataSource;
    }
}
