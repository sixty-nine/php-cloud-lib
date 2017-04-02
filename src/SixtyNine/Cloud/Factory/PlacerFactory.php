<?php

namespace SixtyNine\Cloud\Factory;


use SixtyNine\Cloud\Placer\CircularPlacer;
use SixtyNine\Cloud\Placer\LinearHorizontalPlacer;
use SixtyNine\Cloud\Placer\LinearVerticalPlacer;
use SixtyNine\Cloud\Placer\LissajouPlacer;
use SixtyNine\Cloud\Placer\PlacerInterface;
use SixtyNine\Cloud\Placer\SpiranglePlacer;
use SixtyNine\Cloud\Placer\WordlePlacer;

class PlacerFactory
{
    /** @var PlacerFactory */
    protected static $instance;

    /** @var array */
    protected $placers = array(
        'Circular' => CircularPlacer::class,
        'Wordle' => WordlePlacer::class,
        'Spirangle' => SpiranglePlacer::class,
        'Linear Horizontal' => LinearHorizontalPlacer::class,
        'Linear Vertical' => LinearVerticalPlacer::class,
        'Lissajou' => LissajouPlacer::class,
    );

    protected function __construct() { }

    public  static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPlacersNames()
    {
        return array_keys($this->placers);
    }

    public function getDefaultPlacer($imgWidth, $imgHeight)
    {
        return $this->getPlacer('Circular', $imgWidth, $imgHeight);
    }

    public function getPlacer($name, $imgWidth, $imgHeight)
    {
        $className = $this->getPlacerClass($name);
        return new $className($imgWidth, $imgHeight);
    }

    /**
     * @param string $name
     * @return PlacerInterface
     * @throws \InvalidArgumentException
     */
    protected function getPlacerClass($name)
    {
        if (!array_key_exists($name, $this->placers)) {
            throw new \InvalidArgumentException('Placer not found: ' . $name);
        }

        return $this->placers[$name];
    }
} 