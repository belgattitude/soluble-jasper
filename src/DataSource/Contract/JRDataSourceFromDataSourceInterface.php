<?php

declare(strict_types=1);

namespace Soluble\Jasper\DataSource\Contract;

use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;

interface JRDataSourceFromDataSourceInterface extends DataSourceInterface
{
    public function getJRDataSource(BridgeAdapter $adapter): JavaObject;
}
