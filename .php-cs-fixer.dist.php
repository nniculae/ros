<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests'
    ])
    ->exclude('var');

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PHP80Migration:risky' => true,
        '@PHP81Migration' => true,
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
    ])
    ->setFinder($finder);
