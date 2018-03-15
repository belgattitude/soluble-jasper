# Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

## 0.7.1 (unreleased)

### Imroved

- Minor, use `sprintf()` instead for string concat in `JdbcDsnFactory`.

### Fixed

- Minor, enforce strict return type to `ReportParams::getIterator() : ArrayIterator`. 

## 0.7.0 (2018-03-15)

### Improved

- Ensure JsonDataSource and XmlDataSource accepts also a valid url. 

### Added

- XmlDataSource added
- Added `JavaCompileManager::compileReportToFile()` method

### Fixed

*nothing*
 
### Removed

*nothing*
