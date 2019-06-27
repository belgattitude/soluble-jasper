### Creating a new report

```php
<?php declare(strict_types=1);

use Soluble\Japha\Bridge\Adapter as JavaBridgeAdapter;
use Soluble\Jasper\{ReportRunnerFactory, Report, ReportParams};
use Soluble\Jasper\DataSource\JavaSqlConnection;
use Soluble\Jasper\Exporter\PDFExporter;


// Step 1: Get the report runner
// Good practice is to initialize once and get it from a PSR-11 compatible container

$bridgeAdapter = new JavaBridgeAdapter([
    'servlet_address' => 'localhost:8080/JasperReports/servlet.phpjavabridge'    
]);

$reportRunner = ReportRunnerFactory::getBridgedReportRunner($bridgeAdapter);

// Step 2: Define your report parameters

$report = new Report(
     '/path/my_report.jrxml',
     new ReportParams([
            'BookTitle'    => 'Soluble Jasper',
            'BookSubTitle' => 'Generated on JVM with Jasper reports'
     ]),
     new JavaSqlConnection(
         'jdbc:mysql://localhost/my_db?user=user&password=password',
         'com.mysql.jdbc.Driver'
     )
);

// Step 3: Export the report

$pdfExporter = new PDFExporter($report, $reportRunner);

$pdfExporter->saveFile('/path/my_report_output.pdf', [
    'author' => 'John Doe',
    'title' => 'My document'
]);

// Or for PSR7 response

$response = $pdfExporter->getPsr7Response([
    'author' => 'John Doe',
    'title' => 'My document'    
]);

//$exportManager = $reportRunner->getExportManager($report);
//$exportManager->savePdf('/path/my_report_output.pdf');


/*
$pdfExporter = $exportManager->getPdfExporter();
$pdfExporter->saveFile('/path/my_report_output.pdf');

// Both will need to cache the report 
$psr7Response = $pdfExporter->getPsr7Response();
$stream       = $pdfExporter->getStream();
*/

```
