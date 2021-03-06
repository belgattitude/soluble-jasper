# Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

## 1.0.0 (2019-05-27)

### Improved

- Few minor fixes. Version 1.0.0 release.

## 0.7.3 (2018-10-26)

### Added

- Support for `zend-diactoros` 1.6+ and 2.0+.

## 0.7.2 (2018-08-27)

### Improved 

- Filesystem permissions and directory existence tests

## 0.7.1 (2018-06-28)

### Improved

- Improved permission errors (`IOPermissionException`...) when saving the report fails.

### Fixed

- Minor, enforce strict return type to `ReportParams::getIterator() : ArrayIterator`.
- JasperFillManager better detects `BrokenJsonException`. 

### Changed

- Refactor exception hierarchy
- Minor, use `sprintf()` instead for string concat in `JdbcDsnFactory`.
- Added automatic installation of expressive smoke test server (composer)

### Updated

- Q&A tools: phpstan 0.10 & infection mutation framework.

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
