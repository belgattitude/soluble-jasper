# CONTRIBUTING


## Recommended workflow

1. Setup a [GitHub account](https://github.com/), if you haven't yet.
2. Fork the project (i.e from the github project page). All dev is done on 'master' branch
3. Clone your own fork, run `composer install`.

Then setup a local JasperBridgeServer and copy ./phpunit.xml.dist in
./phpunit.xml (edit config as needed). Check phpunit works (`./vendor/bin/phpunit`).

4. Modify the code... Fix or improve :)
5. Run `composer fix` to be sure code style is ok.
6. Run `composer check` to run style checks and phpstan. 
7. Commit/Push your pull request. 

Thanks !!!
   
