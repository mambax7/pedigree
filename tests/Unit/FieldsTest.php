<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use XoopsModules\Pedigree\Fields;

final class FieldsTest extends TestCase
{
    public function testBooleanAccessorsReflectPersistedConfiguration(): void
    {
        $field = new Fields();
        $field->setVar('id', 7);
        $field->setVar('fieldname', 'colour');
        $field->setVar('isactive', 1);
        $field->setVar('viewinadvanced', 0);
        $field->setVar('hassearch', 1);
        $field->setVar('litter', 1);
        $field->setVar('generallitter', 0);
        $field->setVar('lookuptable', 1);
        $field->setVar('viewinlist', 1);
        $field->setVar('viewinpedigree', 1);
        $field->setVar('viewinpie', 0);

        self::assertSame('colour', (string)$field, 'Casting should return the configured field name.');
        self::assertSame(7, $field->getId());
        self::assertTrue($field->isActive());
        self::assertFalse($field->inAdvanced());
        self::assertTrue($field->hasSearch());
        self::assertTrue($field->addLitter());
        self::assertFalse($field->generalLitter());
        self::assertTrue($field->hasLookup());
        self::assertTrue($field->inList());
        self::assertTrue($field->inPedigree());
        self::assertFalse($field->inPie());
    }

    public function testIdentifierFallsBackToZeroWhenUnset(): void
    {
        $field = new Fields();
        self::assertSame(0, $field->getId());

        $field->setVar('id', 13);
        self::assertSame(13, $field->getId());
    }
}
