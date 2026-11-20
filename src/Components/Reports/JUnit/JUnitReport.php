<?php

namespace SVRUnit\Components\Reports\JUnit;

use DOMDocument;
use SVRUnit\Components\Reports\ReportInterface;
use SVRUnit\Components\Tests\Results\RunResult;


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

        $content .= '<testsuites name="SVRUnit Tests" tests="' . $report->getTestCount() . '" time="' . $report->getTestTime() . '" failures="' . $report->getErrorCount() . '">';


        foreach ($report->getTestSuiteResults() as $suite) {

            $content .= '<testsuite name="' . $this->escape($suite->getTestSuite()->getName()) . '" tests="' . count($suite->getAllTestResults()) . '" time="' . $suite->getTestTime() . '" skipped="0" failures="0" errors="' . count($suite->getFailedTests()) . '">';

            foreach ($suite->getAllTestResults() as $test) {

                $content .= '<testcase name="' . $this->escape($test->getTest()->getName()) . '" classname="' . $this->escape($test->getClassName()) . '" assertions="' . $test->getAssertions() . '" time="' . $test->getTimeSeconds() . '" errors="' . $test->getErrors() . '">';

                if (!$test->isSuccess()) {
                    $content .= '<failure name="' . $this->escape($test->getTest()->getName()) . '" type="SVRunit_AssertionException">';
                    $content .= PHP_EOL;
                    $content .= 'Expected: ' . $this->escape($test->getExpected()) . PHP_EOL;
                    $content .= 'Actual: ' . $this->escape($test->getActual()) . PHP_EOL;
                    $content .= '</failure>';
                }

                $content .= '</testcase>';
            }

            $content .= '</testsuite>';
        }

        $content .= '</testsuites>';

        $path = dirname($this->filename);

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        $sanitizedContent = $this->sanitizeXmlString($content);

        $dom->loadXML($sanitizedContent);
        $out = $dom->saveXML();

        file_put_contents($this->filename, $out);
    }

    /**
     *
     */
    public function clear(): void
    {
        if (file_exists($this->filename)) {
            unlink($this->filename);
        }
    }


    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private function sanitizeXmlString(string $xml): string
    {
        // Remove ASCII control characters except tab, newline, and carriage return
        return (string)preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $xml);
    }

}
