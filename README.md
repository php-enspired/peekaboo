![](https://img.shields.io/github/release/php-enspired/peekaboo.svg)  ![](https://img.shields.io/badge/PHP-8.1-blue.svg?colorB=8892BF)  ![](https://img.shields.io/badge/license-GPL_3.0_only-blue.svg)

peekaboo! (ICU)
===============

_peekaboo_ provides message formatting utilities using International Components for Unicode, with a fallback on basic string templating.

dependencies
------------

Requires php 8.1 or later.

ICU support requires the `intl` extension.
Building ICU resource bundles uses `genrb`.

installation
------------

Recommended installation method is via [Composer](https://getcomposer.org/): simply `composer require php-enspired/peekaboo`.

for starters
------------

```php
use at\peekaboo\ {
  HasMessages,
  MakesMessages
};

class Foo implements HasMessages {
  use MakesMessages;

  public const MESSAGES = [
    "welcome" => "welcome to the {place}, we've got fun and games"
  ];
}

echo (new Foo())->makeMessage("welcome", ["place" => "jungle"]);
// welcome to the jungle, we've got fun and games
```

docs
----

- API:
  - [`HasMessages`](https://github.com/php-enspired/peekaboo/wiki/Api:-HasMessages)
  - [`MakesMessages`](https://github.com/php-enspired/peekaboo/wiki/Api:-MakesMessages)
  - [`MessageRegistry`](https://github.com/php-enspired/peekaboo/wiki/Api:-MessageRegistry)
- [Basic Usage](https://github.com/php-enspired/peekaboo/wiki/Usage:-Basics)
- [Message Errors](https://github.com/php-enspired/peekaboo/wiki/Usage:-Message-Errors)

tests
-----

Run static analysis with `composer test:analyze`

Run unit tests with `composer test:unit`

contributing or getting help
----------------------------

I'm [on IRC at `libera#php-enspired`](https://web.libera.chat/#php-enspired), or open an issue [on github](https://github.com/php-enspired/peekaboo/issues).  Feedback is welcomed as well.
