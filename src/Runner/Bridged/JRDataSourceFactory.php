<?php

declare(strict_types=1);

namespace Soluble\Jasper\Runner\Bridged;

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Japha\Db\DriverManager;
use Soluble\Japha\Interfaces\JavaObject;
use Soluble\Jasper\DataSource;
use Soluble\Jasper\Exception\UnsupportedDataSourceException;
use Soluble\Jasper\Proxy\Engine\JRDataSourceInterface;
use Soluble\Jasper\Proxy\Engine\JREmptyDataSource;

class JRDataSourceFactory
{
    /**
     * @var BridgeAdapter
     */
    private $ba;

    public function __construct(BridgeAdapter $bridgeAdapter)
    {
        $this->ba = $bridgeAdapter;
    }

    /**
     * @param DataSource\DataSourceInterface $dataSource
     *
     * @return JavaObject
     */
    public function __invoke(DataSource\DataSourceInterface $dataSource): JRDataSourceInterface
    {
        $jrDataSource = null;

        if ($dataSource instanceof DataSource\JavaSqlConnection) {
            $connection = (new DriverManager($this->ba))->createConnection(
                $dataSource->getJdbcDsn(),
                $dataSource->getDriverClass()
            );
            $jrDataSource = new JRDataSourceConnection($connection);
        } elseif ($dataSource instanceof DataSource\EmptyDataSource) {
            $jrDataSource = new JREmptyDataSource($this->ba);
        }

        if ($jrDataSource === null) {
            throw new UnsupportedDataSourceException(sprintf(
                'Unsupported datasource class: "%s"',
                get_class($dataSource)
            ));
        }

        return $jrDataSource;
    }
}
