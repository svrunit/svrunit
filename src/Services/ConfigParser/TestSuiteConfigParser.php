<?php

namespace SVRUnit\Services\ConfigParser;

use SimpleXMLElement;
use SVRUnit\Components\Tests\TestSuite;


class TestSuiteConfigParser
{

    /**
     * @param string $configFile
     * @return array<mixed>
     * @throws \Exception
     */
    public function loadTestSuites(string $configFile): array
    {
        $xmlString = (string)file_get_contents($configFile);
        $xmlSettings = simplexml_load_string($xmlString);

        $testSuites = [];

        if (!$xmlSettings instanceof SimpleXMLElement) {
            throw new \Exception('Error when loading Test Suite. Invalid XML: ' . $configFile);
        }

        $attributes = $xmlSettings->attributes();

        $setupTime = 0;

        if ($attributes instanceof SimpleXMLElement) {
            if ($attributes->setupTime) {
                $setupTime = (string)$attributes->setupTime[0];
            } 
        }

        $setupTime = (int)$setupTime;


        /** @var SimpleXMLElement $suiteNode */
        foreach ($xmlSettings->testsuites->children() as $suiteNode) {

            $suite = new TestSuite(
                (string)$suiteNode['name'],
                (string)$suiteNode['group']
            );

            $suite->setSetupTimeSeconds($setupTime);


            if ($suiteNode['executable'] !== null) {
                $suite->setExecutable((string)$suiteNode['executable']);
            }

            if ($suiteNode['dockerImage'] !== null) {
                $suite->setDockerImage((string)$suiteNode['dockerImage']);
            }

            if ($suiteNode['dockerCommandRunner'] !== null) {
                $value = (string)$suiteNode['dockerCommandRunner'];
                $suite->setDockerCommandRunner((bool)$value);
            }

            if ($suiteNode['dockerContainer'] !== null) {
                $suite->setDockerContainer((string)$suiteNode['dockerContainer']);
            }

            if ($suiteNode['dockerEntrypoint'] !== null) {
                $suite->setDockerEntrypoint((string)$suiteNode['dockerEntrypoint']);
            }

            if ($suiteNode['dockerEnv'] !== null) {
                $envs = (string)$suiteNode['dockerEnv'];
                $envVariables = [];
                if (!empty($envs)) {
                    $envVariables = array_filter(explode(',', $envs));
                }
                $suite->setDockerEnvVariables($envVariables);
            }


            /** @var SimpleXMLElement $childNode */
            foreach ($suiteNode->children() as $childNode) {

                $nodeType = $childNode->getName();
                $nodeValue = (string)$childNode[0];

                switch ($nodeType) {
                    case 'file':
                        $suite->addTestFile($nodeValue);
                        break;

                    case 'directory':
                        $suite->addTestFolder($nodeValue);
                        break;

                    default:
                        throw new \Exception('Unknown child in test suite: ' . $childNode->getName() . ', ' . $suite->getName());
                }
            }

            $testSuites[] = $suite;
        }

        return $testSuites;
    }

}
