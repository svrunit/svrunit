<?php

namespace SVRUnit\Services\ConfigParser;

use SimpleXMLElement;
use SVRUnit\Components\Tests\TestSuite;


class ConfigXmlParser
{

    /**
     * @param $configFile
     * @return array
     */
    public function loadTestSuites(string $configFile): array
    {
        $xmlString = file_get_contents($configFile);
        $xmlSettings = simplexml_load_string($xmlString);

        $testSuites = [];

        $setupTime = (string)$xmlSettings->attributes()->setupTime[0];

        if (empty($setupTime)) {
            $setupTime = 0;
        }


        /** @var SimpleXMLElement $suiteNode */
        foreach ($xmlSettings->testsuites->children() as $suiteNode) {

            $suite = new TestSuite(
                (string)$suiteNode['name']
            );

            $suite->setSetupTimeSeconds($setupTime);


            if ($suiteNode['dockerImage'] !== null) {
                $suite->setDockerImage((string)$suiteNode['dockerImage']);
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


            /** @var SimpleXMLElement $directory */
            foreach ($suiteNode->children() as $directory) {
                $folder = (string)$directory[0];
                $suite->addTestFolder($folder);
            }

            $testSuites[] = $suite;
        }

        return $testSuites;
    }

}