<?php

namespace SixtyNine\Cloud\Filters;

/**
 * Remove words too short or too long.
 */
class RemoveByLength extends AbstractFilter implements FilterInterface
{
    protected $maxLength;
    protected $minLength;

    /**
     * @param bool|int $minLength The minimal length or false
     * @param bool|int $maxLength The maximal length or false
     */
    public function __construct($minLength = false, $maxLength = false)
    {
        $this->minLength = $minLength ? (int)$minLength : false;
        $this->maxLength = $maxLength ? (int)$maxLength : false;
    }

    /**
     * @return bool|int The minimal length or false
     */
    public function getMinLength()
    {
        return $this->minLength;
    }

    /**
     * @return bool|int The maximal length or false
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }

    /** {@inheritdoc} */
    public function keepWord($word)
    {
        $len = strlen($word);

        if (false !== $this->minLength && $len <= $this->minLength) {
            return false;
        }

        if (false !== $this->maxLength && $len >= $this->maxLength) {
            return false;
        }

        return true;
    }
}
