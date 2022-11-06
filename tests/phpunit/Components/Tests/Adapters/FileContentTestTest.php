<?php

namespace SVRUnit\Tests\Components\Tests\Adapters;

use SVRUnit\Components\Tests\Adapters\CommandTest;
use SVRUnit\Components\Tests\Adapters\DirectoryExistsTest;
use SVRUnit\Components\Tests\Adapters\FileContentTest;
use SVRUnit\Components\Tests\Adapters\FileExistsTest;
use SVRUnit\Tests\Fakes\FakeTestRunner;
use PHPUnit\Framework\TestCase;

class FileContentTestTest extends TestCase
{

    /**
     * This test verifies that our success property
     * is correctly set and returned.
     */
    public function testName(): void
    {
        $test = new FileContentTest(
            'File Content ABC',
            '',
            'test.txt',
            '',
            ''
        );

        $this->assertEquals('File Content ABC', $test->getName());
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
        $this->expectExceptionMessage('FileContent test has an invalid configuration without a file');

        $test = new FileContentTest('', '', '', '', '');

        $fakeRunner = new FakeTestRunner('yes');
        $test->executeTest($fakeRunner);
    }

    /**
     * This test verifies that our test throws an exception
     * without an expected value.
     *
     * @return void
     * @throws \Exception
     */
    public function testThrowsExceptionWithoutExpected(): void
    {
        $this->expectExceptionMessage('FileContent test has an invalid configuration without a file');

        $test = new FileContentTest('', '', '', 'test.txt', '');

        $fakeRunner = new FakeTestRunner('yes');
        $test->executeTest($fakeRunner);
    }

    /**
     * This test verifies that our test throws an exception
     * without an unexpected value.
     *
     * @return void
     * @throws \Exception
     */
    public function testThrowsExceptionWithoutNotExpected(): void
    {
        $this->expectExceptionMessage('FileContent test has an invalid configuration without a file');

        $test = new FileContentTest('', '', '', '', 'test.txt');

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
        $test = new FileContentTest('', '', 'test.txt', 'my-content', '');

        $test->executeTest($fakeRunner);

        # 1 command executed
        $this->assertCount(1, $fakeRunner->getRunCommands());

        # command needs to be the right one
        $this->assertEquals('cat test.txt', $fakeRunner->getRunCommands()[0]);
    }

    /**
     * @testWith     [ true, true ]
     *               [ false, false ]
     *
     * @param bool $contentFound
     * @param bool $isSuccess
     * @return void
     * @throws \Exception
     */
    public function testResultForExpected(bool $contentFound, bool $isSuccess): void
    {
        $answer = ($contentFound) ? 'my-content' : 'my-wrong-content';

        $fakeRunner = new FakeTestRunner($answer);
        $test = new FileContentTest('', '', 'test.txt', 'my-content', '');

        $result = $test->executeTest($fakeRunner);

        # our fake runner says it is existing,
        # so verify that our result is also correctly created.
        $this->assertEquals($isSuccess, $result->isSuccess());
    }

    /**
     * @testWith     [ false, true ]
     *               [ true, false ]
     *
     * @param bool $contentFound
     * @param bool $isSuccess
     * @return void
     * @throws \Exception
     */
    public function testResultForNotExpected(bool $contentFound, bool $isSuccess): void
    {
        $answer = ($contentFound) ? 'my-content' : 'my-wrong-content';

        $fakeRunner = new FakeTestRunner($answer);
        $test = new FileContentTest('', '', 'test.txt', '', 'my-content');

        $result = $test->executeTest($fakeRunner);

        # our fake runner says it is existing,
        # so verify that our result is also correctly created.
        $this->assertEquals($isSuccess, $result->isSuccess());
    }

}
