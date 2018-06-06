<?php

namespace SixtyNine\Cloud\Tests\Builder;


use SixtyNine\Cloud\Builder\FiltersBuilder;
use SixtyNine\Cloud\Filters\Filters;
use SixtyNine\Cloud\Filters\ChangeCase;
use SixtyNine\Cloud\Filters\RemoveByLength;
use SixtyNine\Cloud\Filters\RemoveCharacters;
use SixtyNine\Cloud\Filters\RemoveNumbers;
use SixtyNine\Cloud\Filters\RemoveTrailingCharacters;
use PHPUnit\Framework\TestCase;

class FiltersBuilderTest extends TestCase
{
    public function testConstructor()
    {
        $filters = FiltersBuilder::create()
            ->setRemoveNumbers(false)
            ->setRemoveTrailing(false)
            ->setRemoveUnwanted(false)
            ->build()
        ;
        $this->assertInstanceOf(Filters::class, $filters);
        $this->assertEquals(array(), $filters->getFilters());
    }

    public function testSetCase()
    {
        $filters = FiltersBuilder::create()
            ->setRemoveNumbers(false)
            ->setRemoveTrailing(false)
            ->setRemoveUnwanted(false)
            ->setCase('uppercase')
            ->build()
        ;
        $this->assertInstanceOf(Filters::class, $filters);
        $this->assertInstanceOf(ChangeCase::class, $filters->getFilters()[0]);
        $this->assertAttributeEquals('uppercase', 'case', $filters->getFilters()[0]);
    }

    public function testRemoveNumbers()
    {
        $filters = FiltersBuilder::create()
            ->setRemoveNumbers(true)
            ->setRemoveTrailing(false)
            ->setRemoveUnwanted(false)
            ->build()
        ;
        $this->assertInstanceOf(Filters::class, $filters);
        $this->assertInstanceOf(RemoveNumbers::class, $filters->getFilters()[0]);
    }

    public function testRemoveTrailing()
    {
        $filters = FiltersBuilder::create()
            ->setRemoveNumbers(false)
            ->setRemoveTrailing(true)
            ->setRemoveUnwanted(false)
            ->build()
        ;
        $this->assertInstanceOf(Filters::class, $filters);
        $this->assertInstanceOf(RemoveTrailingCharacters::class, $filters->getFilters()[0]);
    }

    public function testRemoveUnwanted()
    {
        $filters = FiltersBuilder::create()
            ->setRemoveNumbers(false)
            ->setRemoveTrailing(false)
            ->setRemoveUnwanted(true)
            ->build()
        ;
        $this->assertInstanceOf(Filters::class, $filters);
        $this->assertInstanceOf(RemoveCharacters::class, $filters->getFilters()[0]);
    }

    public function testMinMaxLength()
    {
        $filters = FiltersBuilder::create()
            ->setRemoveNumbers(false)
            ->setRemoveTrailing(false)
            ->setRemoveUnwanted(false)
            ->setMinLength(5)
            ->setMaxLength(15)
            ->build()
        ;
        $this->assertInstanceOf(Filters::class, $filters);
        $this->assertInstanceOf(RemoveByLength::class, $filters->getFilters()[0]);
        $this->assertAttributeEquals(5, 'minLength', $filters->getFilters()[0]);
        $this->assertAttributeEquals(15, 'maxLength', $filters->getFilters()[0]);
    }
}
