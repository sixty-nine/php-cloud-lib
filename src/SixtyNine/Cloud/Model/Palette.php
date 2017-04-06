<?php

namespace SixtyNine\Cloud\Model;

class Palette
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $colors;

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param array $colors
     * @return $this
     */
    public function setColors($colors)
    {
        $this->colors = $colors;
        return $this;
    }

    /**
     * @return array
     */
    public function getColors()
    {
        return $this->colors;
    }
}

