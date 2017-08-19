# CONTRIBUTING


## Recommended workflow

### Setup

1. Setup a [GitHub account](https://github.com/), if you haven't yet.
2. Fork the project (i.e from the github project page). 
3. Clone your own fork

### Test

Setup a local JasperBridgeServer and copy ./phpunit.xml.dist in
./phpunit.xml (edit config as needed). Check phpunit works (`./vendor/bin/phpunit`).

### Contribute

1. Create a new branch from master (i.e. feature/24)
2. Modify the code... Fix or improve :)
3. Run `composer fix` to be sure code style is ok.
4. Run `composer check` to run style checks and phpstan. 
5. Commit/Push your pull request. 

Thanks !!!
   
