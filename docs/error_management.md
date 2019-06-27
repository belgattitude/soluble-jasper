### Exceptions

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

### Common errors


If you encounter permissions problems (i.e. the pdf are created under tomcat8 user), just add your user 
to the tomcat group:

```shell
$ sudo usermod -a -G <tomcat group name> <username>
``` 


### Logging


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

