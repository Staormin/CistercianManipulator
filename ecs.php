<?php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\UselessOverridingMethodSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Functions\FunctionCallArgumentSpacingSniff;
use PHP_CodeSniffer\Standards\PEAR\Sniffs\WhiteSpace\ObjectOperatorIndentSniff;
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\ArrayNotation\NoMultilineWhitespaceAroundDoubleArrowFixer;
use PhpCsFixer\Fixer\AttributeNotation\OrderedAttributesFixer;
use PhpCsFixer\Fixer\CastNotation\CastSpacesFixer;
use PhpCsFixer\Fixer\CastNotation\ModernizeTypesCastingFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\ClassNotation\FinalClassFixer;
use PhpCsFixer\Fixer\ClassNotation\NoBlankLinesAfterClassOpeningFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Fixer\ClassNotation\SingleTraitInsertPerStatementFixer;
use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer;
use PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer;
use PhpCsFixer\Fixer\FunctionNotation\UseArrowFunctionsFixer;
use PhpCsFixer\Fixer\FunctionNotation\VoidReturnFixer;
use PhpCsFixer\Fixer\Import\GlobalNamespaceImportFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\SingleLineAfterImportsFixer;
use PhpCsFixer\Fixer\NamespaceNotation\BlankLinesBeforeNamespaceFixer;
use PhpCsFixer\Fixer\Phpdoc\AlignMultilineCommentFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocIndentFixer;
use PhpCsFixer\Fixer\ReturnNotation\ReturnAssignmentFixer;
use PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer;
use PhpCsFixer\Fixer\Whitespace\BlankLineBeforeStatementFixer;
use PhpCsFixer\Fixer\Whitespace\MethodChainingIndentationFixer;
use PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer;
use SlevomatCodingStandard\Sniffs\Attributes\AttributeAndTargetSpacingSniff;
use SlevomatCodingStandard\Sniffs\Classes\ClassMemberSpacingSniff;
use SlevomatCodingStandard\Sniffs\Classes\ConstantSpacingSniff;
use SlevomatCodingStandard\Sniffs\Classes\EmptyLinesAroundClassBracesSniff;
use SlevomatCodingStandard\Sniffs\Classes\EnumCaseSpacingSniff;
use SlevomatCodingStandard\Sniffs\Classes\MethodSpacingSniff;
use SlevomatCodingStandard\Sniffs\Classes\ModernClassNameReferenceSniff;
use SlevomatCodingStandard\Sniffs\Classes\RequireConstructorPropertyPromotionSniff;
use SlevomatCodingStandard\Sniffs\Classes\RequireSelfReferenceSniff;
use SlevomatCodingStandard\Sniffs\Commenting\DocCommentSpacingSniff;
use SlevomatCodingStandard\Sniffs\Commenting\UselessInheritDocCommentSniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\AssignmentInConditionSniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\BlockControlStructureSpacingSniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\EarlyExitSniff;
use SlevomatCodingStandard\Sniffs\Functions\RequireMultiLineCallSniff;
use SlevomatCodingStandard\Sniffs\Functions\RequireTrailingCommaInDeclarationSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\AlphabeticallySortedUsesSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\ReferenceUsedNamesOnlySniff;
use SlevomatCodingStandard\Sniffs\Namespaces\UnusedUsesSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\UseFromSameNamespaceSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\UselessAliasSniff;
use SlevomatCodingStandard\Sniffs\PHP\ShortListSniff;
use SlevomatCodingStandard\Sniffs\PHP\UselessParenthesesSniff;
use SlevomatCodingStandard\Sniffs\PHP\UselessSemicolonSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\DisallowArrayTypeHintSyntaxSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\NullableTypeForNullDefaultValueSniff;
use SlevomatCodingStandard\Sniffs\Variables\UnusedVariableSniff;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return ECSConfig::configure()
    ->withPaths([__DIR__ . '/config', __DIR__ . '/public', __DIR__ . '/bin', __DIR__ . '/src'])
    ->withSets(
        [
            SetList::PSR_12,
            SetList::CLEAN_CODE,
            SetList::ARRAY,
            SetList::DOCTRINE_ANNOTATIONS,
            SetList::PHPUNIT,
        ]
    )
    ->withRules(
        [
            AlphabeticallySortedUsesSniff::class,
            AlignMultilineCommentFixer::class,
            BlankLineBeforeStatementFixer::class,
            CastSpacesFixer::class,
            FunctionCallArgumentSpacingSniff::class,
            MethodChainingIndentationFixer::class,
            NoBlankLinesAfterClassOpeningFixer::class,
            NoExtraBlankLinesFixer::class,
            NoMultilineWhitespaceAroundDoubleArrowFixer::class,
            NoUnusedImportsFixer::class,
            ObjectOperatorIndentSniff::class,
            PhpdocIndentFixer::class,
            ReturnAssignmentFixer::class,
            BlankLinesBeforeNamespaceFixer::class,
            SingleLineAfterImportsFixer::class,
            SingleQuoteFixer::class,
            SingleTraitInsertPerStatementFixer::class,
            TrailingCommaInMultilineFixer::class,
            UseArrowFunctionsFixer::class,
            UselessInheritDocCommentSniff::class,
            UselessOverridingMethodSniff::class,
            UselessSemicolonSniff::class,
            VisibilityRequiredFixer::class,
            VoidReturnFixer::class,
            YodaStyleFixer::class,
            ModernizeTypesCastingFixer::class,
            OrderedClassElementsFixer::class,
            FinalClassFixer::class,
            ModernClassNameReferenceSniff::class,
            AttributeAndTargetSpacingSniff::class,
            ClassMemberSpacingSniff::class,
            EnumCaseSpacingSniff::class,
            RequireConstructorPropertyPromotionSniff::class,
            RequireSelfReferenceSniff::class,
            DocCommentSpacingSniff::class,
            AssignmentInConditionSniff::class,
            BlockControlStructureSpacingSniff::class,
            EarlyExitSniff::class,
            RequireTrailingCommaInDeclarationSniff::class,
            UseFromSameNamespaceSniff::class,
            UselessAliasSniff::class,
            ShortListSniff::class,
            UselessParenthesesSniff::class,
            DisallowArrayTypeHintSyntaxSniff::class,
            NullableTypeForNullDefaultValueSniff::class,
            UnusedVariableSniff::class,
            OrderedAttributesFixer::class,
            RequireMultiLineCallSniff::class,
        ]
    )
    ->withConfiguredRule(UnusedUsesSniff::class, [
        'searchAnnotations' => true,
    ])
    ->withConfiguredRule(ReferenceUsedNamesOnlySniff::class, [
        'searchAnnotations' => true,
    ])
    ->withConfiguredRule(MethodSpacingSniff::class, [
        'minLinesCount' => 1,
        'maxLinesCount' => 1,
    ])
    ->withConfiguredRule(
        EnumCaseSpacingSniff::class,
        [
            'minLinesCountBeforeWithComment' => 0,
            'maxLinesCountBeforeWithComment' => 0,
            'minLinesCountBeforeWithoutComment' => 0,
            'maxLinesCountBeforeWithoutComment' => 0,
        ]
    )
    ->withConfiguredRule(
        EmptyLinesAroundClassBracesSniff::class,
        [
            'linesCountAfterOpeningBrace' => 0,
            'linesCountBeforeClosingBrace' => 0,
        ]
    )
    ->withConfiguredRule(
        ConstantSpacingSniff::class,
        [
            'minLinesCountBeforeWithComment' => 0,
            'maxLinesCountBeforeWithComment' => 0,
            'minLinesCountBeforeWithoutComment' => 0,
            'maxLinesCountBeforeWithoutComment' => 0,
        ]
    )
    ->withConfiguredRule(ArraySyntaxFixer::class, [
        'syntax' => 'short',
    ])
    ->withConfiguredRule(
        ClassAttributesSeparationFixer::class,
        [
            'elements' => [
                'const' => 'none',
                'property' => 'one',
            ],
        ]
    )
    ->withConfiguredRule(
        GlobalNamespaceImportFixer::class,
        [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ]
    )
    ->withConfiguredRule(
        LineLengthFixer::class,
        [
            LineLengthFixer::BREAK_LONG_LINES => true,
            LineLengthFixer::INLINE_SHORT_LINES => false,
            LineLengthFixer::LINE_LENGTH => 120,
        ]
    )
    ->withRootFiles()
    ->withSpacing(indentation: Option::INDENTATION_SPACES, lineEnding: PHP_EOL)
    ->withParallel();
