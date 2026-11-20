<?php

namespace SVRUnit\Tests\Components\Runner\Adapters\Local;

use PHPUnit\Framework\TestCase;
use SVRUnit\Components\Runner\Adapters\Local\LocalTestRunner;
use SVRUnit\Tests\Fakes\FakeOutputWriter;
use SVRUnit\Tests\Fakes\FakeShellRunner;


class LocalTestRunnerTest extends TestCase
{

    /**
     * This test verifies that the command is executed
     * locally including stderr.
     */
    public function testRunTest(): void
    {
        $shell = new FakeShellRunner();

        $runner = new LocalTestRunner($shell, new FakeOutputWriter(), false);

        $runner->runTest('php -v');

        $this->assertEquals('php -v 2>&1', $shell->getUsedCommand());
    }

}
