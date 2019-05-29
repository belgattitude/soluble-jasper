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

require_once __DIR__ . '/../../vendor/autoload.php';

use JasperTest\Util\MarkdownBenchmark;
use Soluble\Japha\Bridge\Adapter as BridgeAdapter;
use Soluble\Jasper\DataSource\JavaSqlConnection;
use Soluble\Jasper\Report;
use Soluble\Jasper\ReportParams;
use Soluble\Jasper\ReportRunnerFactory;

ini_set('display_errors', 'true');

$reportPath = __DIR__ . '/../reports';
$reports    = [
    'text-only' => new Report(
        "$reportPath/00_report_mini.jrxml",
        new ReportParams([
            'BookTitle'    => 'Soluble Jasper',
            'BookSubTitle' => 'Generated on JVM with Jasper reports'
        ])
    ),
    'text+png'       => new Report("$reportPath/01_report_default.jrxml"),
    'text+jpg'       => new Report("$reportPath/12_report_jpg.jrxml"),
    //'text+png+cache' => new Report("$reportPath/05_report_img_cache.jrxml"),
    'barcodes'       => new Report("$reportPath/06_report_barcodes.jrxml"),
];

$mysql_password = $_SERVER['argv'][1] ?? '';

if ($mysql_password !== '') {
    $reports['jdbc'] = new Report(
        "$reportPath/08_report_jdbc.jrxml",
        null,
        new JavaSqlConnection(
            "jdbc:mysql://localhost/phpunit_soluble_test_db?user=root&password=$mysql_password&serverTimezone=UTC",
            'com.mysql.jdbc.Driver'
        )
    );
}

$bm = new MarkdownBenchmark();

//#####################
// Benching connection
//#####################
$start_total_time = $bm->getTimeMs();

echo '<pre>' . PHP_EOL;

// BENCHING CONNECTION
$start_connection_time = $bm->getTimeMs();

try {
    $ba = new BridgeAdapter([
        'driver'             => 'Pjb62',
        'servlet_address'    => 'http://127.0.0.1:8080/JasperReports/servlet.phpjavabridge',
        //'java_prefer_values' => true
    ]);
    $init = $ba->java('java.lang.String');
} catch (\Exception $e) {
    die('Error connecting: ' . $e->getMessage());
}
$end_connection_time = $bm->getTimeMs();
$connection_time     = $bm->getFormattedTimeMs($start_connection_time, $end_connection_time);
// END OF BENCHING CONNECTION

$reportRunner = ReportRunnerFactory::getBridgedReportRunner($ba);

$miniReport    = $reports['text-only'];
$imgMiniReport = $reports['text+png'];

//#####################
// Benching connection
//#####################

echo "\n### Jasper compile time and filling (internal)\n\n";

$bm->printTableHeader();

foreach ([$reports['text-only'], $reports['text+png']] as $key => $report) {
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
        function ($iterations) use ($reportRunner, $compiledReport, $miniReport) {
            for ($i = 0; $i < $iterations; ++$i) {
                $reportRunner->fillReport($compiledReport, $miniReport->getReportParams());
            }
        }
    );
}

//#####################################
// Benching report generation in PDF
//######################################
echo "\n\n### PDF exports\n\n";

$bm->printTableHeader();

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
$total_time     = $bm->getFormattedTimeMs($start_total_time, $end_total_time);

echo PHP_EOL;
echo '- Connection time: ' . $connection_time . PHP_EOL;
echo '- Total time     : ' . $total_time . PHP_EOL;
echo PHP_EOL;
