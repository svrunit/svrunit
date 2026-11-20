<?php

namespace SVRUnit\Components\Reports\Html;

use SVRUnit\Components\Reports\ReportInterface;
use SVRUnit\Components\Tests\Results\RunResult;
use SVRUnit\SVRUnit;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;


class HtmlReport implements ReportInterface
{

    /**
     * @var string
     */
    private $filename;

    /**
     * @var Environment
     */
    private $twig;


    /**
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;

        $loader = new FilesystemLoader(__DIR__ . '/Template');
        $this->twig = new Environment($loader);
    }


    /**
     * @param RunResult $result
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
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


        $html = $this->twig->render('index.html.twig', $params);

        $path = dirname($this->filename);

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        file_put_contents($this->filename, $html);
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
