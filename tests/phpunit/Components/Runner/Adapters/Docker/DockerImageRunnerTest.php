<?php

namespace SVRUnit\Tests\Components\Runner\Adapters\Docker;

use PHPUnit\Framework\TestCase;
use SVRUnit\Components\Runner\Adapters\Docker\DockerImageRunner;
use SVRUnit\Tests\Fakes\FakeOutputWriter;
use SVRUnit\Tests\Fakes\FakeShellRunner;


class DockerImageRunnerTest extends TestCase
{

    /**
     * This test verifies that the setup command of the
     * docker image is correctly built and sent
     * to the shell terminal.
     */
    public function testSetupWithoutEnv()
    {
        $shell = new FakeShellRunner();

        $runner = new DockerImageRunner(
            'img/test:latest',
            array(),
            '',
            'svrunit_abc',
            $shell,
            new FakeOutputWriter()
        );

        $runner->setUp();

        $this->assertEquals('docker run --rm --name svrunit_abc -d img/test:latest', $shell->getUsedCommand());
    }

    /**
     * This test verifies that the setup command including
     * provided ENV variables for the docker image is correctly built and sent
     * to the shell terminal.
     */
    public function testSetupWithEnv()
    {
        $shell = new FakeShellRunner();

        $runner = new DockerImageRunner(
            'img/test:latest',
            array('PHP_VERSION=1', 'XDEBUG_ENABLED=1'),
            '',
            'svrunit_abc',
            $shell,
            new FakeOutputWriter()
        );

        $runner->setUp();

        $this->assertEquals('docker run --rm --env PHP_VERSION=1 --env XDEBUG_ENABLED=1 --name svrunit_abc -d img/test:latest', $shell->getUsedCommand());
    }

    /**
     * This test verifies that the docker container is
     * correctly removed when executing the teardown phase.
     */
    public function testTeardown()
    {
        $shell = new FakeShellRunner();

        $runner = new DockerImageRunner(
            'img/test:latest',
            array(),
            '',
            'svrunit_abc',
            $shell,
            new FakeOutputWriter()
        );

        $runner->tearDown();

        $this->assertEquals('docker rm -f svrunit_abc', $shell->getUsedCommand());
    }

}
