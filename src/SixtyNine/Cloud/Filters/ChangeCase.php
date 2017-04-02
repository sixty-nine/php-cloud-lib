<?php

namespace SixtyNine\Cloud\Filters;

/**
 * Change the case of the words.
 */
class ChangeCase extends AbstractFilter implements FilterInterface
{
    const UPPERCASE = 'uppercase';
    const LOWERCASE = 'lowercase';
    const UCFIRST = 'ucfirst';

    /** @var string */
    protected $case;

    /**
     * @param string $case
     */
    public function __construct($case)
    {
        $this->case = $case;
    }

    /** {@inheritdoc} */
    public function filterWord($word)
    {
        switch ($this->case) {
            case self::UPPERCASE:
                $word = strtoupper($word);
                break;
            case self::UCFIRST:
                $word = ucfirst($word);
                break;
            case self::LOWERCASE:
                $word = strtolower($word);
                break;
        }

        return $word;
    }
}