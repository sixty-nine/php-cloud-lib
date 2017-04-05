<?php

namespace SixtyNine\Cloud\Model;

use Doctrine\Common\Collections\ArrayCollection;
use SixtyNine\Cloud\Color\ColorGeneratorInterface;
use SixtyNine\Cloud\Filters\Filters;
use JMS\Serializer\Annotation as JMS;
use Webmozart\Assert\Assert;

class WordsList
{
    const SORT_ALPHA = 'text';
    const SORT_COUNT = 'count';
    const SORT_ANGLE = 'angle';

    const SORT_ASC = 'asc';
    const SORT_DESC = 'desc';

    /**
     * @var string
     * @JMS\Type("string")
     */
    protected $name;

    /**
     * @var ArrayCollection
     * @JMS\Type("ArrayCollection<SixtyNine\Cloud\Model\Word>")
     */
    protected $words;

    /**
     * @var array
     * @JMS\Exclude()
     */
    protected $allowedSortBy = array(self::SORT_ALPHA, self::SORT_ANGLE, self::SORT_COUNT);

    /**
     * @var array
     * @JMS\Exclude()
     */
    protected $allowedSortOrder = array(self::SORT_ASC, self::SORT_DESC);

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->words = new ArrayCollection();
    }

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
     * Check if a Word with the same text already exists in the Cloud
     * @param string $text
     * @return null|Word
     */
    public function getWordForText($text)
    {
        /** @var Word $word */
        foreach ($this->words as $word) {
            if ($word->getText() === $text) {
                return $word;
            }
        }
        return null;
    }

    /**
     * @param Word $word
     * @return $this
     */
    public function addWord(Word $word)
    {
        if (!$this->words->contains($word)) {
            $this->words->add($word);
        }

        return $this;
    }

    /**
     * @param Word $word
     * @return $this
     */
    public function removeWord(Word $word)
    {
        $this->words->remove($word);
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getWords()
    {
        return $this->words;
    }

    /**
     * @return ArrayCollection
     */
    public function getWordsOrdered()
    {
        $iterator = $this->words->getIterator();
        $iterator->uasort(function ($a, $b) {
            return ($a->getPosition() < $b->getPosition()) ? -1 : 1;
        });
        return new ArrayCollection(iterator_to_array($iterator));
    }

    /**
     * @param string $sortBy
     * @param string $sortOrder
     * @throws \InvalidArgumentException
     */
    public function sortWords($sortBy, $sortOrder)
    {
        Assert::oneOf($sortBy, $this->allowedSortBy, 'Invalid sort by: ' . $sortBy);
        Assert::oneOf($sortOrder, $this->allowedSortOrder, 'Invalid sort order: ' - $sortOrder);

        $sorter = function ($a, $b) use ($sortBy, $sortOrder) {
            $method = $sortBy === self::SORT_ANGLE
                ? 'getOrientation'
                : 'get' . ucfirst($sortBy)
            ;

            $attr1 = $a->$method();
            $attr2 = $b->$method();

            if ($sortOrder === self::SORT_ASC) {
                return $attr1 > $attr2;
            }

            return $attr1 < $attr2;
        };


        $iterator = $this->words->getIterator();
        $iterator->uasort($sorter);

        $index = 0;
        foreach ($iterator as $word) {
            $word->setPosition($index);
            $index++;
        }
    }

    /**
     * @return int
     * @JMS\VirtualProperty
     * @JMS\SerializedName("max-count")
     */
    public function getWordsMaxCount()
    {
        $max = 0;
        /** @var Word $word */
        foreach ($this->words as $word) {
            if ($word->getCount() > $max) {
                $max = $word->getCount();
            }
        }
        return $max;
    }

    /**
     * @return int
     * @JMS\VirtualProperty
     * @JMS\SerializedName("count")
     */
    public function getWordsCount()
    {
        return $this->words->count();
    }

    /**
     * @param string $words
     * @param Filters $filters
     * @param int $maxWords
     */
    public function importWords($words, Filters $filters = null, $maxWords = 100)
    {
        $array = preg_split("/[\n\r\t ]+/", $words);

        foreach ($array as $word) {
            $this->importWord($word, $filters);
            if ($maxWords && $this->words->count() >= $maxWords) {
                break;
            }
        }
    }

    /**
     * @param string $word
     * @param Filters $filters
     */
    public function importWord($word, Filters $filters = null)
    {
        if ($filters) {
            $word = $filters->apply($word);

            if (!$word) {
                return;
            }
        }

        $entity = $this->getWordForText($word);

        if (!$entity) {
            $entity = new Word();
            $entity
                ->setList($this)
                ->setText($word)
                ->setOrientation(Word::DIR_HORIZONTAL)
                ->setColor('#000000')
            ;
            $this->addWord($entity);
        }

        $entity->setCount($entity->getCount() + 1);
    }

    /**
     * @param string $html
     * @param Filters $filters
     * @param int $maxWords
     */
    public function importHtml($html, Filters $filters = null, $maxWords = 100)
    {
        if (!$html) {
            return;
        }

        $d = new \DOMDocument;
        $mock = new \DOMDocument;
        libxml_use_internal_errors(true);
        $d->loadHTML($html);
        libxml_use_internal_errors(false);
        $body = $d->getElementsByTagName('body')->item(0);
        if ($body) {
            foreach ($body->childNodes as $child) {
                $mock->appendChild($mock->importNode($child, true));
            }
        }
        $text = html_entity_decode(strip_tags($mock->saveHTML()));
        $this->importWords($text, $filters, $maxWords);
    }

    /**
     * @param string $url
     * @param Filters $filters
     * @param int $maxWords
     */
    public function importUrl($url, Filters $filters = null, $maxWords = 100)
    {
        $this->importHtml(file_get_contents($url), $filters, $maxWords);
    }

    /**
     * Apply the given $filters to the $list.
     * @param Filters $filters
     */
    public function filterWords(Filters $filters)
    {
        /** @var Word $word */
        foreach ($this->getWords() as $word) {
            $filtered = $filters->apply($word->getText());
            if (!$filtered) {
                $this->words->remove($word);
                continue;
            }
            $word->setText($filtered);
        }
    }

    /**
     * @param int $verticalProbability
     */
    public function randomizeOrientation($verticalProbability = 50)
    {
        /** @var \SixtyNine\CloudBundle\Entity\Word $word */
        foreach ($this->getWords() as $word) {

            $orientation = random_int(0, 99) < $verticalProbability
                ? Word::DIR_VERTICAL
                : Word::DIR_HORIZONTAL
            ;

            $word->setOrientation($orientation);
        }
    }

    /**
     * @param ColorGeneratorInterface $colorGenerator
     */
    public function randomizeColors(ColorGeneratorInterface $colorGenerator)
    {
        /** @var \SixtyNine\CloudBundle\Entity\Word $word */
        foreach ($this->getWords() as $word) {
            $word->setColor($colorGenerator->getNextColor());
        }
    }

}

