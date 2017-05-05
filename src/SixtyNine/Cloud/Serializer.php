<?php

namespace SixtyNine\Cloud;

use JMS\Serializer\SerializerBuilder;
use SixtyNine\Cloud\Model\Cloud;
use SixtyNine\Cloud\Model\CloudWord;
use SixtyNine\Cloud\Model\Word;
use SixtyNine\Cloud\Model\WordsList;

class Serializer
{
    /**
     * @param WordsList $list
     * @param bool $pretty
     * @return string
     */
    public function saveList(WordsList $list, $pretty = false)
    {
        $serializer = SerializerBuilder::create()->build();
        $data = $serializer->serialize($list, 'json');
        return $pretty ? $this->prettyJson($data) : $data;
    }

    /**
     * @param string $data
     * @return WordsList
     */
    public function loadList($data)
    {
        $serializer = SerializerBuilder::create()->build();
        $list = $serializer->deserialize($data, WordsList::class, 'json');
        /** @var Word $word */
        foreach ($list->getWords() as $word) {
            $word->setList($list);
        }
        return $list;
    }

    /**
     * @param Cloud $cloud
     * @param bool $pretty
     * @return string
     */
    public function saveCloud(Cloud $cloud, $pretty = false)
    {
        $serializer = SerializerBuilder::create()->build();
        $data = $serializer->serialize($cloud, 'json');
        return $pretty ? $this->prettyJson($data) : $data;
    }

    /**
     * @param string $data
     * @return Cloud
     */
    public function loadCloud($data)
    {
        $serializer = SerializerBuilder::create()->build();
        $cloud = $serializer->deserialize($data, Cloud::class, 'json');
        /** @var CloudWord $word */
        foreach ($cloud->getWords() as $word) {
            $box = $word->getBox();
            $word->setCloud($cloud);
            $box->update();
            $word->setPosition(array((int)$box->getX(), (int)$box->getY()));
        }
        return $cloud;
    }

    /**
     * @param string $data
     * @return string
     */
    public function prettyJson($data)
    {
        return json_encode(json_decode($data), JSON_PRETTY_PRINT);
    }
}
