
[<img src="https://raw.githubusercontent.com/wiki/svrunit/svrunit/assets/logo.png">]()

![Build Status](https://github.com/svrunit/svrunit/actions/workflows/ci_pipe.yml/badge.svg) ![GitHub release (latest by date)](https://img.shields.io/github/v/release/svrunit/svrunit) ![GitHub commits since latest release (by date)](https://img.shields.io/github/commits-since/svrunit/svrunit/latest) ![Build Status](https://github.com/svrunit/svrunit/actions/workflows/nightly_build.yml/badge.svg)

Welcome to SVRUnit - Your Server Testing Framework!

## Basic Concept

Sometimes it's hard to verify that your server or even Docker image has all mandatory dependencies and packages installed.

Or maybe you just want to verify that some files exist - or do not exist at all?!

SVRUnit allows you to configure different test that you can run against your server, Docker container or even plain Docker images.

Why not just testing all these different Xdebug versions of your Docker images!?

[<img src="https://raw.githubusercontent.com/wiki/svrunit/svrunit/assets/test-result.png">]()

## Installation

SVRUnit is based on a `phar` file and thus requires PHP to be installed. But once that is done, just download the ZIP, extract the PHAR and you're ready to go.

```
curl -O https://www.svrunit.com/downloads/svrunit.zip
unzip svrunit.zip -d .
rm -f svrunit.zip
```

## Configuration
The whole configuration is done using a XML file.
You can create different test suites with different settings and even share tests across all of your suites.

Every test suite has the information where it will be run.
Either on your local machine, or in a Docker image that will be spawned automatically for you.


## Assertions
Tests are based on YAML files. You can create 1 single file with all your tests, or different tests based on topics or anything else.

SVRUnit provides different assertion options, such as "File Exists", "File Contains" and way more. Configure it, assign the expected result and you're ready to go.


## Start Tests
Once configured, you can easily start your test with this command:

```
php svrunit.phar --configuration=./svrunit.xml
```

That's it for now!
You can read more about all these different possibilities in your WIKI guide.



## Documentation
There's plenty of documentation available at https://docs.svrunit.com

Please use this as additional resources.

