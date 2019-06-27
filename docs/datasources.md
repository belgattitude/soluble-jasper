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
 
$jsonDataSource = new JsonDataSource('<url_or_path>/northwind.json');
/*
$jsonDataSource->setOptions([
    JsonDataSource::PARAM_JSON_DATE_PATTERN   => 'yyyy-MM-dd',
    JsonDataSource::PARAM_JSON_NUMBER_PATTERN => '#,##0.##',
    JsonDataSource::PARAM_JSON_TIMEZONE_ID    => 'Europe/Brussels',
    JsonDataSource::PARAM_JSON_LOCALE_CODE    => 'en_US'
]);
*/

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

### XmlDataSource

Example using `XmlDataSource`:

```php
<?php declare(strict_types=1);

use Soluble\Jasper\{ReportRunnerFactory, Report, ReportParams};
use Soluble\Jasper\DataSource\XmlDataSource;
 
$xmlDataSource = new XmlDataSource('<url_or_path>/northwind.xml');
/*
$xmlDataSource->setOptions([
    XmlDataSource::PARAM_XML_DATE_PATTERN   => 'yyyy-MM-dd',
    XmlDataSource::PARAM_XML_NUMBER_PATTERN => '#,##0.##',
    XmlDataSource::PARAM_XML_TIMEZONE_ID    => 'Europe/Brussels',
    XmlDataSource::PARAM_XML_LOCALE_CODE    => 'en_US'
]);
*/

$report = new Report(
                '/path/myreport.jrxml',
                new ReportParams([
                    'LOGO_FILE' => '/path/assets/wave.png',
                    'TITLE'     => 'My Title'            
                ]),  
                $xmlDataSource);

$reportRunner = ReportRunnerFactory::getBridgedReportRunner($this->ba);
$exportManager = $reportRunner->getExportManager($report);

$exportManager->savePdf('/path/my_output.pdf');

```
