<?php

namespace SixtyNine\Cloud\Model;

use Doctrine\Common\Collections\ArrayCollection;

class QuadTree
{
    const MAX_OBJECTS = 10;
    const MAX_LEVELS = 10;

    protected $level;
    protected $objects;
    protected $bounds;
    protected $isSplited = false;
    /** @var array QuadTree[] */
    protected $nodes;

    public function __construct(Box $bounds, $level = 0)
    {
        $this->level = $level;
        $this->objects = new ArrayCollection();
        $this->bounds = $bounds;
    }

    public function split()
    {
        $this->isSplited = true;

        $subWidth = (int)($this->bounds->getWidth() / 2);
        $subHeight = (int)($this->bounds->getHeight() / 2);
        $x = (int)$this->bounds->getX();
        $y = (int)$this->bounds->getY();

        $this->nodes = array();
        $this->nodes[0] = new Quadtree(new Box($x, $y, $subWidth, $subHeight), $this->level + 1);
        $this->nodes[1] = new Quadtree(new Box($x + $subWidth, $y, $subWidth, $subHeight), $this->level + 1);
        $this->nodes[2] = new Quadtree(new Box($x, $y + $subHeight, $subWidth, $subHeight), $this->level + 1);
        $this->nodes[3] = new Quadtree(new Box($x + $subWidth, $y + $subHeight, $subWidth, $subHeight), $this->level + 1);
    }

    public function getIndex(Box $box)
    {
        $vMidpoint = $this->bounds->getX() + ($this->bounds->getWidth() / 2);
        $hMidpoint = $this->bounds->getY() + ($this->bounds->getHeight() / 2);

        $topQuadrant = ($box->getY() <= $hMidpoint && $box->getY() + $box->getHeight() <= $hMidpoint);
        $bottomQuadrant = ($box->getY() >= $hMidpoint);
        $leftQuadrant = $box->getX() <= $vMidpoint && $box->getX() + $box->getWidth() <= $vMidpoint;
        $rightQuadrant = $box->getX() >= $vMidpoint;

        // Object can completely fit within the left quadrants
        if ($leftQuadrant) {
            if ($topQuadrant) {
                return 0;
            } else if ($bottomQuadrant) {
                return 2;
            }
        } else if ($rightQuadrant) {
            if ($topQuadrant) {
                return 1;
            } else if ($bottomQuadrant) {
                return 3;
            }
        }

        return -1;
    }

    public function insert(Box $box)
    {
        if ($this->isSplited) {
            $index = $this->getIndex($box);
            if ($index !== -1) {
                $this->nodes[$index]->insert($box);
                return;
            }
        }

        $this->objects->add($box);

        if (count($this->objects) > self::MAX_OBJECTS && $this->level < self::MAX_LEVELS) {
            if (!$this->isSplited) {
                $this->split();
            }

            foreach ($this->objects as $object) {
                $index = $this->getIndex($object);
                if ($index !== -1) {
                    $this->objects->removeElement($object);
                    $this->nodes[$index]->insert($object);
                }
            }
        }
    }

    public function count()
    {
        $count = $this->objects->count();

        if ($this->isSplited) {
            foreach ($this->nodes as $node) {
                $count += $node->count();
            }
        }

        return $count;
    }

    public function retrieve(Box $box)
    {
        $return = array();
        $index = $this->getIndex($box);

        if ($index !== -1 && $this->isSplited) {
            $return = array_merge($return, $this->nodes[$index]->retrieve($box));
        }

        $return = array_merge($return, $this->objects->toArray());
        return $return;
    }

    public function collides(Box $box)
    {
        foreach ($this->objects as $object) {
            if ($box->intersects($object)) {
                return true;
            }
        }

        if ($this->isSplited) {

            $index = $this->getIndex($box);
            $nodes = (-1 === $index) ? $this->nodes : array($this->nodes[$index]);
            foreach ($nodes as $node) {
                if ($node->collides($box)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function __toString()
    {
        $padding = str_repeat('  ', $this->level);
        $res = sprintf(
            '%sQuadTree, level: %s, bounds: %s, objects: %s',
            $padding,
            $this->level,
            $this->bounds,
            $this->objects->count()
        );

        foreach ($this->objects as $box) {
            $res .= PHP_EOL . $padding . '  - ' . (string)$box;
        }

        if (null !== $this->nodes) {
            foreach ($this->nodes as $node) {
                $res .= PHP_EOL . (string)$node;
            }
        }

        return $res . PHP_EOL;
    }

    /**
     * @return array
     */
    public function getAllObjects()
    {
        $return = array();

        $return = array_merge($return, $this->objects->toArray());

        if ($this->isSplited) {
            foreach ($this->nodes as $node) {
                $return = array_merge($return, $node->getAllObjects());
            }
        }

        return $return;
    }
}
