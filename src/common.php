<?php

class AppManager
{

    /**
     * @param array $arguments
     */
    public static function run(array $arguments)
    {
        $cur_dir = explode('\\', getcwd());
        $workingDir = $cur_dir[count($cur_dir) - 1];

        $config = new SVRUnit\Components\Launcher\Launcher($arguments);
        $config->load();

        $options = $config->getOptions();

        echo "SVRUnit Testing Framework, v" . \SVRUnit\SVRUnit::VERSION . PHP_EOL;
        echo "Copyright (c) 2021 Christian Dangl" . PHP_EOL;
        echo "www.svrunit.com" . PHP_EOL;
        echo PHP_EOL;

        if ($options->isShowVersion()) {
            # we only show the version above
            # so lets quit
            return;
        }

        $configAbsolutePath = (!empty($options->getConfigurationFile())) ? $workingDir . '/' . $options->getConfigurationFile() : '';

        echo "Configuration: " . $configAbsolutePath . PHP_EOL;
        echo PHP_EOL;

        $runner = new SVRUnit\SVRUnit($configAbsolutePath);

        $runner->run(
            $options->isDebugMode()
        );

    }

}
