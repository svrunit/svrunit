<?php


namespace SVRUnit\Components\Tests;


class TestSuiteResult implements TestSuiteResultInterface
{

    /**
     * @var TestSuite
     */
    private $testSuite;

    private $results;


    /**
     * TestSuiteResult constructor.
     * @param $testSuite
     * @param $results
     */
    public function __construct(TestSuite $testSuite, array $results)
    {
        $this->testSuite = $testSuite;
        $this->results = $results;
    }


    public function getName(): string
    {
        return $this->testSuite->getName();
    }

    public function hasErrors(): bool
    {
        /** @var TestResultInterface $result */
        foreach ($this->results as $result) {

            if ($result->isSuccess() === false) {
                return false;
            }
        }

        return true;
    }


    public function getResults(): array
    {
        return $this->results;
    }

}
