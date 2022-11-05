<?php

namespace SVRUnit\Tests\Components\Tests\Adapters;

use PHPUnit\Framework\TestCase;
use SVRUnit\Components\Tests\Adapters\DirectoryExistsTest;
use SVRUnit\Tests\Fakes\FakeTestRunner;

class DirectoryExistsTestTest extends TestCase
{

    /**
     * This test verifies that our success property
     * is correctly set and returned.
     */
    public function testName(): void
    {
        $test = new DirectoryExistsTest(
            'DocRoot exists',
            '',
            'abc',
            false
        );

        $this->assertEquals('DocRoot exists', $test->getName());
    }

    /**
     *
     * @return void
     * @throws \Exception
     */
    public function testCommandCorrectlySent(): void
    {
        $fakeRunner = new FakeTestRunner('yes');
        $test = new DirectoryExistsTest('', '', '/var/www', true);

        $test->executeTest($fakeRunner);

        # 1 command executed
        $this->assertCount(1, $fakeRunner->getRunCommands());

        # command needs to be the right one
        $this->assertEquals('[ -d /var/www ] && echo yes || echo no', $fakeRunner->getRunCommands()[0]);
    }

    /**
     * @testWith     [ true, true ]
     *               [ false, false ]
     *
     * @param bool $dirFound
     * @param bool $isSuccess
     * @return void
     */
    public function testResultForExpected(bool $dirFound, bool $isSuccess): void
    {
        $answer = ($dirFound) ? 'yes' : 'no';

        $fakeRunner = new FakeTestRunner($answer);
        $test = new DirectoryExistsTest('', '', '/var/www', true);

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
     */
    public function testResultForNotExpected(bool $dirFound, bool $isSuccess): void
    {
        $answer = ($dirFound) ? 'yes' : 'no';

        $fakeRunner = new FakeTestRunner($answer);
        $test = new DirectoryExistsTest('', '', '/var/www', false);

        $result = $test->executeTest($fakeRunner);

        # our fake runner says it is existing,
        # so verify that our result is also correctly created.
        $this->assertEquals($isSuccess, $result->isSuccess());
    }

}
