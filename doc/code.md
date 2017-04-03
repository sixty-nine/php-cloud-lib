# Filters

Filters allow to change or filters out words when the words are inserted in a `WordsList`.

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

# WordsList

A list of words contains the words to draw on the cloud along with their number of occurrences, color and, orientation.

The `WordsListBuilder` allows to create a list of words from some random text or from a URL.

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

A cloud contains the words from a `WordsList` already placed and ready to be drawn.

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
    ->setPlacer(PlacerFactory::PLACER_CIRCULAR) // How the words will be placed in the cloud (Circular, Wordle, Spirangle, Linear Horizontal, Linear Vertical, Lissajou)
    ->useList($list)                            // Use the words in the given $list
    ->build()
;
```

# Rendering a cloud with Imagine

The cloud renderer draws the cloud on an Imagine image and return it.

You can then manipulate the `Imagine\Gd\Image` as you wish.

```php
$factory = FontsFactory::create('/path/to/my/ttf/fonts');
$renderer = new CloudRenderer();

/** @var \Imagine\Gd\Image $image */
$image = $renderer->render($cloud, $factory);
$image->save('/tmp/image.png');
```

# Serialization

This component allows to save lists and clouds to JSON and load them (experimental) from appropriate JSON.

## Lists

```php
$serializer = new Serializer();
$json = $serializer->saveList($list, true);
```

```php
$serializer = new Serializer();
$list = $serializer->loadList($json);
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
