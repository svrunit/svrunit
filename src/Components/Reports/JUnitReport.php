<?php

namespace SVRUnit\Components\Reports;

use DOMDocument;
use SVRUnit\Components\Tests\TestSuiteResultInterface;

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
     * @param TestSuiteResultInterface[] $suites
     */
    public function generate(array $suites): void
    {
        $content = '<?xml version="1.0" encoding="UTF-8"?>';

        $content .= '<testsuites>';

        foreach ($suites as $suite) {

            $testCount = count($suite->getResults());

            $content .= '<testsuite name="' . $suite->getName() . '" tests="' . $testCount . '">';

            foreach ($suite->getResults() as $testResult) {

                $errorCount = ($testResult->isSuccess()) ? 0 : 1;

                $content .= '<testcase name="' . $testResult->getTest()->getName() . '" assertions="1" errors="' . $errorCount . '">';

                if (!$testResult->isSuccess()) {
                    $content .= '<failure name="' . $testResult->getTest()->getName() . '" type="SVRunit_AssertionException">';
                    $content .= PHP_EOL;
                    $content .= 'Expected: ' . $testResult->getExpected() . PHP_EOL;
                    $content .= 'Actual: ' . $testResult->getOutput();
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