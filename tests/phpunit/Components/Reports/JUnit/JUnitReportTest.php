<?php

namespace SVRUnit\Tests\Components\Reports\JUnit;

use PHPUnit\Framework\TestCase;
use SVRUnit\Components\Reports\JUnit\JUnitReport;
use SVRUnit\Components\Tests\Adapters\CommandTest;
use SVRUnit\Components\Tests\Results\RunResult;
use SVRUnit\Components\Tests\Results\SuiteResult;
use SVRUnit\Components\Tests\Results\TestResult;
use SVRUnit\Components\Tests\TestSuite;


class JUnitReportTest extends TestCase
{

    /**
     * This test verifies that XML special characters in names
     * and outputs are escaped, so that a valid report is written.
     */
    public function testSpecialCharactersAreEscaped(): void
    {
        $file = sys_get_temp_dir() . '/svrunit_' . uniqid() . '/junit.xml';

        $test = new CommandTest('Test "A" & <B>', '', 'ls', 'x', [], [], '', '', '');
        $testResult = new TestResult($test, 'spec.yml', false, 1, 'Should contain: <x>', 'a & "b"');

        $runResult = new RunResult();
        $runResult->addSuiteResult(new SuiteResult(new TestSuite('Suite & Co', ''), [$testResult]));

        (new JUnitReport($file))->generate($runResult);

        $xml = simplexml_load_file($file);

        $this->assertNotFalse($xml);
        $this->assertEquals('Test "A" & <B>', (string)$xml->testsuite->testcase['name']);
        $this->assertStringContainsString('Actual: a & "b"', (string)$xml->testsuite->testcase->failure);

        unlink($file);
        rmdir(dirname($file));
    }

}
