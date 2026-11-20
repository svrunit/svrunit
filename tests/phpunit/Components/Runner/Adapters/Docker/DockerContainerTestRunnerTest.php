<?php

namespace SVRUnit\Tests\Components\Runner\Adapters\Docker;

use PHPUnit\Framework\TestCase;
use SVRUnit\Components\Runner\Adapters\Docker\DockerContainerTestRunner;
use SVRUnit\Tests\Fakes\FakeOutputWriter;
use SVRUnit\Tests\Fakes\FakeShellRunner;


class DockerContainerTestRunnerTest extends TestCase
{

    /**
     * This test verifies that the command is executed
     * inside the existing container including stderr.
     */
    public function testRunTest(): void
    {
        $shell = new FakeShellRunner();

        $runner = new DockerContainerTestRunner('my_container', $shell, new FakeOutputWriter(), false);

        $runner->runTest('php -v');

        $this->assertEquals("docker exec my_container bash -c 'php -v 2>&1'", $shell->getUsedCommand());
    }

}
