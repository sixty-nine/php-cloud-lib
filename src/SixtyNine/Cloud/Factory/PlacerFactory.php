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
    const PLACER_CIRCULAR = 'circular';
    const PLACER_WORDLE = 'wordle';
    const PLACER_SPIRANGLE = 'spirangle';
    const PLACER_LINEAR_H = 'linear-h';
    const PLACER_LINEAR_V = 'linear-v';
    const PLACER_LISSAJOU = 'lissajou';

    /** @var PlacerFactory */
    protected static $instance;

    /** @var array */
    protected $placers = array(
        self::PLACER_CIRCULAR => CircularPlacer::class,
        self::PLACER_WORDLE => WordlePlacer::class,
        self::PLACER_SPIRANGLE => SpiranglePlacer::class,
        self::PLACER_LINEAR_H => LinearHorizontalPlacer::class,
        self::PLACER_LINEAR_V => LinearVerticalPlacer::class,
        self::PLACER_LISSAJOU => LissajouPlacer::class,
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
        return $this->getPlacer(self::PLACER_CIRCULAR, $imgWidth, $imgHeight);
    }

    public function getPlacer($name, $imgWidth, $imgHeight, $increment = 10)
    {
        $className = $this->getPlacerClass($name);
        return new $className($imgWidth, $imgHeight, $increment);
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