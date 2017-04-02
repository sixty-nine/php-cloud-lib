<?php

namespace SixtyNine\Cloud\Model;

use JMS\Serializer\Annotation as JMS;

class Word
{
    const DIR_VERTICAL = 'vertical';
    const DIR_HORIZONTAL = 'horizontal';

    /**
     * @var string
     * @JMS\Type("string")
     */
    protected $text;

    /**
     * @var int
     * @JMS\Type("integer")
     */
    protected $count;

    /**
     * @var string
     * @JMS\Type("string")
     */
    protected $orientation = self::DIR_HORIZONTAL;

    /**
     * @var string
     * @JMS\Type("string")
     */
    protected $color = '000000';

    /**
     * @var int
     * @JMS\Type("integer")
     */
    protected $position;

    /**
     * @var WordsList
     * @JMS\Type("SixtyNine\Cloud\Model\WordsList")
     */
    protected $list;

    /**
     * @param \SixtyNine\Cloud\Model\WordsList $list
     * @return $this
     */
    public function setList($list)
    {
        $this->list = $list;
        return $this;
    }

    /**
     * @return \SixtyNine\Cloud\Model\wordsList
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @param int $count
     * @return $this
     */
    public function setCount($count)
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $color
     * @return $this
     */
    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $orientation
     * @return $this
     */
    public function setOrientation($orientation)
    {
        $this->orientation = $orientation;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrientation()
    {
        return $this->orientation;
    }

    /**
     * @param int $position
     * @return $this
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

}

