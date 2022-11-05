<?php

namespace SVRUnit\Tests\Components\Tests\Adapters;

use SVRUnit\Components\Tests\Adapters\CommandTest;
use SVRUnit\Tests\Fakes\FakeTestRunner;
use PHPUnit\Framework\TestCase;

class CommandTestTest extends TestCase
{

    /**
     * This test verifies that our success property
     * is correctly set and returned.
     */
    public function testName(): void
    {
        $test = new CommandTest(
            'PHP Test',
            '',
            '',
            '',
            [],
            [],
            '',
            '',
            ''
        );

        $this->assertEquals('PHP Test', $test->getName());
    }

    /**
     * This  test verifies that our command is correctly
     * passed on to the test runner.
     *
     * @return void
     * @throws \Exception
     */
    public function testCommandIsRun(): void
    {
        $fakeRunner = new FakeTestRunner();

        $test = new CommandTest(
            'PHP Test',
            '',
            'ls -la',
            'test',
            [],
            [],
            '',
            '',
            ''
        );

        $test->executeTest($fakeRunner);

        $this->assertCount(1, $fakeRunner->getRunCommands());
        $this->assertEquals('ls -la', $fakeRunner->getRunCommands()[0]);
    }

    /**
     * This  test verifies that our provided setup and
     * teardown commands are correctly passed on to
     * our test runner.
     *
     * @return void
     * @throws \Exception
     */
    public function testSetupAndTeardownAreUsed(): void
    {
        $fakeRunner = new FakeTestRunner();

        $test = new CommandTest(
            'PHP Test',
            '',
            'ls -la test',
            'test',
            [],
            [],
            '',
            'mkdir -p test',
            'rm -rf test'
        );

        $test->executeTest($fakeRunner);

        $this->assertCount(3, $fakeRunner->getRunCommands());

        $this->assertEquals('mkdir -p test', $fakeRunner->getRunCommands()[0]);
        $this->assertEquals('ls -la test', $fakeRunner->getRunCommands()[1]);
        $this->assertEquals('rm -rf test', $fakeRunner->getRunCommands()[2]);
    }

}
