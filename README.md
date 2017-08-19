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
- soluble-jasper client

## Use cases 

## Basics

### Creating a new report

```php
<?php declare(strict_types=1);

use Soluble\Jasper\ReportRunnerFactory;
use Soluble\Jasper\Report;
use Soluble\Jasper\ReportParams;
use Soluble\Jasper\DataSource\JDBCDataSource;


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

## Benchmarks

Early benchmarks for common operation (run on a laptop for now). See `tests/bench/simple_benchmarks.php`.


### Internal usage based on a very simple report

| Benchmark name |  x1 | x5 | x10 | Average | Memory |
|----| ----:|----:|----:|-------:|----:| 
| 00_report_test_mini.jrxml (compile) | 55.73ms| 198.15ms| 411.82ms| 41.61ms| 37.59Kb|
| 00_report_test_mini.jrxml (fill) | 6.34ms| 17.09ms| 29.26ms| 3.29ms| 29.29Kb|


### PDF exports

| Benchmark name |  x1 | x5 | x10 | Average | Memory |
|----| ----:|----:|----:|-------:|----:| 
| 00_report_test_mini.jrxml (text-only) | 52.45ms| 8.38ms| 16.72ms| 4.85ms| 0.79Kb|
| 01_report_test_default.jrxml (text + png) | 381.98ms| 1,635.86ms| 3,296.36ms| 332.14ms| 0.75Kb|
| 06_report_test_barcodes.jrxml (barcodes) | 103.01ms| 314.64ms| 710.39ms| 70.50ms| 0.75Kb|


  
## Coding standards and interop

* [PSR 4 Autoloader](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)
* [PSR 3 Logger interface](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)
* [PSR 2 Coding Style Guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
* [PSR 1 Coding Standards](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)

