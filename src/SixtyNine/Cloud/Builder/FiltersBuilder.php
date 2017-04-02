<?php

namespace SixtyNine\Cloud\Builder;

use SixtyNine\Cloud\Filters\ChangeCase;
use SixtyNine\Cloud\Filters\Filters;
use SixtyNine\Cloud\Filters\RemoveByLength;
use SixtyNine\Cloud\Filters\RemoveCharacters;
use SixtyNine\Cloud\Filters\RemoveNumbers;
use SixtyNine\Cloud\Filters\RemoveTrailingCharacters;

class FiltersBuilder
{
    /** @var array */
    protected $allowedCase;
    /** @var string|null */
    protected $case;
    /** @var bool */
    protected $removeNumbers = true;
    /** @var bool */
    protected $removeUnwanted = true;
    /** @var bool */
    protected $removeTrailing = true;
    /** @var int|null */
    protected $minLength;
    /** @var int|null */
    protected $maxLength;

    protected function __construct()
    {
        $this->allowedCase = array(
            ChangeCase::LOWERCASE,
            ChangeCase::UPPERCASE,
            ChangeCase::UCFIRST,
        );
    }

    public static function create()
    {
        return new self();
    }

    /**
     * @param string $case
     * @return FiltersBuilder
     */
    public function setCase($case)
    {
        $case = strtolower($case);
        if (in_array($case, $this->allowedCase)) {
            $this->case = $case;
        }
        return $this;
    }

    /**
     * @param boolean $enabled
     * @return FiltersBuilder
     */
    public function setRemoveNumbers($enabled)
    {
        $this->removeNumbers = (bool)$enabled;
        return $this;
    }

    /**
     * @param boolean $enabled
     * @return FiltersBuilder
     */
    public function setRemoveUnwanted($enabled)
    {
        $this->removeUnwanted = (bool)$enabled;
        return $this;
    }

    /**
     * @param boolean $enabled
     * @return FiltersBuilder
     */
    public function setRemoveTrailing($enabled)
    {
        $this->removeTrailing = (bool)$enabled;
        return $this;
    }

    /**
     * @param int $maxLength
     * @return FiltersBuilder
     */
    public function setMaxLength($maxLength)
    {
        $this->maxLength = $maxLength;
        return $this;
    }

    /**
     * @param int $minLength
     * @return FiltersBuilder
     */
    public function setMinLength($minLength)
    {
        $this->minLength = $minLength;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getAllowedCase()
    {
        return $this->allowedCase;
    }

    /**
     * @return Filters
     */
    public function build()
    {
        $filters = new Filters();

        if (null !== $this->case) {
            $filters->addFilter(new ChangeCase($this->case));
        }

        if ($this->removeNumbers) {
            $filters->addFilter(new RemoveNumbers());
        }

        if ($this->removeUnwanted) {
            $filters->addFilter(new RemoveCharacters());
        }

        if ($this->removeTrailing) {
            $filters->addFilter(new RemoveTrailingCharacters());
        }

        if (null !== $this->minLength || null !== $this->maxLength) {
            $filters->addFilter(new RemoveByLength($this->minLength, $this->maxLength));
        }

        return $filters;
    }
} 