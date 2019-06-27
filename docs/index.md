
[![PHP Version](http://img.shields.io/badge/php-7.1+-ff69b4.svg)](https://packagist.org/packages/soluble/jasper)
[![Build Status](https://travis-ci.org/belgattitude/soluble-jasper.svg?branch=master)](https://travis-ci.org/belgattitude/soluble-jasper)
[![codecov](https://codecov.io/gh/belgattitude/soluble-jasper/branch/master/graph/badge.svg)](https://codecov.io/gh/belgattitude/soluble-jasper)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/belgattitude/soluble-jasper/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/belgattitude/soluble-jasper/?branch=master)
![PHPStan](https://img.shields.io/badge/style-level%207-brightgreen.svg?style=flat-square&label=phpstan)
[![License](https://poser.pugx.org/soluble/jasper/license.png)](https://packagist.org/packages/soluble/jasper)

> PDF report generation with jasper reports from PHP

### What is Jasperreports ?

JasperReports is a template based opensource reporting library popular amongst Java developers.
It comes with a pretty decent visual designer [Jasperstudio](https://community.jaspersoft.com/project/jaspersoft-studio)
and supports an impressive list of features such as
barcode, fonts, layout, pagination, page size, orientation, columns, precise text spacing / wrapping, charts, subreports, svg, translations...
and allow creation of sophisticated reports. It's opinionated and requires to catch few principles, like report bands, parameters,
datasources, subreports... 

### What's the use case ?

Whenever you need to achieve near pixel-perfect, semi-complex reports layouts that would simply make no
sense to develop with [pure-php](./index.md#alternatives) or puppeteer / headless chrome based approaches. Think of a product catalog, a complex invoice, tracking labels...  

### What is soluble-jasper ?

**soluble-jasper** relies on a [network based bridge](https://docs.soluble.io/soluble-japha/) 
to the JVM and allows to manipulate the report creation from the PHP side. Basically
you just run the jrxml template designed with JasperStudio:

```php
<?php declare(strict_types=1);

use Soluble\Japha\Bridge\Adapter as JavaBridgeAdapter;
use Soluble\Jasper\{ReportRunnerFactory, Report, ReportParams};
use Soluble\Jasper\DataSource\JavaSqlConnection;
use Soluble\Jasper\Exporter\PDFExporter;

// Create a connection to the Jasper bridge.
$reportRunner = ReportRunnerFactory::getBridgedReportRunner(
    new JavaBridgeAdapter([
        'servlet_address' => 'localhost:8080/JasperReports/servlet.phpjavabridge'    
    ])
);

$report = new Report(
    
     // Set the report template file
     '/path/my_report.jrxml',
     
     // Param values that sent to the report template
     // i.e. <textFieldExpression><![CDATA[$P{BookTitle}]]></textFieldExpression> 
     new ReportParams([
            'BookTitle'    => 'Soluble Jasper',
            'BookSubTitle' => 'Generated on JVM with Jasper reports'
     ]),
     
     // Set a database connection if you're using queries in your 
     // report
     new JavaSqlConnection(
         'jdbc:mysql://localhost/my_db?user=user&password=password',
         'com.mysql.jdbc.Driver'
     )
);

// Step 3: Export the report

$pdfExporter = new PDFExporter($report, $reportRunner);
$pdfExporter->saveFile('/path/my_report_output.pdf', [
    // PDF metadata
    'author' => 'John Doe',
    'title' => 'My document'
]);


``` 

This approach have few drawbacks *(the main one is having to install a bridge server running
jasper report on the JVM)* but offers a great level of flexibility and speed *(bridged api, no cli, no startup time)*.
See comparison of [alternatives](./index.md#alternatives). 
      
### Features

- Report generation in PDF (other formats can be supported, open an issue) 
- Datasources for JDBC, JSON and XML (url or filesystem) 
- Support for PSR-7 responses (stream)

### Requirements

- PHP 7.1+
- JasperBridge (see install)
- Java

### Alternatives

#### JasperReport based

- [JasperStarter](http://jasperstarter.cenote.de/) A CLI to run jasper reports
- [JasperServer](https://community.jaspersoft.com/wiki/what-jasperreports-server) 

#### PHP based 

- [FPDF](https://tcpdf.org/), [mpdf](https://github.com/mpdf/mpdf/), [tcpdf](https://github.com/tecnickcom/tcpdf)... 


#### Others 

Convert HTML, Markdown, Office to PDF 

- [Gotenberg](https://github.com/thecodingmachine/gotenberg) A Docker-powered stateless API for converting HTML, Markdown and Office documents to PDF
- [Unoconv](https://github.com/unoconv/unoconv)

  
## Coding standards and interop

* [PSR 7 HTTP Message](https://github.com/php-fig/http-message)
* [PSR 4 Autoloader](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)
* [PSR 3 Logger interface](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)
* [PSR 2 Coding Style Guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)

