<?php

namespace SixtyNine\Cloud;

use JMS\Serializer\SerializerBuilder;
use SixtyNine\Cloud\Model\Cloud;
use SixtyNine\Cloud\Model\WordsList;

class Serializer
{
    public function saveList(WordsList $list, $pretty = false)
    {
        $serializer = SerializerBuilder::create()->build();
        $data = $serializer->serialize($list, 'json');
        return $pretty ? $this->prettyJson($data) : $data;
    }

    public function loadList($data)
    {
        $serializer = SerializerBuilder::create()->build();
        $list = $serializer->deserialize($data, WordsList::class, 'json');
        foreach ($list->getWords() as $word) {
            $word->setList($list);
        }
        return $list;
    }

    public function saveCloud(Cloud $cloud, $pretty = false)
    {
        $serializer = SerializerBuilder::create()->build();
        $data = $serializer->serialize($cloud, 'json');
        return $pretty ? $this->prettyJson($data) : $data;
    }

    public function loadCloud($data)
    {
        $serializer = SerializerBuilder::create()->build();
        $cloud = $serializer->deserialize($data, Cloud::class, 'json');
        foreach ($cloud->getWords() as $word) {
            $word->setCloud($cloud);
        }
        return $cloud;
    }

    public function prettyJson($data)
    {
        return json_encode(json_decode($data), JSON_PRETTY_PRINT);
    }
}
