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

$miniReport = new Report(
    __DIR__ . '/../reports/00_report_test_mini.jrxml',
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

$imgMiniReport = new Report(
    __DIR__ . '/../reports/01_report_test_default.jrxml',
    new ReportParams([
        'BookTitle'    => 'Soluble Jasper',
        'BookSubTitle' => 'Generated on JVM with Jasper reports'
    ])
);

echo "\n### Internal usage based on a very simple report\n\n";

$bm->printTableHeader();

$bm->time(
    basename($miniReport->getReportFile()) . ' (compile)',
    function ($iterations) use ($reportRunner, $miniReport) {
        for ($i = 0; $i < $iterations; ++$i) {
            $reportRunner->compileReport($miniReport);
        }
    }
);

$compiledReport = $reportRunner->compileReport($miniReport);
$bm->time(
    basename($miniReport->getReportFile()) . ' (fill)',
    function ($iterations) use ($reportRunner, $compiledReport, $miniReport) {
        for ($i = 0; $i < $iterations; ++$i) {
            $reportRunner->fillReport($compiledReport, $miniReport->getReportParams());
        }
    }
);

echo "\n\n### PDF exports\n\n";

$bm->printTableHeader();

$reportPath = __DIR__ . '/../reports';
$reports = [
    'text-only' => new Report(
        "$reportPath/00_report_test_mini.jrxml",
            new ReportParams([
                'BookTitle'    => 'Soluble Jasper',
                'BookSubTitle' => 'Generated on JVM with Jasper reports'
            ])
        ),
    'text + png' => new Report("$reportPath/01_report_test_default.jrxml"),
    'barcodes'   => new Report("$reportPath/06_report_test_barcodes.jrxml"),
];

$mysql_password = $_SERVER['argv'][1] ?? '';

if ($mysql_password !== '') {
    $reports['jdbc'] = new Report(
        "$reportPath/08_report_test_jdbc.jrxml",
        null,
        new JDBCDataSource("jdbc:mysql://localhost/phpunit_soluble_test_db?user=root&password=$mysql_password&serverTimezone=UTC")
    );
}

$idx = 0;
foreach ($reports as $key => $report) {
    $exportManager = $reportRunner->getExportManager($report);
    $bm->time(
        basename($report->getReportFile()) . " ($key)",
        function ($iterations) use ($exportManager, $idx) {
            for ($i = 0; $i < $iterations; ++$i) {
                $exportManager->savePdf("/tmp/my_report_output_{$idx}_{$i}.pdf");
            }
        }
    );
    ++$idx;
}

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

    public function printTableHeader()
    {
        echo '| Benchmark name | ' . implode('|', array_map(function ($iter) {
            return " x$iter ";
        }, $this->iterations)) . '| Average | Memory |' . PHP_EOL;
        echo '|----| ' . implode('|', array_map(function ($iter) {
            return '----:';
        }, $this->iterations)) . '|-------:|----:| ' . PHP_EOL;
    }

    /**
     * @param string   $name
     * @param callable $fn
     */
    public function time($name, callable $fn)
    {
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
