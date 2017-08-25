# soluble-jasper  

[![PHP Version](http://img.shields.io/badge/php-7.1+-ff69b4.svg)](https://packagist.org/packages/soluble/jasper)
[![Build Status](https://travis-ci.org/belgattitude/soluble-jasper.svg?branch=master)](https://travis-ci.org/belgattitude/soluble-jasper)
[![codecov](https://codecov.io/gh/belgattitude/soluble-jasper/branch/master/graph/badge.svg)](https://codecov.io/gh/belgattitude/soluble-jasper)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/belgattitude/soluble-jasper/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/belgattitude/soluble-jasper/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/soluble/jasper/v/stable.svg)](https://packagist.org/packages/soluble/jasper)
[![Total Downloads](https://poser.pugx.org/soluble/jasper/downloads.png)](https://packagist.org/packages/soluble/jasper)
[![License](https://poser.pugx.org/soluble/jasper/license.png)](https://packagist.org/packages/soluble/jasper)

Report generation using jasper reports from PHP.  

> EARLY ALPHA WORK !!! DO NOT USE YET

## Features


## Requirements

- PHP 7.1+
- PHPJasperBridge (see install)
- Java

## Dependencies

- [soluble-japha](https://github.com/belgattitude/soluble-japha) client for communication with the jasper bridge

## Examples

### Creating a new report

```php
<?php declare(strict_types=1);

use Soluble\Japha\Bridge\Adapter as JavaBridgeAdapter;
use Soluble\Jasper\{ReportRunnerFactory, Report, ReportParams};
use Soluble\Jasper\DataSource\JavaSqlConnection;

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

// Step 3: Get the export manager and choose exports

$exportManager = $reportRunner->getExportManager($report);
$exportManager->savePdf('/path/my_report_output.pdf');


/*
$pdfExporter = $exportManager->getPdfExporter();
$pdfExporter->saveFile('/path/my_report_output.pdf');

// Both will need to cache the report 
$psr7Response = $pdfExporter->getPsr7Response();
$stream       = $pdfExporter->getStream();
*/

```


## Datasources


Jasper reports supports multiple datasources for filling the report (see [JRApi](http://jasperreports.sourceforge.net/api/net/sf/jasperreports/engine/JasperFillManager.html))

### JavaSqlConnection

Example using `JavaSlConnection`:

```php
<?php declare(strict_types=1);

use Soluble\Jasper\DataSource\JavaSqlConnection;

$dataSource = new JavaSqlConnection(
     'jdbc:mysql://server_host/database?user=user&password=password',
     'com.mysql.jdbc.Driver'
);
```

!!! tip
    For convenience you can also use the `JdbcDsnFactory` to convert 
    database params. 

    ```php
    <?php declare(strict_types=1);
    
    use Soluble\Jasper\DataSource\Util\JdbcDsnFactory;
    
    $dbParams = [
        'driver'    => 'mysql', // JDBC driver key.
        'host'      => 'localhost',
        'db'        => 'my_db',
        'user'      => 'user',
        'password'  => 'password',
        // Optional extended options
        'driverOptions'  => [
            'serverTimezone' => 'UTC'
        ]        
    ];
    
    $dsn = JdbcDsnFactory::createDsnFromParams($dbParams);
    
    // You should get a jdbc formatted dsn:
    //   'jdbc:mysql://localhost/my_db?user=user&password=password&serverTimezone=UTC'
    // ready to use as $dsn argument for `JdbcDataSource`
    ```

### JsonDataSource

Example using `JsonDataSource`:

```php
<?php declare(strict_types=1);

use Soluble\Jasper\{ReportRunnerFactory, Report, ReportParams};
use Soluble\Jasper\DataSource\JsonDataSource;
 
$jsonDataSource = new JsonDataSource('/path/northwind.json');
$jsonDataSource->setOptions([
    JsonDataSource::PARAM_JSON_DATE_PATTERN   => 'yyyy-MM-dd',
    JsonDataSource::PARAM_JSON_NUMBER_PATTERN => '#,##0.##',
    JsonDataSource::PARAM_JSON_TIMEZONE_ID    => 'Europe/Brussels',
    JsonDataSource::PARAM_JSON_LOCALE_CODE    => 'en_US'
]);


$report = new Report(
                '/path/myreport.jrxml',
                new ReportParams([
                    'LOGO_FILE' => '/path/assets/wave.png',
                    'TITLE'     => 'My Title'            
                ]),  
                $jsonDataSource);

$reportRunner = ReportRunnerFactory::getBridgedReportRunner($this->ba);
$exportManager = $reportRunner->getExportManager($report);

$exportManager->savePdf($output_pdf);

```

## Exceptions

When running or exporting a report, the following exception can be thrown: 

Generally at compile time:

| Exception                       | Description                                                              |                    
|---------------------------------|--------------------------------------------------------------------------|
| `ReportFileNotFoundException`   | When the report file cannot be opened (PHP or Java side, check perms)    |
| `BrokenXMLReportFileException`  | When the report JRXML file cannot be parsed (xml error)                  |
| `ReportCompileException`        | Compilation error, generally an invalid expression or missing resource   |
| `JavaProxiedException`          | Exception on the Java side, and call `$e->getJvmStackTrace()` for debug  |  
| `RuntimeException`              | Normally never thrown, see exception message                             |


## Installation

### JasperBridge

Build a war file

```shell
# Example based on php-java-bridge master
$ git clone https://github.com/belgattitude/php-java-bridge.git
$ cd php-java-bridge
$ ./gradlew war -I init-scripts/init.jasperreports.gradle -I init-scripts/init.mysql.gradle 
```

Deploy on Tomcat (example on ubuntu)

```shell
$ sudo cp ./build/libs/JavaBridgeTemplate.war /var/lib/tomcat8/webapps/JasperReports.war
```

Wait few seconds and point your browser to [http://localhost:8080/JasperReports](http://localhost:8080/JasperReports), you
should see the php-java-bridge dashboard page.

![](./docs/images/jasper_bridge_dashboard.png "Jasper bridge dashboard")

The bridge address can be used in the japha bridge adapter:

```php
<?php declare(strict_types=1);

use Soluble\Japha\Bridge\Adapter;

$ba = new Adapter([
    'driver' => 'Pjb62',
    'servlet_address' => 'localhost:8080/JasperReports/servlet.phpjavabridge'    
]);

// This should print your JVM version
echo $ba->javaClass('java.lang.System')->getProperty('java.version');

```

## Benchmarks

Early benchmarks for common operation (run on a laptop for now, will do soon on digitalocean). See `tests/bench/simple_benchmarks.php`.

### Jasper compile time and filling (internal)

| Benchmark name |  x1 | x5 | x10 | Average | Memory |
|----| ----:|----:|----:|-------:|----:| 
| 00_report_test_mini.jrxml (compile) | 47.87ms| 176.62ms| 344.27ms| 35.55ms| 37.59Kb|
| 00_report_test_mini.jrxml (fill) | 3.77ms| 8.47ms| 12.81ms| 1.57ms| 28.92Kb|
| 01_report_test_default.jrxml (compile) | 40.17ms| 173.92ms| 347.49ms| 35.10ms| 0.31Kb|
| 01_report_test_default.jrxml (fill) | 3.32ms| 12.82ms| 19.77ms| 2.24ms| 0.42Kb|


### PDF exports

| Benchmark name |  x1 | x5 | x10 | Average | Memory |
|----| ----:|----:|----:|-------:|----:| 
| 00_report_test_mini.jrxml (text-only) | 51.82ms| 3.21ms| 6.04ms| 3.82ms| 0.79Kb|
| 01_report_test_default.jrxml (text+png) | 373.95ms| 1,628.55ms| 3,200.64ms| 325.20ms| 0.75Kb|
| 06_report_test_barcodes.jrxml (barcodes) | 88.93ms| 324.90ms| 689.11ms| 68.93ms| 0.75Kb|
| 08_report_test_jdbc.jrxml (jdbc) | 32.03ms| 35.00ms| 65.28ms| 8.27ms| 17.31Kb|

- Connection time: 6 ms
- Total time     : 7773 ms

  
## Coding standards and interop

* [PSR 4 Autoloader](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)
* [PSR 3 Logger interface](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)
* [PSR 2 Coding Style Guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)

