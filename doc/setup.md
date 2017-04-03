# Installation

```bash
composer require sixty-nine/php-cloud-lib dev-master
```

# Setup

The library does not come with any TTF font provided.

To generate words clouds you need at least one font installed.

Without any other setup, the library expects to find the fonts installed in the `fonts/` directory. If you don't want
any additional configuration, place your fonts there. Or you can use the `--fonts-path` command option to point to
another directory containing the fonts.
