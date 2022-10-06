# Changes in SVRUnit

All notable changes of SVRUnit releases are documented in this file
using the [Keep a CHANGELOG](https://keepachangelog.com/) principles.


## [UNRELEASED]

### Fixed

- Fixed wrong calculation (error) in ErrorCount for Test Results and Reports
- Improved the reliability of the Yaml parser and fixed a few NULL crashes due to broken xml configurations


## [1.2.5]

### Changed

- JUnit Reports now show the specification file as className for tests.

### Fixed

- Fix wrong total test count of suites in reports
- Fix broken time measurement of tests
- Fix wrong JUnit time measurement displays. It does now use seconds instead of milliseconds.

## [1.2.4]

### Added

- Added new option "dockerImageRunner" in XML Configuration to also test simple commands that are run in a Docker image.

## [1.2.3] - 2022-01-29

### Changed

- Improved command output comparing by removing a few new lines and trim the values first

### Fixed

- Fix bug where an expected value of "0" in the command tests lead to a wrong behaviour

## [1.2.2] - 2021-04-06

### Added

- Added new option "--report-html" to generate HTML reports

## [1.2.1] - 2021-03-27

### Added

- Added new option "--stop-on-error" to immediately stop if an error occurs.

### Changed

- Improved JUnit XML output with more data

## [1.2] - 2021-03-24

### Added

- Added new JUnit XML Reporter for test suites

### Changed

- Improved CLI commands with options like --help, --version

## [1.1] - 2021-03-19

### Changed

- Exit Code when started with a missing configuration file is now ERROR

## [1.0] - 2021-03-09

### Added

- Initial Version
