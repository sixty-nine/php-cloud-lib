# Filters

```php
/** @var \SixtyNine\Cloud\Filters\Filters $filters */
$filters = FiltersBuilder::create()
    ->setCase('lowercase')
    ->setRemoveNumbers(true)
    ->setRemoveTrailing(true)
    ->setRemoveUnwanted(true)
    ->setMinLength(5)
    ->setMaxLength(15)
    ->build()
;
```

# Words lists

```php
$list = WordsListBuilder::create()
    ->importWords('foobar foo foo bar')
    ->build('foobar')
;
```

```php
/** @var \SixtyNine\Cloud\Model\WordsList $list */
$list = WordsListBuilder::create()
    ->setFilters($filters)
    ->setMaxWords(100)
    ->importUrl('https://en.wikipedia.org/wiki/Tag_cloud')
    ->build('foobar')
;
```

# Cloud

```php
$factory = FontsFactory::create('/path/to/my/ttf/fonts');

$fontSizeGenerator = new \SixtyNine\Cloud\FontSize\DimFontSizeGenerator();

/** @var \SixtyNine\Cloud\Model\Cloud $cloud */
$cloud = CloudBuilder::create($factory)
    ->setBackgroundColor('#ffffff')             // Cloud background color
    ->setDimension(1024, 768)                   // Cloud dimensions
    ->setFont('Arial.ttf')                      // TTF font filename
    ->setSizeGenerator($fontSizeGenerator)      // Optional, alternative font size generator
    ->setFontSizes(14, 64)                      // Minimal and maximal font size to use in the generator
    ->setPlacer('Circular')                     // How the words will be placed in the cloud (Circular, Wordle, Spirangle, Linear Horizontal, Linear Vertical, Lissajou)
    ->useList($list)                            // Use the words in the given $list
    ->build()
;
```

# Rendering a cloud

```php
$factory = FontsFactory::create('/path/to/my/ttf/fonts');
$renderer = new CloudRenderer();

/** @var \Imagine\Gd\Image $image */
$image = $renderer->render($cloud, $factory);
$image->save('/tmp/image.png');
```

# Serialization

## Lists

```php
$serializer = new Serializer();
$json = $serializer->saveList($list, true);
```

```php
$serializer = new Serializer();
$list = $serializer->loadList($json);
```

```json
{
    "name": "foobar",
    "words": [
        {
            "text": "foobar",
            "count": 1,
            "orientation": "vertical",
            "color": "#9452f3"
        },
        {
            "text": "foo",
            "count": 2,
            "orientation": "vertical",
            "color": "#923a24"
        },
        {
            "text": "bar",
            "count": 1,
            "orientation": "vertical",
            "color": "#1ea9de"
        }
    ]
}
```

## Clouds

```php
$serializer = new Serializer();
$json = $serializer->saveCloud($cloud, true);
```

```php
$serializer = new Serializer();
$list = $serializer->loadCloud($json);
```

```json
{
    "background_color": "#000000",
    "width": 800,
    "height": 600,
    "font": "Arial.ttf",
    "words": [
        {
            "size": 60,
            "angle": 0,
            "color": "#894264",
            "text": "foo",
            "position": [
                266,
                300
            ],
            "box": [
                112,
                61
            ],
            "is_visible": true
        },
        {
            "size": 35,
            "angle": 0,
            "color": "#41b9e1",
            "text": "bar",
            "position": [
                256,
                263
            ],
            "box": [
                68,
                36
            ],
            "is_visible": true
        },
        {
            "size": 35,
            "angle": 270,
            "color": "#350afc",
            "text": "foobar",
            "position": [
                342,
                495
            ],
            "box": [
                36,
                134
            ],
            "is_visible": true
        }
    ]
}
```
