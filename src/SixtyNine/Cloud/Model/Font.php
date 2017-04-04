<?php

namespace SixtyNine\Cloud\Model;

/**
 * Embed a TTF font in the Cloud system.
 */
class Font
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $file;

    /**
     * Create a TTF font from the given $file.
     * @param string $name
     * @param string $file
     * @param int $paddingAngle
     * @param int $paddingSize
     * @throws \InvalidArgumentException
     */
    function __construct($name, $file, $paddingAngle = 0, $paddingSize = 1)
    {
        if (!file_exists($file)) {
            throw new \InvalidArgumentException("File not found $file");
        }

        $this->name = $name;
        $this->file = $file;
        $this->paddingAngle = $paddingAngle;
        $this->paddingSize = $paddingSize;
    }

    /**
     * @param string $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
