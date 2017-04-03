# Clouder command

The clouder command `bin/clouder` allows you to create word clouds on the command line.

# Building a cloud

## From words in a text file

```bash

bin/clouder cloud:from-file <path>
      --case[=CASE]                                  # Change case filter type (uppercase, lowercase, ucfirst)
      --max-word-count[=MAX-WORD-COUNT]              # Maximum number of words [default: 100]
      --min-word-length[=MIN-WORD-LENGTH]            # Minimumal word length [default: 5]
      --max-word-length[=MAX-WORD-LENGTH]            # Maximal word length [default: 10]
      --no-remove-numbers                            # Disable the remove numbers filter
      --no-remove-trailing                           # Disable the remove trailing characters filter
      --no-remove-unwanted                           # Disable the remove unwanted characters filter
      --vertical-probability[=VERTICAL-PROBABILITY]  # The percentage probability of having vertical words (0-100) [default: 50]
      --palette[=PALETTE]                            # The name of the palette used to color words
      --palette-type[=PALETTE-TYPE]                  # The way the palette colors are used (cycle, random) [default: "cycle"]
      --palettes-file[=PALETTES-FILE]                # Optional path to the fonts, if omitted, defaults to <base>/fonts
      --sort-by[=SORT-BY]                            # Words sorting field (text, count, angle)
      --sort-order[=SORT-ORDER]                      # Words sorting order (asc, desc)
      --background-color[=BACKGROUND-COLOR]          # Background color of the cloud [default: "#FFFFFF"]
      --placer[=PLACER]                              # Word placer to use
      --font[=FONT]                                  # Font to use to draw the cloud
      --width[=WIDTH]                                # Width of the cloud [default: 800]
      --height[=HEIGHT]                              # Height of the cloud [default: 600]
      --font-size-boost[=FONT-SIZE-BOOST]            # Minimal font size (linear, dim, boost) [default: "linear"]
      --min-font-size[=MIN-FONT-SIZE]                # Minimal font size [default: 12]
      --max-font-size[=MAX-FONT-SIZE]                # Maximal font size [default: 64]
      --save-to-file[=SAVE-TO-FILE]                  # If set to a file name, the output will be saved there
      --format[=FORMAT]                              # Output format (gif, jpeg, png) [default: "png"]
      --fonts-path[=FONTS-PATH]                      # Optional path to the fonts, if omitted, defaults to <base>/fonts
      --render-usher                                 # Enable the rendering of the words usher
      --render-boxes                                 # Enable the rendering of the words bounding boxes

```

## From a URL

```bash

bin/clouder cloud:from-url <url>
      --case[=CASE]                                  # Change case filter type (uppercase, lowercase, ucfirst)
      --max-word-count[=MAX-WORD-COUNT]              # Maximum number of words [default: 100]
      --min-word-length[=MIN-WORD-LENGTH]            # Minimumal word length [default: 5]
      --max-word-length[=MAX-WORD-LENGTH]            # Maximal word length [default: 10]
      --no-remove-numbers                            # Disable the remove numbers filter
      --no-remove-trailing                           # Disable the remove trailing characters filter
      --no-remove-unwanted                           # Disable the remove unwanted characters filter
      --vertical-probability[=VERTICAL-PROBABILITY]  # The percentage probability of having vertical words (0-100) [default: 50]
      --palette[=PALETTE]                            # The name of the palette used to color words
      --palette-type[=PALETTE-TYPE]                  # The way the palette colors are used (cycle, random) [default: "cycle"]
      --palettes-file[=PALETTES-FILE]                # Optional path to the fonts, if omitted, defaults to <base>/fonts
      --sort-by[=SORT-BY]                            # Words sorting field (text, count, angle)
      --sort-order[=SORT-ORDER]                      # Words sorting order (asc, desc)
      --background-color[=BACKGROUND-COLOR]          # Background color of the cloud [default: "#FFFFFF"]
      --placer[=PLACER]                              # Word placer to use
      --font[=FONT]                                  # Font to use to draw the cloud
      --width[=WIDTH]                                # Width of the cloud [default: 800]
      --height[=HEIGHT]                              # Height of the cloud [default: 600]
      --font-size-boost[=FONT-SIZE-BOOST]            # Minimal font size (linear, dim, boost) [default: "linear"]
      --min-font-size[=MIN-FONT-SIZE]                # Minimal font size [default: 12]
      --max-font-size[=MAX-FONT-SIZE]                # Maximal font size [default: 64]
      --save-to-file[=SAVE-TO-FILE]                  # If set to a file name, the output will be saved there
      --format[=FORMAT]                              # Output format (gif, jpeg, png) [default: "png"]
      --fonts-path[=FONTS-PATH]                      # Optional path to the fonts, if omitted, defaults to <base>/fonts
      --render-usher                                 # Enable the rendering of the words usher
      --render-boxes                                 # Enable the rendering of the words bounding boxes

```

# Get information

## List the available fonts

This command lists all the TTF fonts found in the given `FONTS-PATH` or if none given, in the `fonts` directory of the root
of the project.

```bash
bin/clouder list:fonts [--fonts-path=FONTS-PATH]
```

## List the available palettes

This command lists all the palettes found. The palettes are loaded from the given `PALETTES-FILE` or from the default
palettes provided with the library in `src/SixtyNine/Cloud/Resources/palettes.yml`.

```bash
bin/clouder list:palettes -[-palettes-file=PALETTES-FILE]
```

## List the available words placers

List the words placers that can be used.

```bash
bin/clouder list:placers
```

## Debug placers

To help debug the implementation of new words placers, this command generates an image that shows the path a word placer
uses.

```bash
debug:usher <placer>
      --width[=WIDTH]                        # Width of the image [default: 800]
      --height[=HEIGHT]                      # Height of the image [default: 600]
      --color[=COLOR]                        # Color of the path [default: "#FF0000"]
      --background-color[=BACKGROUND-COLOR]  # Background color of the cloud [default: "#FFFFFF"]
      --save-to-file[=SAVE-TO-FILE]          # If set to a file name, the output will be saved there
      --format[=FORMAT]                      # Output format (gif, jpeg, png) [default: "png"]
```
