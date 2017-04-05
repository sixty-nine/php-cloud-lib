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
     * @throws \InvalidArgumentException
     */
    function __construct($name, $file)
    {
        if (!file_exists($file)) {
            throw new \InvalidArgumentException("File not found $file");
        }

        $this->name = $name;
        $this->file = $file;
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
