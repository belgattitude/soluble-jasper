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

### Benchmarks

Early simple benchmarks for internal operations:



| Benchmark name |  x1 | x5 | x10 | Average | Memory |
|----| ----:|----:|----:|-------:|----:| 
| 01_report_test_default.jrxml (compile) | 31.83ms| 138.85ms| 284.88ms| 28.47ms| 37.59Kb|
| 01_report_test_default.jrxml (fill) | 4.18ms| 11.52ms| 19.74ms| 2.21ms| 29.04Kb|


For full saving a PDF

| Benchmark name |  x1 | x5 | x10 | Average | Memory |
|----| ----:|----:|----:|-------:|----:| 
| 01_report_test_default.jrxml (savePDF) | 339.41ms| 1,555.24ms| 3,009.04ms| 306.48ms| 0.79Kb|

  
## Coding standards and interop

* [PSR 4 Autoloader](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)
* [PSR 3 Logger interface](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)
* [PSR 2 Coding Style Guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
* [PSR 1 Coding Standards](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)

