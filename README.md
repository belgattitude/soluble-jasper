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

use Soluble\Jasper\ReportRunnerFactory;
use Soluble\Jasper\Report;
use Soluble\Jasper\ReportParams;
use Soluble\Jasper\DataSource\JDBCDataSource;
use Soluble\Japha\Bridge\Adapter;

$bridgeAdapter = new Adapter([
    'driver' => 'Pjb62',
    'servlet_address' => 'localhost:8080/JasperReports/servlet.phpjavabridge'    
]);

$reportRunner = ReportRunnerFactory::getBridgedJasperReportRunner($bridgeAdapter);

$report = new Report(
     '/path/my_report.jrxml',
     new ReportParams([
            'BookTitle'    => 'Soluble Jasper',
            'BookSubTitle' => 'Generated on JVM with Jasper reports'
     ]),
     new JDBCDataSource(
         'jdbc:mysql://localhost/$db?user=user&password=password&serverTimezone=UTC',
         'com.mysql.jdbc.Driver'
     )
);


$exportManager = $reportRunner->getExportManager($report);
$exportManager->savePdf('/path/my_report_output.pdf');

```

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


### Internal usage based on a very simple report

| Benchmark name |  x1 | x5 | x10 | Average | Memory |
|----| ----:|----:|----:|-------:|----:| 
| 00_report_test_mini.jrxml (compile) | 42.48ms| 187.27ms| 353.88ms| 36.48ms| 37.59Kb|
| 00_report_test_mini.jrxml (fill) | 5.61ms| 11.16ms| 19.48ms| 2.27ms| 29.29Kb|


### PDF exports

| Benchmark name |  x1 | x5 | x10 | Average | Memory |
|----| ----:|----:|----:|-------:|----:| 
| 00_report_test_mini.jrxml (text-only) | 42.78ms| 4.61ms| 7.25ms| 3.41ms| 0.79Kb|
| 01_report_test_default.jrxml (text + png) | 378.82ms| 1,657.12ms| 3,429.01ms| 341.56ms| 0.75Kb|
| 06_report_test_barcodes.jrxml (barcodes) | 90.90ms| 313.88ms| 660.88ms| 66.60ms| 0.75Kb|
| 08_report_test_jdbc.jrxml (jdbc) | 39.31ms| 34.53ms| 94.66ms| 10.53ms| 17.31Kb|

  
## Coding standards and interop

* [PSR 4 Autoloader](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)
* [PSR 3 Logger interface](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)
* [PSR 2 Coding Style Guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
* [PSR 1 Coding Standards](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)

