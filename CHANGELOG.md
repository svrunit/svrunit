# Changes in SVRUnit

All notable changes of SVRUnit releases are documented in this file
using the [Keep a CHANGELOG](https://keepachangelog.com/) principles.

## [unreleased]

### Added

- Add command debug output to local test runner

### Fixed

- Fixed problem with invalid characters in JUnit Reports.

## [1.8.0]

### Added

- Added new suite argument **(($EXEC))** to provide a custom executable placeholder. This helps to reuse the same tests for different types of your AUT. You can e.g. test a bin/script + PHAR version with the same tests by using placeholders for your executable.

### Changed

- Allow test files with a "*.yaml" extension and not only "*.yml"
- Change dependencies of symfony/yaml and twig/twig to * for better composer compatibility in other projects
- DirectoryExists Tests does now throw an error if no directory was specified.
- FileExists Tests does now throw an error if no file was specified.
- FileContentTest Tests does now throw an error if no file or expected/unexpected conditions where specified.

## [1.7.0]

### Added

- Added new "expected_and" and "expected_or" conditions to Command tests. This allows you to provide an array of expected strings.
- Added new option to provide single files in addition to directory for Test Suites.
- Added new option to check PHP INI values also from Apache Web values. 2 Modes are available: "web" and "cli" for ini tests.

### Changed

- Moved the commands to list groups and suites to 2 separate commands "list:groups" and "list:suites".
- The Docker pull command is now done in "quiet" mode if the Docker image is not found locally. This reduces the CLI output.
- Improved expected and actual output for directory tests.
- Runs without 0 found tests will now lead to a failure. Before this, this was just a warning output.
- Failed catches due to Fatal exceptions will now be visible with a red symfony console output.

### Fixed

- Fixed broken local test runner without Docker.

## [1.6.0]

### Added

- Add new bin/svrunit script. This means you can finally also install and run it via composer in your project.

## [1.5.0]

### Added

- Add new "exclude-group" option to exclude one or more groups during the test run

### Changed

- Changed the default command to be "list". "php svrunit.phar" will now show all available commands.

## [1.4.0]

### Added

- Add new "group" option in Test Suite. Use groups to only run a set of specific test suites.
- Add new "list-groups" option to output all available groups.
- Add new "list-suites" option to output all available test suites.

## [1.3.0]

### Added

- The CommandTest does now offer 2 new settings "setup" and "teardown" to run any commands prior or after the test.

### Changed

- Improve output of Expected and Actual values of test on CLI

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
