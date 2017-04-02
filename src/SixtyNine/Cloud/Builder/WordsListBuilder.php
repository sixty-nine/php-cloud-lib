<?php

namespace SixtyNine\Cloud\Builder;

use SixtyNine\Cloud\Color\ColorGeneratorInterface;
use SixtyNine\Cloud\Filters\Filters;
use SixtyNine\Cloud\Model\WordsList;

class WordsListBuilder
{
    /** @var \SixtyNine\Cloud\Filters\Filters */
    protected $filters;
    /** @var string */
    protected $words;
    /** @var string */
    protected $url;
    /** @var int */
    protected $maxWords;
    /** @var string */
    protected $sortOrder;
    /** @var string */
    protected $sortBy;
    /** @var int */
    protected $randomizeOrientation;
    /** @var ColorGeneratorInterface */
    protected $colorGenerator;

    protected function __construct()
    {
        $this->filters = FiltersBuilder::create()->build();
    }

    /**
     * @return WordsListBuilder
     */
    public static function create()
    {
        return new self();
    }

    /**
     * @param Filters $filters
     * @return WordsListBuilder
     */
    public function setFilters(Filters $filters)
    {
        $this->filters = $filters;
        return $this;
    }

    /**
     * @param int $max
     * @return WordsListBuilder
     */
    public function setMaxWords($max)
    {
        $this->maxWords = $max;
        return $this;
    }

    /**
     * @param string $words
     * @return WordsListBuilder
     */
    public function importWords($words)
    {
        $this->words = $words;
        return $this;
    }

    /**
     * @param string $url
     * @return WordsListBuilder
     */
    public function importUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @param string $sortBy
     * @param string $sortOrder
     * @return WordsListBuilder
     */
    public function sort($sortBy, $sortOrder)
    {
        $this->sortBy = $sortBy;
        $this->sortOrder = $sortOrder;
        return $this;
    }

    public function randomizeOrientation($rate = 50)
    {
        $this->randomizeOrientation = $rate;
        return $this;
    }

    public function randomizeColors(ColorGeneratorInterface $generator)
    {
        $this->colorGenerator = $generator;
        return $this;
    }

    /**
     * @param string $name
     * @return WordsList
     */
    public function build($name)
    {
        $list = new WordsList();
        $list->setName($name);

        if ($this->words) {
            $list->importWords($this->words, $this->filters, $this->maxWords);
        }

        if ($this->url) {
            $list->importUrl($this->url, $this->filters, $this->maxWords);
        }

        if ($this->sortBy && $this->sortOrder) {
            $list->sortWords($this->sortBy, $this->sortOrder);
        }

        if (null !== $this->randomizeOrientation) {
            $list->randomizeOrientation($this->randomizeOrientation);
        }

        if (null !== $this->colorGenerator) {
            $list->randomizeColors($this->colorGenerator);
        }

        return $list;
    }
} 