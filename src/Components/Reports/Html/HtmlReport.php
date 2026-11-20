<?php

namespace SVRUnit\Components\Reports\Html;

use SVRUnit\Components\Reports\ReportInterface;
use SVRUnit\Components\Tests\Results\RunResult;
use SVRUnit\SVRUnit;


class HtmlReport implements ReportInterface
{

    /**
     * @var string
     */
    private $filename;


    /**
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }


    /**
     * @param RunResult $result
     */
    public function generate(RunResult $result): void
    {
        $params = [
            'version' => SVRUnit::VERSION,
            'generated' => date('Y-m-d H:i:s T'),
            'total' => $result->getTestCount(),
            'failed' => $result->getErrorCount(),
            'time' => $result->getTestTime(),
            'testsuites' => [],
        ];

        foreach ($result->getTestSuiteResults() as $suiteResult) {

            $suiteData = [
                'name' => $suiteResult->getTestSuite()->getName(),
                'group' => $suiteResult->getTestSuite()->getGroup(),
                'time' => $suiteResult->getTestTime(),
                'total' => count($suiteResult->getAllTestResults()),
                'errors' => count($suiteResult->getFailedTests()),
                'tests' => [],
            ];

            foreach ($suiteResult->getAllTestResults() as $test) {

                $testData = [
                    'name' => $test->getTest()->getName(),
                    'success' => $test->isSuccess(),
                    'time' => $test->getTimeSeconds(),
                    'expected' => $test->getExpected(),
                    'actual' => $test->getActual(),
                ];

                $suiteData['tests'][] = $testData;
            }

            $params['testsuites'][] = $suiteData;
        }


        $html = $this->render($params);

        $path = dirname($this->filename);

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        file_put_contents($this->filename, $html);
    }

    /**
     * Plain PHP template instead of a template engine, so that we don't need
     * a dependency that would block installations in older PHP projects.
     *
     * @param array<mixed> $report
     * @return string
     */
    private function render(array $report): string
    {
        $e = function ($value): string {
            return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
        };

        ob_start();

        require __DIR__ . '/Template/index.html.php';

        return (string)ob_get_clean();
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

}
