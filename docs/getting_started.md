
> If you're new about Jasper and prefer to evaluate first, just play with [Jasperstudio](https://community.jaspersoft.com/project/jaspersoft-studio)
> and create one or two reports. 
    
### Installation

#### PHP

```bash
$ composer require soluble/jasper
```

#### Jasperbridge

There's an open issue about providing a [docker install](https://github.com/belgattitude/soluble-jasper/issues/5). We :heart: contributors :)

Quick start on Ubuntu Xenial with Tomcat8:

```
$ sudo apt install sudo apt install default-jdk tomcat8
```

Build the war container (war is +/- a phar for the PHP world)
  
```shell
# Example based on php-java-bridge master
$ git clone https://github.com/belgattitude/php-java-bridge.git
$ cd php-java-bridge
$ ./gradlew war -I init-scripts/init.jasperreports.gradle -I init-scripts/init.mysql.gradle 
```  

Deploy on Tomcat (example on ubuntu `sudo apt install tomcat8`)
  
```shell
$ sudo cp ./build/libs/JavaBridgeTemplate.war /var/lib/tomcat8/webapps/JasperBridge.war
```

The the address to [http://localhost:8080/JasperBridge](http://localhost:8080/JasperReports), you
should see the bridge dashboard page:

![](./assets/images/jasper_bridge_dashboard.png "Jasper bridge dashboard") 


### Test 

Test whether the bridge is working:

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



