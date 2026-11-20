<?php

namespace SVRUnit\Tests\Services\ConfigParser;

use PHPUnit\Framework\TestCase;
use SVRUnit\Components\Tests\TestSuite;
use SVRUnit\Services\ConfigParser\TestSuiteConfigParser;


class TestSuiteConfigParserTest extends TestCase
{

    /**
     * This test verifies that the dockerCommandRunner attribute
     * is parsed as a real boolean, so "false" does not enable it.
     *
     * @testWith ["true", true]
     *           ["1", true]
     *           ["false", false]
     *           ["0", false]
     */
    public function testDockerCommandRunnerBoolean(string $value, bool $expected): void
    {
        $file = tempnam(sys_get_temp_dir(), 'svrunit');

        file_put_contents($file, '<svrunit><testsuites><testsuite name="A" dockerImage="img" dockerCommandRunner="' . $value . '"/></testsuites></svrunit>');

        $suites = (new TestSuiteConfigParser())->loadTestSuites($file, '');

        unlink($file);

        $expectedType = $expected ? TestSuite::TYPE_DOCKER_COMMAND_RUNNER : TestSuite::TYPE_DOCKER_IMAGE;

        $this->assertEquals($expectedType, $suites[0]->getType());
    }

}
