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

Example using `JavaSqlConnection`:

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

$exportManager->savePdf('/path/my_output.pdf');

```

## Logging

You can enable any `psr/log` compatible logger. Here's a basic example with [monolog](https://github.com/Seldaek/monolog):

```php
<?php

use Soluble\Japha\Bridge\Adapter as JavaBridgeAdapter;
use Soluble\Jasper\{ReportRunnerFactory, Report, ReportParams};
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$logger = new Logger('soluble-japha-logger');
$logger->pushHandler(new StreamHandler('path/to/your.log', Logger::WARNING));

$bridgeAdapter = new JavaBridgeAdapter([
    'servlet_address' => 'localhost:8080/JasperReports/servlet.phpjavabridge'    
]);

$reportRunner = ReportRunnerFactory::getBridgedReportRunner($bridgeAdapter, $logger);

$report = new Report('/path/my_report.jrxml', new ReportParams());

// Any exception during report compilation, filling or exporting will
// be logged ;)

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


At filling time:

| Exception                       | Description                                                              |                    
|---------------------------------|--------------------------------------------------------------------------|
| `BrokenJsonDataSourceException` | When the json datasource cannot be parsed                                |
| `JavaProxiedException`          | Exception on the Java side, and call `$e->getJvmStackTrace()` for debug  |  



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
| 00_report_mini.jrxml (compile) | 43.03ms| 179.05ms| 347.55ms| 35.60ms| 18.97Kb|
| 00_report_mini.jrxml (fill) | 3.19ms| 9.15ms| 18.58ms| 1.93ms| 14.27Kb|
| 01_report_default.jrxml (compile) | 39.24ms| 192.41ms| 338.65ms| 35.64ms| 0.31Kb|
| 01_report_default.jrxml (fill) | 3.70ms| 11.22ms| 22.75ms| 2.35ms| 0.44Kb|


### PDF exports

| Benchmark name |  x1 | x5 | x10 | Average | Memory |
|----| ----:|----:|----:|-------:|----:| 
| 00_report_mini.jrxml (text-only) | 38.74ms| 3.76ms| 8.58ms| 3.19ms| 0.79Kb|
| 01_report_default.jrxml (text+png) | 318.68ms| 1,365.02ms| 2,709.56ms| 274.58ms| 0.75Kb|
| 12_report_jpg.jrxml (text+jpg) | 35.17ms| 6.75ms| 8.89ms| 3.18ms| 0.75Kb|
| 06_report_barcodes.jrxml (barcodes) | 123.81ms| 323.71ms| 630.51ms| 67.38ms| 0.75Kb|

- Connection time: 3 ms
- Total time     : 6860 ms

!!! tip
    For best performances: when exporting in PDF, *PNG images* in PDF are much slower than equivalent *JPG*.
    
     
     

  
## Coding standards and interop

* [PSR 4 Autoloader](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)
* [PSR 3 Logger interface](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)
* [PSR 2 Coding Style Guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)

