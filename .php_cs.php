<?php
/*
 * This document has been generated with
 * https://mlocati.github.io/php-cs-fixer-configurator/#version:2.16.1|configurator
 * you can change this configuration by importing this file.
 */

$fullRuleset = [
    // There MUST be one blank line after the namespace declaration.
    'blank_line_after_namespace' => true,
    // Whitespace around the keywords of a class, trait or interfaces definition should be one space.
    'class_definition' => true,
    // The PHP constants `true`, `false`, and `null` MUST be written using the correct casing.
    'constant_case' => true,
    // The keyword `elseif` should be used instead of `else if` so that all control keywords look like single words.
    'elseif' => true,
    // PHP code MUST use only UTF-8 without BOM (remove BOM).
    'encoding' => true,
    // PHP code must use the long `<?php` tags or short-echo `<?=` tags and not other tag variations.
    'full_opening_tag' => true,
    // Spaces should be properly placed in a function declaration.
    'function_declaration' => true,
    // All PHP files must use same line ending.
    'line_ending' => true,
    // PHP keywords MUST be in lower case.
    'lowercase_keywords' => true,
    // In method arguments and method call, there MUST NOT be a space before each comma and there MUST be one space after each comma. Argument lists MAY be split across multiple lines, where each subsequent line is indented once. When doing so, the first item in the list MUST be on the next line, and there MUST be only one argument per line.
    'method_argument_space' => true,
    // There must be a comment when fall-through is intentional in a non-empty case body.
    'no_break_comment' => true,
    // The closing `? >` tag MUST be omitted from files containing only PHP.
    'no_closing_tag' => true,
    // When making a method or function call, there MUST NOT be a space between the method or function name and the opening parenthesis.
    'no_spaces_after_function_name' => true,
    // There MUST NOT be a space after the opening parenthesis. There MUST NOT be a space before the closing parenthesis.
    'no_spaces_inside_parenthesis' => true,
    // Remove trailing whitespace at the end of non-blank lines.
    'no_trailing_whitespace' => true,
    // There MUST be no trailing spaces inside comment or PHPDoc.
    'no_trailing_whitespace_in_comment' => true,
    // A PHP file without end tag must always end with a single empty line feed.
    'single_blank_line_at_eof' => true,
    // There MUST NOT be more than one property or constant declared per statement.
    'single_class_element_per_statement' => true,
    // Each namespace use MUST go on its own line and there MUST be one blank line after the use statements block.
    'single_line_after_imports' => true,
    // A case should be followed by a colon and not a semicolon.
    'switch_case_semicolon_to_colon' => true,
    // Removes extra spaces between colon and case value.
    'switch_case_space' => true,
    // Visibility MUST be declared on all properties and methods; `abstract` and `final` MUST be declared before the visibility; `static` MUST be declared after the visibility.
    'visibility_required' => true,
    // Doctrine annotations must use configured operator for assignment in arrays.
    'doctrine_annotation_array_assignment' => true,
    // Doctrine annotations without arguments must use the configured syntax.
    'doctrine_annotation_braces' => true,
    // Doctrine annotations must be indented with four spaces.
    'doctrine_annotation_indentation' => true,
    // Fixes spaces in Doctrine annotations.
    'doctrine_annotation_spaces' => ['after_argument_assignments'=>false],
    // Concatenation should be spaced according configuration.
    'concat_space' => ['spacing'=>'one'],
    // Comments with annotation should be docblock when used on structural elements.
    'comment_to_phpdoc' => true,
    // Replace deprecated `ereg` regular expression functions with `preg`.
    'ereg_to_preg' => true,
    // Error control operator should be added to deprecation notices and/or removed from other cases.
    'error_suppression' => true,
    // Internal classes should be `final`.
    'final_internal_class' => true,
    // Order the flags in `fopen` calls, `b` and `t` must be last.
    'fopen_flag_order' => true,
    // Replace core functions calls returning constants with the constants.
    'function_to_constant' => true,
    // Function `implode` must be called with 2 arguments in the documented order.
    'implode_call' => true,
    // Replaces `is_null($var)` expression with `null === $var`.
    'is_null' => true,
    // Use `&&` and `||` logical operators instead of `and` and `or`.
    'logical_operators' => true,
    // Replaces `intval`, `floatval`, `doubleval`, `strval` and `boolval` function calls with according type casting operator.
    'modernize_types_casting' => true,
    // Add leading `\` before constant invocation of internal constant to speed up resolving. Constant name match is case-sensitive, except for `null`, `false` and `true`.
    'native_constant_invocation' => true,
    // Add leading `\` before function invocation to speed up resolving.
    'native_function_invocation' => true,
    // Master functions shall be used instead of aliases.
    'no_alias_functions' => true,
    // Replace accidental usage of homoglyphs (non ascii characters) in names.
    'no_homoglyph_names' => true,
    // In function arguments there must not be arguments with default values before non-default ones.
    'no_unreachable_default_argument_value' => true,
    // Properties should be set to `null` instead of using `unset`.
    'no_unset_on_property' => true,
    // Remove Zero-width space (ZWSP), Non-breaking space (NBSP) and other invisible unicode symbols.
    'non_printable_character' => true,
    // PHPUnit assertion method calls like `->assertSame(true, $foo)` should be written with dedicated method like `->assertTrue($foo)`.
    'php_unit_construct' => true,
    // Usage of PHPUnit's mock e.g. `->will($this->returnValue(..))` must be replaced by its shorter equivalent such as `->willReturn(...)`.
    'php_unit_mock_short_will_return' => true,
    // Changes the visibility of the `setUp()` and `tearDown()` functions of PHPUnit to `protected`, to match the PHPUnit TestCase.
    'php_unit_set_up_tear_down_visibility' => true,
    // Class names should match the file name.
    'psr4' => true,
    // Cast shall be used, not `settype`.
    'set_type_to_cast' => true,
    // Comparisons should be strict.
    'strict_comparison' => true,
    // Functions should be used with `$strict` param set to `true`.
    'strict_param' => true,
    // All multi-line strings must use correct line ending.
    'string_line_ending' => true,
    // Calls to `PHPUnit\Framework\TestCase` static methods must all be of the same type, either `$this->`, `self::` or `static::`.
    'php_unit_test_case_static_method_calls' => true,
    // Binary operators should be surrounded by space as configured.
    'binary_operator_spaces' => true,
    // An empty line feed must precede any configured statement.
    'blank_line_before_statement' => true,
    // The body of each structure MUST be enclosed by braces. Braces should be properly placed. Body of braces should be properly indented.
    'braces' => ['allow_single_line_closure'=>true],
    // A single space or none should be between cast and variable.
    'cast_spaces' => true,
    // Equal sign in declare statement should be surrounded by spaces or not following configuration.
    'declare_equal_normalize' => true,
    // Ensure single space between function's argument and its typehint.
    'function_typehint_space' => true,
    // Include/Require and file path should be divided with a single space. File path should not be placed under brackets.
    'include' => true,
    // Code MUST use configured indentation type.
    'indentation_type' => true,
    // Cast should be written in lower case.
    'lowercase_cast' => true,
    // Class static references `self`, `static` and `parent` MUST be in lower case.
    'lowercase_static_reference' => true,
    // Magic constants should be referred to using the correct casing.
    'magic_constant_casing' => true,
    // Magic method definitions and calls must be using the correct casing.
    'magic_method_casing' => true,
    // Function defined by PHP should be called using the correct casing.
    'native_function_casing' => true,
    // Native type hints for functions should use the correct case.
    'native_function_type_declaration_casing' => true,
    // There should not be any empty comments.
    'no_empty_comment' => true,
    // There should not be empty PHPDoc blocks.
    'no_empty_phpdoc' => true,
    // Remove useless semicolon statements.
    'no_empty_statement' => true,
    // Remove leading slashes in `use` clauses.
    'no_leading_import_slash' => true,
    // A final class must not have final methods.
    'no_unneeded_final_method' => true,
    // Removes unneeded curly braces that are superfluous and aren't part of a control structure's body.
    'no_unneeded_curly_braces' => true,
    // Removes unneeded parentheses around control statements.
    'no_unneeded_control_parentheses' => true,
    // Unused `use` statements must be removed.
    'no_unused_imports' => true,
    // In array declaration, there MUST NOT be a whitespace before each comma.
    'no_whitespace_before_comma_in_array' => true,
    // Remove trailing whitespace at the end of blank lines.
    'no_whitespace_in_blank_line' => true,
    // Array index should always be written by using square braces.
    'normalize_index_brace' => true,
    // Remove trailing commas in list function calls.
    'no_trailing_comma_in_list_call' => true,
    // PHP single-line arrays should not have trailing comma.
    'no_trailing_comma_in_singleline_array' => true,
    // Short cast `bool` using double exclamation mark should not be used.
    'no_short_bool_cast' => true,
    // Each element of an array must be indented exactly once.
    'array_indentation' => true,
    // Converts backtick operators to `shell_exec` calls.
    'backtick_to_shell_exec' => true,
    // Class, trait and interface elements must be separated with one blank line.
    'class_attributes_separation' => true,
    // Using `isset($var) &&` multiple times should be done in one call.
    'combine_consecutive_issets' => true,
    // Calling `unset` on multiple items should be done in one call.
    'combine_consecutive_unsets' => true,
    // Replace multiple nested calls of `dirname` by only one call with second `$level` parameter. Requires PHP >= 7.0.
    'combine_nested_dirname' => true,
    // Force strict types declaration in all files. Requires PHP >= 7.0.
    'declare_strict_types' => false,
    // Replaces `dirname(__FILE__)` expression with equivalent `__DIR__` constant.
    'dir_constant' => true,
    // Add curly braces to indirect variables to make them clear to understand. Requires PHP >= 7.0.
    'explicit_indirect_variable' => true,
    // List (`array` destructuring) assignment should be declared using the configured syntax. Requires PHP >= 7.1.
    'list_syntax' => ['syntax'=>'short'],
    // DocBlocks must start with two asterisks, multiline comments must start with a single asterisk, after the opening slash. Both must end with a single asterisk before the closing slash.
    'multiline_comment_opening_closing' => true,
    // Forbid multi-line whitespace before the closing semicolon or move the semicolon to the new line for chained calls.
    'multiline_whitespace_before_semicolons' => ['strategy'=>'new_line_for_chained_calls'],
    // Replace control structure alternative syntax to use braces.
    'no_alternative_syntax' => true,
    // There should not be a binary flag before strings.
    'no_binary_string' => true,
    // Either language construct `print` or `echo` should be used.
    'no_mixed_echo_print' => true,
    // The namespace declaration line shouldn't contain leading whitespace.
    'no_leading_namespace_whitespace' => true,
    // Properties MUST not be explicitly initialized with `null` except when they have a type declaration (PHP 7.4).
    'no_null_property_initialization' => true,
    // Convert PHP4-style constructors to `__construct`.
    'no_php4_constructor' => true,
    // Replace short-echo `<?=` with long format `<?php echo` syntax.
    'no_short_echo_tag' => true,
    // Single-line whitespace before closing semicolon are prohibited.
    'no_singleline_whitespace_before_semicolons' => true,
    // There MUST NOT be spaces around offset braces.
    'no_spaces_around_offset' => true,
    // Removes `@param` and `@return` tags that don't provide any useful information.
    'no_superfluous_phpdoc_tags' => ['allow_mixed'=>true],
    // Variables must be set `null` instead of using `(unset)` casting.
    'no_unset_cast' => true,
    // There should not be an empty `return` statement at the end of a function.
    'no_useless_return' => true,
    // There should not be useless `else` cases.
    'no_useless_else' => true,
    // There should not be space before or after object `T_OBJECT_OPERATOR` `->`.
    'object_operator_without_whitespace' => true,
    // A return statement wishing to return `void` should not return `null`.
    'simplified_null_return' => true,
    // Inside a `final` class or anonymous class `self` should be preferred to `static`.
    'self_static_accessor' => true,
    // Changes doc blocks from single to multi line, or reversed. Works for class constants, properties and methods only.
    'phpdoc_line_span' => ['const'=>'single'],
    // Use `null` coalescing operator `??` where possible. Requires PHP >= 7.0.
    'ternary_to_null_coalescing' => true,
    // Replaces `rand`, `srand`, `getrandmax` functions calls with their `mt_*` analogs.
    'random_api_migration' => true,
    // Usages of `->setExpectedException*` methods MUST be replaced by `->expectException*` methods.
    'php_unit_expectation' => true,
    // Usages of `->getMock` and `->getMockWithoutInvokingTheOriginalConstructor` methods MUST be replaced by `->createMock` or `->createPartialMock` methods.
    'php_unit_mock' => true,
    // PHPUnit classes MUST be used in namespaced version, e.g. `\PHPUnit\Framework\TestCase` instead of `\PHPUnit_Framework_TestCase`.
    'php_unit_namespaced' => true,
    // Usages of `@expectedException*` annotations MUST be replaced by `->setExpectedException*` methods.
    'php_unit_no_expectation_annotation' => true,
    // Order `@covers` annotation of PHPUnit tests.
    'php_unit_ordered_covers' => true,
    // PHPUnit methods like `assertSame` should be used instead of `assertEquals`.
    'php_unit_strict' => true,
    // Adds a default `@coversNothing` annotation to PHPUnit test classes that have no `@covers*` annotation.
    'php_unit_test_class_requires_covers' => true,
    // PHPDoc should contain `@param` for all params.
    'phpdoc_add_missing_param_annotation' => true,
    // `@access` annotations should be omitted from PHPDoc.
    'phpdoc_no_access' => true,
    // `@package` and `@subpackage` annotations should be omitted from PHPDoc.
    'phpdoc_no_package' => true,
    // Scalar types should always be written in the same form. `int` not `integer`, `bool` not `boolean`, `float` not `real` or `double`.
    'phpdoc_scalar' => true,
    // Annotations in PHPDoc should be grouped together so that annotations of the same type immediately follow each other, and annotations of a different type are separated by a single blank line.
    'phpdoc_separation' => true,
    // Single line `@var` PHPDoc should have proper spacing.
    'phpdoc_single_line_var_spacing' => true,
    // `@var` and `@type` annotations should not contain the variable name.
    'phpdoc_var_without_name' => true,
    // Arrays should be formatted like function/method arguments, without leading or trailing single line space.
    'trim_array_spaces' => true,
    // Write conditions in Yoda style (`true`), non-Yoda style (`false`) or ignore those conditions (`null`) based on configuration.
    'yoda_style' => ['equal'=>null],
    // In array declaration, there MUST be a whitespace after each comma.
    'whitespace_after_comma_in_array' => true,
    // Standardize spaces around ternary operator.
    'ternary_operator_spaces' => true,
    // Replace all `<>` with `!=`.
    'standardize_not_equals' => true,
    // Fix whitespace after a semicolon.
    'space_after_semicolon' => true,
    // Convert double quotes to single quotes for simple strings.
    'single_quote' => true,
    // Local, dynamic and directly referenced variables should not be assigned and directly returned by a function or method.
    'return_assignment' => true,
    // PHP arrays should be declared using the configured syntax.
    'array_syntax' => ['syntax'=>'short'],
    // PHPDoc annotation descriptions should not be a sentence.
    'phpdoc_annotation_without_dot' => true,
    // Docblocks should have the same indentation as the documented subject.
    'phpdoc_indent' => true,
    // EXPERIMENTAL: Takes `@param` annotations of non-mixed types and adjusts accordingly the function signature. Requires PHP >= 7.0.
    'phpdoc_to_param_type' => true,
    // EXPERIMENTAL: Takes `@return` annotation of non-mixed types and adjusts accordingly the function signature. Requires PHP >= 7.0.
    'phpdoc_to_return_type' => true,
    // `@var` and `@type` annotations must have type and name in the correct order.
    'phpdoc_var_annotation_correct_order' => true,
    // Sorts PHPDoc types.
    'phpdoc_types_order' => ['null_adjustment'=>'always_first'],
    // The correct case must be used for standard PHP types in PHPDoc.
    'phpdoc_types' => true,
    // Removes extra blank lines after summary and after description in PHPDoc.
    'phpdoc_trim_consecutive_blank_line_separation' => true,
    // PHPDoc should start and end with content, excluding the very first and last line of the docblocks.
    'phpdoc_trim' => true,
    // All items of the given phpdoc tags must be either left-aligned or (by default) aligned vertically.
    'phpdoc_align' => [
        'align' => 'vertical',
        'tags' => [
            'method',
            'param',
            'property',
            'return',
            'throws',
            'type',
            'var',
        ],
    ],
];

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules($fullRuleset)
    ->setFinder(PhpCsFixer\Finder::create()
        ->exclude('vendor')
        ->in(__DIR__ . '/src/')
        ->path(__DIR__ . '/tests/')
    )
;
