<?php

return (new PhpCsFixer\Config())
    ->setUsingCache(false)
    ->setUnsupportedPhpVersionAllowed(true)
    ->setRules([
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => true,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude(['./src/vendor'])
            ->in(__DIR__ . '/src')
    );