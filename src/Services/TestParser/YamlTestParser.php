<?php

namespace SVRUnit\Services\TestParser;


use SVRUnit\Components\Tests\Adapters\CommandTest;
use SVRUnit\Components\Tests\Adapters\DirectoryExistsTest;
use SVRUnit\Components\Tests\Adapters\FileContentTest;
use SVRUnit\Components\Tests\Adapters\FileExistsTest;
use SVRUnit\Components\Tests\Adapters\FileNotExistsTest;
use SVRUnit\Components\Tests\Adapters\FilePermissionTest;
use SVRUnit\Components\Tests\Adapters\PhpIniTest;
use SVRUnit\Components\Tests\Adapters\PhpModuleTest;

class YamlTestParser
{

    const TEST_KEY_COMMANDS = "commands";
    const TEST_KEY_FILE_EXISTS = "file_exists";
    const TEST_KEY_FILE_NOT_EXISTS = "file_not_exists";
    const TEST_KEY_PHP_INI = "php_ini";
    const TEST_KEY_PHP_MODULE = "php_module";
    const TEST_KEY_FILE_PERMISSION = "file_permission";
    const TEST_KEY_FILE_CONTENT = "file_content";
    const TEST_KEY_DIRECTORY_EXISTS = "directory_exists";


    /**
     * @author Christian Dangl
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
     * @author Christian Dangl
     * @param $parsed
     * @return array
     */
    private function parseTests($parsed)
    {
        $tests = array();

        if (array_key_exists(self::TEST_KEY_COMMANDS, $parsed)) {

            /** @var array $command */
            foreach ($parsed[self::TEST_KEY_COMMANDS] as $command) {
                $cmd = new CommandTest($command['name'], $command['command'], $command['expected']);
                $tests[] = $cmd;
            }
        }

        if (array_key_exists(self::TEST_KEY_FILE_EXISTS, $parsed)) {

            /** @var array $command */
            foreach ($parsed[self::TEST_KEY_FILE_EXISTS] as $command) {
                $cmd = new FileExistsTest($command['name'], $command['file']);
                $tests[] = $cmd;
            }
        }

        if (array_key_exists(self::TEST_KEY_FILE_NOT_EXISTS, $parsed)) {

            /** @var array $command */
            foreach ($parsed[self::TEST_KEY_FILE_NOT_EXISTS] as $command) {
                $cmd = new FileNotExistsTest($command['name'], $command['file']);
                $tests[] = $cmd;
            }
        }

        if (array_key_exists(self::TEST_KEY_DIRECTORY_EXISTS, $parsed)) {

            /** @var array $command */
            foreach ($parsed[self::TEST_KEY_DIRECTORY_EXISTS] as $command) {
                $cmd = new DirectoryExistsTest($command['name'], $command['directory']);
                $tests[] = $cmd;
            }
        }

        if (array_key_exists(self::TEST_KEY_PHP_INI, $parsed)) {

            /** @var array $command */
            foreach ($parsed[self::TEST_KEY_PHP_INI] as $command) {
                $cmd = new PhpIniTest($command['name'], $command['setting'], $command['value']);
                $tests[] = $cmd;
            }
        }

        if (array_key_exists(self::TEST_KEY_PHP_MODULE, $parsed)) {

            /** @var array $command */
            foreach ($parsed[self::TEST_KEY_PHP_MODULE] as $command) {
                $cmd = new PhpModuleTest($command['name'], $command['module']);
                $tests[] = $cmd;
            }
        }

        if (array_key_exists(self::TEST_KEY_FILE_PERMISSION, $parsed)) {

            /** @var array $command */
            foreach ($parsed[self::TEST_KEY_FILE_PERMISSION] as $command) {
                $cmd = new FilePermissionTest($command['name'], $command['file'], $command['expected']);
                $tests[] = $cmd;
            }
        }

        if (array_key_exists(self::TEST_KEY_FILE_CONTENT, $parsed)) {

            /** @var array $command */
            foreach ($parsed[self::TEST_KEY_FILE_CONTENT] as $command) {
                $cmd = new FileContentTest($command['name'], $command['file'], $command['expected']);
                $tests[] = $cmd;
            }
        }

        return $tests;
    }

}
