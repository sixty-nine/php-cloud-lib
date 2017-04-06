<?php

namespace SixtyNine\Cloud\Filters;

class Filters
{
    /**
     * An array of FilterInterface
     * @var array
     */
    protected $filters;

    /**
     * @param FilterInterface[] $filters
     */
    public function __construct(array $filters = array())
    {
        $this->filters = array();
        $this->addFilters($filters);
    }

    /**
     * @param FilterInterface[] $filters
     */
    public function addFilters($filters)
    {
        foreach ($filters as $filter) {
            $this->filters[] = $filter;
        }
    }

    /**
     * Add a filter to filter out words.
     * @param FilterInterface $filter
     */
    public function addFilter(FilterInterface $filter)
    {
        if (!in_array($filter, $this->filters)) {

            $this->filters[] = $filter;
        }
    }

    /**
     * @param string $word
     * @return bool|string
     */
    public function apply($word)
    {
        /** @var FilterInterface $filter*/
        foreach($this->filters as $filter) {
            if (!$filter->keepWord($word)) {
                return false;
            }
            $word = $filter->filterWord($word);
        }

        return $word;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }
}
