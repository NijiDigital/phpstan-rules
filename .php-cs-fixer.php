<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$finder = (new Finder())
    ->in([
        __DIR__.'/src',
        __DIR__.'/tests',
    ]);

return (new Config())
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setRules([
        'declare_strict_types' => true,
        'new_with_braces' => true,
        'ordered_class_elements' => true,
        'ordered_imports' => true,
        'phpdoc_align' => true,
        'phpdoc_indent' => true,
        'phpdoc_to_comment' => false,
        'align_multiline_comment' => ['comment_type' => 'phpdocs_only'],
        'concat_space' => ['spacing' => 'one'],
        'return_type_declaration' => true,
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
        ],
        'types_spaces' => [
            'space' => 'single',
        ],
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder);
