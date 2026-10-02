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
        $this->assertEquals('uppercase', $this->readProperty($filters->getFilters()[0], 'case'));
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
        $this->assertEquals(5, $this->readProperty($filters->getFilters()[0], 'minLength'));
        $this->assertEquals(15, $this->readProperty($filters->getFilters()[0], 'maxLength'));
    }

    /**
     * Replacement for assertAttributeEquals(), which was removed in PHPUnit 9.
     *
     * @param object $object
     * @param string $property
     * @return mixed
     */
    protected function readProperty($object, $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);
        return $reflection->getValue($object);
    }
}
