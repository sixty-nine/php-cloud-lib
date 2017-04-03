# How to contribute and TODOs

There are still many things to do.

 * Write better documentation
 * Improve the tests
   Code coverage is actually quite good but there are a lot of tests that just use the code without really testing anything.
 * Write other/better words placers (see classes in `src/SixtyNine/Cloud/Placer`)
 * Write new filters or improve the existing one (`src/SixtyNine/Cloud/Filters`)
 * Improve the way the words are checked for collisions
 * New commands to generate/load/save lists and clouds

## Running the tests

```bash
composer install
bin/phpunit -c tests/
```

## Adding a new placer

To add a new words placer you have to:

 * Create a new class implementing `PlacerInterface`
 * Update `PlacerFactory`:
   * Create a new PLACER_* constant
   * Add the placer class name in `PlacerFactory::$placers`
   * Write a test and update the documentation if appropriate

## Adding a new filter

To add a new words filter you have to:

 * Create a new class implementing `FilterInterface`
 * Add a method to setup your filter in `FiltersBuilder`
 * Use your filter in `FiltersBuilder::build()`

## Adding a new command

When creating a new command, one must not forget to register it in `bin/clouder` by adding it in the `$commands` array.
