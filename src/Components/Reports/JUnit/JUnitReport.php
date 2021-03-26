<?php

namespace SVRUnit\Components\Reports\JUnit;

use DOMDocument;
use SVRUnit\Components\Reports\ReportInterface;
use SVRUnit\Components\Reports\TestResult;
use SVRUnit\Components\Reports\TestSuiteResult;
use SVRUnit\Components\Tests\Results\RunResult;
use SVRUnit\Components\Tests\Results\SuiteResult;


class JUnitReport implements ReportInterface
{

    /**
     * @var string
     */
    private $filename;


    /**
     * Reports constructor.
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }


    /**
     * @param RunResult $report
     */
    public function generate(RunResult $report): void
    {
        $content = '<?xml version="1.0" encoding="UTF-8"?>';

        $content .= '<testsuites>';


        foreach ($report->getTestSuiteResults() as $suite) {

            $content .= '<testsuite name="' . $suite->getTestSuite()->getName() . '" tests="' . count($suite->getAllTestResults()) . '" time="' . $suite->getTestTime() . '" failures="0" errors="' . count($suite->getFailedTests()) . '">';

            foreach ($suite->getAllTestResults() as $test) {

                $content .= '<testcase name="' . $test->getTest()->getName() . '" classname="svrunit" assertions="' . $test->getAssertions() . '" time="' . $test->getTime() . '" errors="' . $test->getErrors() . '">';

                if (!$test->isSuccess()) {
                    $content .= '<failure name="' . $test->getTest()->getName() . '" type="SVRunit_AssertionException">';
                    $content .= PHP_EOL;
                    $content .= 'Expected: ' . $test->getExpected() . PHP_EOL;
                    $content .= 'Actual: ' . $test->getActual() . PHP_EOL;
                    $content .= '</failure>';
                }

                $content .= '</testcase>';
            }

            $content .= '</testsuite>';
        }

        $content .= '</testsuites>';

        $path = dirname($this->filename);

        if (!is_dir($path)) {
            mkdir($path);
        }

        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        $dom->loadXML($content);
        $out = $dom->saveXML();

        file_put_contents($this->filename, $out);
    }

}
