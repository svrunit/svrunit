<?php

namespace SVRUnit\Services\TestParser;


use SVRUnit\Components\Tests\Adapters\CommandTest;
use SVRUnit\Components\Tests\Adapters\DirectoryExistsTest;
use SVRUnit\Components\Tests\Adapters\FileContentTest;
use SVRUnit\Components\Tests\Adapters\FileExistsTest;
use SVRUnit\Components\Tests\Adapters\FilePermissionTest;
use SVRUnit\Components\Tests\Adapters\PhpIniTest;
use SVRUnit\Components\Tests\Adapters\PhpModuleTest;

class YamlTestParser
{

    const TEST_KEY_COMMANDS = "commands";
    const TEST_KEY_PHP_INI = "php_ini";
    const TEST_KEY_PHP_MODULE = "php_module";
    const TEST_KEY_FILE_EXISTS = "file_exists";
    const TEST_KEY_FILE_CONTENT = "file_content";
    const TEST_KEY_FILE_PERMISSION = "file_permission";
    const TEST_KEY_DIRECTORY_EXISTS = "directory_exists";


    /**
     * @param $testsFile
     * @return array
     */
    public function parse($testsFile)
    {
        $parser = new \Symfony\Component\Yaml\Parser();

        $parsed = $parser->parse(file_get_contents($testsFile));

        /** @var array $tests */
        $tests = $this->parseTests($parsed);

        return $tests;
    }


    /**
     * @param $parsed
     * @return array
     */
    private function parseTests($parsed)
    {
        $tests = array();

        if (array_key_exists(self::TEST_KEY_COMMANDS, $parsed)) {

            /** @var array $command */
            foreach ($parsed[self::TEST_KEY_COMMANDS] as $command) {
                $cmd = new CommandTest(
                    $this->getValue('name', $command, ''),
                    $this->getValue('command', $command, ''),
                    $this->getValue('expected', $command, ''),
                    $this->getValue('not_expected', $command, '')
                );
                $tests[] = $cmd;
            }
        }

        if (array_key_exists(self::TEST_KEY_FILE_EXISTS, $parsed)) {

            /** @var array $command */
            foreach ($parsed[self::TEST_KEY_FILE_EXISTS] as $command) {
                $cmd = new FileExistsTest(
                    $this->getValue('name', $command, ''),
                    $this->getValue('file', $command, ''),
                    (bool)$this->getValue('expected', $command, '0')
                );
                $tests[] = $cmd;
            }
        }

        if (array_key_exists(self::TEST_KEY_DIRECTORY_EXISTS, $parsed)) {

            /** @var array $command */
            foreach ($parsed[self::TEST_KEY_DIRECTORY_EXISTS] as $command) {
                $cmd = new DirectoryExistsTest(
                    $this->getValue('name', $command, ''),
                    $this->getValue('directory', $command, ''),
                    (bool)$this->getValue('expected', $command, '0')
                );
                $tests[] = $cmd;
            }
        }

        if (array_key_exists(self::TEST_KEY_PHP_INI, $parsed)) {

            /** @var array $command */
            foreach ($parsed[self::TEST_KEY_PHP_INI] as $command) {
                $cmd = new PhpIniTest(
                    $this->getValue('name', $command, ''),
                    $this->getValue('setting', $command, ''),
                    $this->getValue('value', $command, ''),
                    $this->getValue('not_value', $command, '')
                );
                $tests[] = $cmd;
            }
        }

        if (array_key_exists(self::TEST_KEY_PHP_MODULE, $parsed)) {

            /** @var array $command */
            foreach ($parsed[self::TEST_KEY_PHP_MODULE] as $command) {
                $cmd = new PhpModuleTest(
                    $this->getValue('name', $command, ''),
                    $this->getValue('module', $command, '')
                );
                $tests[] = $cmd;
            }
        }

        if (array_key_exists(self::TEST_KEY_FILE_PERMISSION, $parsed)) {

            /** @var array $command */
            foreach ($parsed[self::TEST_KEY_FILE_PERMISSION] as $command) {
                $cmd = new FilePermissionTest(
                    $this->getValue('name', $command, ''),
                    $this->getValue('file', $command, ''),
                    $this->getValue('expected', $command, ''),
                    $this->getValue('not_expected', $command, '')
                );
                $tests[] = $cmd;
            }
        }

        if (array_key_exists(self::TEST_KEY_FILE_CONTENT, $parsed)) {

            /** @var array $command */
            foreach ($parsed[self::TEST_KEY_FILE_CONTENT] as $command) {
                $cmd = new FileContentTest(
                    $this->getValue('name', $command, ''),
                    $this->getValue('file', $command, ''),
                    $this->getValue('expected', $command, ''),
                    $this->getValue('not_expected', $command, '')
                );
                $tests[] = $cmd;
            }
        }

        return $tests;
    }

    /**
     * @param string $key
     * @param array $struct
     * @param string $default
     * @return string
     */
    private function getValue(string $key, array $struct, string $default): string
    {
        if (!isset($struct[$key])) {
            return $default;
        }

        return (string)$struct[$key];
    }

}
