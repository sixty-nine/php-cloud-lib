<?php

namespace SixtyNine\Cloud\Builder;

use SixtyNine\Cloud\Model\Palette;
use Symfony\Component\Yaml\Yaml;

class PalettesBuilder
{
    protected $palettes = array();

    protected function __construct()
    {
    }

    public static function create()
    {
        return new self();
    }

    /**
     * @param string $name
     * @param array $colors
     * @return PalettesBuilder
     */
    public function addPalette($name, array $colors)
    {
        $palette = new Palette();
        $palette->setName($name)->setColors($colors);
        $this->palettes[$name] = $palette;

        return $this;
    }

    /**
     * @param string $file
     * @return PalettesBuilder
     * @throws \InvalidArgumentException
     */
    public function importPalettes($file)
    {
        if (!file_exists($file)) {
            throw new \InvalidArgumentException('File not found: ' . $file);
        }

        $yml = Yaml::parse(file_get_contents($file));

        if (!array_key_exists('palettes', $yml)) {
            throw new \InvalidArgumentException('Invalid palettes YAML');
        }

        foreach ($yml['palettes'] as $name => $colors) {
            $this->addPalette($name, $colors);
        }

        return $this;
    }

    /**
     * @param string $name
     * @return Palette
     * @throws \InvalidArgumentException
     */
    public function getNamedPalette($name)
    {
        if (!array_key_exists($name, $this->palettes)) {
            throw new \InvalidArgumentException('Palette not found: ' . $name);
        }

        return $this->palettes[$name];
    }

    /**
     * @param int $count
     * @return Palette
     */
    public function getRandomPalette($count = 5)
     {
         $colors = array();
         for ($i = 0; $i < $count; $i++) {
             $part1 = dechex(rand(0, 255));
             $part2 = dechex(rand(0, 255));
             $part3 = dechex(rand(0, 255));
             $color = '#'
                . (strlen($part1) >= 2 ? $part1 : '0' . $part1)
                . (strlen($part2) >= 2 ? $part2 : '0' . $part2)
                . (strlen($part3) >= 2 ? $part3 : '0' . $part3)
             ;
             $colors[] = $color;
         }
         $palette = new Palette();
         $palette->setName('random')->setColors($colors);
         return $palette;
     }

    /**
     * @param array $colors
     */
    public function getPalette(array $colors)
    {
        $palette = new Palette();
        $palette->setName('palette')->setColors($colors);
        return $palette;
    }

    /**
     * @return array
     */
    public function getPalettes()
    {
        return $this->palettes;
    }
}
