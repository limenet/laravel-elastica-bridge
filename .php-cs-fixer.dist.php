<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$rules = [
    '@Symfony' => true,
    '@Symfony:risky' => true,
    '@PHP80Migration' => true,
    '@PHP80Migration:risky' => true,
    '@PSR12:risky' => true,
    'array_indentation' => true,
    'heredoc_to_nowdoc' => true,
    'increment_style' => ['style' => 'post'],
    'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
    'native_constant_invocation' => false,
    'native_function_invocation' => false,
    'no_empty_comment' => false,
    'no_useless_return' => true,
    'nullable_type_declaration_for_default_null_value' => true,
    'protected_to_private' => false,
    'simplified_null_return' => false, // disabled by Shift
    'use_arrow_functions' => true,
    'yoda_style' => false,
    'php_unit_method_casing' => ['case' => 'snake_case']
];

$project_path = getcwd();
$finder = Finder::create()
    ->in([
        $project_path . '/src',
        $project_path . '/tests',
    ])
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new Config())
    ->setFinder($finder)
    ->setRules($rules)
    ->setRiskyAllowed(true)
    ->setUsingCache(true);
