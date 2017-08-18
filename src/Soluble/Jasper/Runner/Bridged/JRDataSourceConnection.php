<?php

declare(strict_types=1);

namespace Soluble\Jasper\Runner\Bridged;

use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\Runner\Bridged\Proxy\JRDataSourceInterface;

class JRDataSourceConnection implements JRDataSourceInterface
{
    /**
     * @var JavaObject Java('java.sql.Connection')
     */
    protected $connection;

    public function __construct(JavaObject $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return JavaObject Java('java.sql.Connection')
     */
    public function getJavaProxiedObject(): JavaObject
    {
        return $this->connection;
    }
}
