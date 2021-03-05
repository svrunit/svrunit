<?php

/**
 * Class FakeTest
 * @author Christian Dangl
 * @copyright dasistweb GmbH (http://www.dasistweb.de)
 */
class FakeTest implements TestInterface
{

    /**
     * @author Christian Dangl
     * @return string
     */
    function getName()
    {
        return "";
    }

    /**
     * @author Christian Dangl
     * @param TestRunnerInterface $runner
     * @return null|TestResult
     */
    function executeTest(TestRunnerInterface $runner)
    {
        return null;
    }

}
