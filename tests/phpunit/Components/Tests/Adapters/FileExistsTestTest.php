<?php

namespace SVRUnit\Tests\Components\Tests\Adapters;

use SVRUnit\Components\Tests\Adapters\CommandTest;
use SVRUnit\Components\Tests\Adapters\DirectoryExistsTest;
use SVRUnit\Components\Tests\Adapters\FileExistsTest;
use SVRUnit\Tests\Fakes\FakeTestRunner;
use PHPUnit\Framework\TestCase;

class FileExistsTestTest extends TestCase
{

    /**
     * This test verifies that our success property
     * is correctly set and returned.
     */
    public function testName(): void
    {
        $test = new FileExistsTest(
            'File exists',
            '',
            'test.txt',
            false
        );

        $this->assertEquals('File exists', $test->getName());
    }

    /**
     * This test verifies that our test throws an exception
     * if no file was configured to be tested.
     *
     * @return void
     * @throws \Exception
     */
    public function testThrowsExceptionWithoutFile(): void
    {
        $this->expectExceptionMessage('FileExists test has an invalid configuration without a file');

        $test = new FileExistsTest(
            'File exists',
            '',
            '',
            false
        );

        $fakeRunner = new FakeTestRunner('yes');
        $test->executeTest($fakeRunner);
    }

    /**
     *
     * @return void
     * @throws \Exception
     */
    public function testCommandCorrectlySent(): void
    {
        $fakeRunner = new FakeTestRunner('yes');
        $test = new FileExistsTest('File exists', '', 'test.txt', true);

        $test->executeTest($fakeRunner);

        # 1 command executed
        $this->assertCount(1, $fakeRunner->getRunCommands());

        # command needs to be the right one
        $this->assertEquals('[ -f test.txt ] && echo svrunit-file-exists || echo svrunit-file-not-existing', $fakeRunner->getRunCommands()[0]);
    }

    /**
     * @testWith     [ true, true ]
     *               [ false, false ]
     *
     * @param bool $dirFound
     * @param bool $isSuccess
     * @return void
     * @throws \Exception
     */
    public function testResultForExpected(bool $dirFound, bool $isSuccess): void
    {
        $answer = ($dirFound) ? 'svrunit-file-exists' : 'svrunit-file-not-exists';

        $fakeRunner = new FakeTestRunner($answer);
        $test = new FileExistsTest('File exists', '', 'test.txt', true);

        $result = $test->executeTest($fakeRunner);

        # our fake runner says it is existing,
        # so verify that our result is also correctly created.
        $this->assertEquals($isSuccess, $result->isSuccess());
    }

    /**
     * @testWith     [ false, true ]
     *               [ true, false ]
     *
     * @param bool $dirFound
     * @param bool $isSuccess
     * @return void
     * @throws \Exception
     */
    public function testResultForNotExpected(bool $dirFound, bool $isSuccess): void
    {
        $answer = ($dirFound) ? 'svrunit-file-exists' : 'svrunit-file-not-exists';

        $fakeRunner = new FakeTestRunner($answer);
        $test = new FileExistsTest('File exists', '', 'test.txt', false);

        $result = $test->executeTest($fakeRunner);

        # our fake runner says it is existing,
        # so verify that our result is also correctly created.
        $this->assertEquals($isSuccess, $result->isSuccess());
    }

}
