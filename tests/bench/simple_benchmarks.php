<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\ReportRunnerFactory;
use Soluble\Jasper\Report;
use Soluble\Jasper\ReportParams;
use Soluble\Jasper\DataSource\JDBCDataSource;

ini_set('display_errors', 'true');

$bm = new Benchmark();

$start_total_time = $bm->getTimeMs();

echo '<pre>' . PHP_EOL;

// BENCHING CONNECTION
$start_connection_time = $bm->getTimeMs();
try {
    $ba = new BridgeAdapter([
        'driver'             => 'Pjb62',
        'servlet_address'    => 'http://127.0.0.1:8080/JasperReports/servlet.phpjavabridge',
        'java_prefer_values' => true
    ]);
    $init = $ba->java('java.lang.String');
} catch (\Exception $e) {
    die('Error connecting: ' . $e->getMessage());
}
$end_connection_time = $bm->getTimeMs();
$connection_time = $bm->getFormattedTimeMs($start_connection_time, $end_connection_time);
// END OF BENCHING CONNECTION

$reportRunner = ReportRunnerFactory::getBridgedJasperReportRunner($ba);

$report = new Report(
    __DIR__ . '/../reports/01_report_test_default.jrxml',
    new ReportParams([
        'BookTitle'    => 'Soluble Jasper',
        'BookSubTitle' => 'Generated on JVM with Jasper reports'
    ])
    /*
    ,new JDBCDataSource(
        'jdbc:mysql://localhost/$db?user=user&password=password&serverTimezone=UTC',
        'com.mysql.jdbc.Driver'
    )*/
);

$bm->time(
    basename($report->getReportFile()) . ' (compile)',
    function ($iterations) use ($reportRunner, $report) {
        for ($i = 0; $i < $iterations; ++$i) {
            $reportRunner->compileReport($report);
        }
    }
);

$compiledReport = $reportRunner->compileReport($report);
$bm->time(
    basename($report->getReportFile()) . ' (fill)',
    function ($iterations) use ($reportRunner, $compiledReport, $report) {
        for ($i = 0; $i < $iterations; ++$i) {
            $reportRunner->fillReport($compiledReport, $report->getReportParams());
        }
    }
);

$exportManager = $reportRunner->getExportManager($report);
$bm->time(
    basename($report->getReportFile()) . ' (savePDF)',
    function ($iterations) use ($exportManager) {
        for ($i = 0; $i < $iterations; ++$i) {
            $exportManager->savePdf('/tmp/my_report_output.pdf');
        }
    }
);

$end_total_time = $bm->getTimeMs();
$total_time = $bm->getFormattedTimeMs($start_total_time, $end_total_time);

echo PHP_EOL;
echo '- Connection time: ' . $connection_time . PHP_EOL;
echo '- Total time     : ' . $total_time . PHP_EOL;
echo PHP_EOL;

class Benchmark
{
    /**
     * @var bool
     */
    public $tableHeaderPrinted = false;

    /**
     * @var array
     */
    public $iterations = [1, 5, 10];

    public function __construct()
    {
    }

    /**
     * @param string   $name
     * @param callable $fn
     */
    public function time($name, callable $fn)
    {
        if (!$this->tableHeaderPrinted) {
            echo '| Benchmark name | ' . implode('|', array_map(function ($iter) {
                return " x$iter ";
            }, $this->iterations)) . '| Average | Memory |' . PHP_EOL;
            echo '|----| ' . implode('|', array_map(function ($iter) {
                return '----:';
            }, $this->iterations)) . '|-------:|----:| ' . PHP_EOL;
            $this->tableHeaderPrinted = true;
        }

        $times = [];

        $start_memory = memory_get_usage(false);

        foreach ($this->iterations as $iteration) {
            $start_time = microtime(true);
            $fn($iteration);
            $total_time = microtime(true) - $start_time;
            $times[$iteration] = $total_time;
        }

        $memory = memory_get_usage(false) - $start_memory;

        $avg = array_sum($times) / array_sum(array_keys($times));

        /*
        $ttime = array_sum($times);
        echo number_format($ttime * 1000, 2);
        */
        echo  "| $name | " . implode('| ', array_map(function ($time) {
            return number_format($time * 1000, 2) . 'ms';
        }, $times)) . '| ' .
            number_format($avg * 1000, 2) . 'ms| ' .
            round($memory / 1024, 2) . 'Kb' . '|' . PHP_EOL;
    }

    /**
     * Return formatted time .
     *
     * @param int $start_time
     * @param int $end_time
     */
    public function getFormattedTimeMs($start_time, $end_time)
    {
        $time = $end_time - $start_time;

        return number_format($time, 0, '.', '') . ' ms';
    }

    /**
     * Get ms time (only 64bits platform).
     *
     * @return int
     */
    public function getTimeMs()
    {
        $mt = explode(' ', microtime());

        return ((int) $mt[1]) * 1000 + ((int) round($mt[0] * 1000));
    }
}
