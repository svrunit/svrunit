<?php

namespace SVRUnit\Services\TestParser;


use SVRUnit\Components\Tests\Adapters\CommandTest;
use SVRUnit\Components\Tests\Adapters\DirectoryExistsTest;
use SVRUnit\Components\Tests\Adapters\FileContentTest;
use SVRUnit\Components\Tests\Adapters\FileExistsTest;
use SVRUnit\Components\Tests\Adapters\FilePermissionTest;
use SVRUnit\Components\Tests\Adapters\PhpIniTest;
use SVRUnit\Components\Tests\Adapters\PhpModuleTest;
use Symfony\Component\Yaml\Parser;

class TestSpecFileParser
{

    const TEST_KEY_COMMANDS = "commands";
    const TEST_KEY_PHP_INI = "php_ini";
    const TEST_KEY_PHP_MODULE = "php_module";
    const TEST_KEY_FILE_EXISTS = "file_exists";
    const TEST_KEY_FILE_CONTENT = "file_content";
    const TEST_KEY_FILE_PERMISSION = "file_permission";
    const TEST_KEY_DIRECTORY_EXISTS = "directory_exists";


    /**
     * @param string $testsFile
     * @return array<mixed>
     */
    public function parse(string $testsFile): array
    {
        $parser = new Parser();

        $parsed = $parser->parse((string)file_get_contents($testsFile));

        return $this->parseTests(basename($testsFile), $parsed);
    }


    /**
     * @param string $testFile
     * @param array<mixed> $parsed
     * @return array<mixed>
     */
    private function parseTests(string $testFile, array $parsed): array
    {
        $tests = [];

        if (array_key_exists(self::TEST_KEY_COMMANDS, $parsed)) {

            /** @var array<mixed> $command */
            foreach ($parsed[self::TEST_KEY_COMMANDS] as $command) {
                $cmd = new CommandTest(
                    $this->getValue('name', $command, ''),
                    $testFile,
                    $this->getValue('command', $command, ''),
                    $this->getValue('expected', $command, ''),
                    $this->getArray('expected_and', $command, []),
                    $this->getArray('expected_or', $command, []),
                    $this->getValue('not_expected', $command, ''),
                    $this->getValue('setup', $command, ''),
                    $this->getValue('teardown', $command, '')
                );
                $tests[] = $cmd;
            }
        }

        if (array_key_exists(self::TEST_KEY_FILE_EXISTS, $parsed)) {

            /** @var array<mixed> $command */
            foreach ($parsed[self::TEST_KEY_FILE_EXISTS] as $command) {
                $cmd = new FileExistsTest(
                    $this->getValue('name', $command, ''),
                    $testFile,
                    $this->getValue('file', $command, ''),
                    (bool)$this->getValue('expected', $command, '0')
                );
                $tests[] = $cmd;
            }
        }

        if (array_key_exists(self::TEST_KEY_DIRECTORY_EXISTS, $parsed)) {

            /** @var array<mixed> $command */
            foreach ($parsed[self::TEST_KEY_DIRECTORY_EXISTS] as $command) {
                $cmd = new DirectoryExistsTest(
                    $this->getValue('name', $command, ''),
                    $testFile,
                    $this->getValue('directory', $command, ''),
                    (bool)$this->getValue('expected', $command, '0')
                );
                $tests[] = $cmd;
            }
        }

        if (array_key_exists(self::TEST_KEY_PHP_INI, $parsed)) {

            /** @var array<mixed> $command */
            foreach ($parsed[self::TEST_KEY_PHP_INI] as $command) {
                $cmd = new PhpIniTest(
                    $this->getValue('name', $command, ''),
                    $testFile,
                    $this->getValue('setting', $command, ''),
                    $this->getValue('value', $command, ''),
                    $this->getValue('not_value', $command, '')
                );
                $tests[] = $cmd;
            }
        }

        if (array_key_exists(self::TEST_KEY_PHP_MODULE, $parsed)) {

            /** @var array<mixed> $command */
            foreach ($parsed[self::TEST_KEY_PHP_MODULE] as $command) {
                $cmd = new PhpModuleTest(
                    $this->getValue('name', $command, ''),
                    $testFile,
                    $this->getValue('module', $command, '')
                );
                $tests[] = $cmd;
            }
        }

        if (array_key_exists(self::TEST_KEY_FILE_PERMISSION, $parsed)) {

            /** @var array<mixed> $command */
            foreach ($parsed[self::TEST_KEY_FILE_PERMISSION] as $command) {
                $cmd = new FilePermissionTest(
                    $this->getValue('name', $command, ''),
                    $testFile,
                    $this->getValue('file', $command, ''),
                    $this->getValue('expected', $command, ''),
                    $this->getValue('not_expected', $command, '')
                );
                $tests[] = $cmd;
            }
        }

        if (array_key_exists(self::TEST_KEY_FILE_CONTENT, $parsed)) {

            /** @var array<mixed> $command */
            foreach ($parsed[self::TEST_KEY_FILE_CONTENT] as $command) {
                $cmd = new FileContentTest(
                    $this->getValue('name', $command, ''),
                    $testFile,
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
     * @param array<mixed>|null $struct
     * @param string $default
     * @return string
     */
    private function getValue(string $key, ?array $struct, string $default): string
    {
        if ($struct === null) {
            return $default;
        }

        if (!isset($struct[$key])) {
            return $default;
        }

        return (string)$struct[$key];
    }

    /**
     * @param string $key
     * @param array<mixed>|null $struct
     * @param array<mixed> $default
     * @return array<mixed>
     */
    private function getArray(string $key, ?array $struct, array $default): array
    {
        if ($struct === null) {
            return $default;
        }

        if (!isset($struct[$key])) {
            return $default;
        }

        return (array)$struct[$key];
    }

}
