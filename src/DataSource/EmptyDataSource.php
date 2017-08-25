<?php

declare(strict_types=1);

namespace Soluble\Jasper\DataSource;

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\DataSource\Contract\JRDataSourceFromDataSourceInterface;
use Soluble\Jasper\Proxy\Engine\JREmptyDataSource;

class EmptyDataSource implements JRDataSourceFromDataSourceInterface
{
    /**
     * @return JavaObject Java('net.sf.jasperreports.engine.JREmptyDataSource')
     */
    public function getJRDataSource(BridgeAdapter $bridgeAdapter): JavaObject
    {
        return (new JREmptyDataSource($bridgeAdapter))->getJavaProxiedObject();
    }
}
