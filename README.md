![](https://img.shields.io/github/release/php-enspired/peekaboo.svg)  ![](https://img.shields.io/badge/PHP-8.3-blue.svg?colorB=8892BF)  ![](https://img.shields.io/badge/license-GPL_3.0_only-blue.svg)

peekaboo! (ICU)
===============

_peekaboo_ provides message formatting utilities using International Components for Unicode, with a fallback on basic string templating.

dependencies
------------

Requires php 8.3 or later.

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
  MessageMapper
};

class Foo implements HasMessages {
  use MessageMapper;
}

$formatted = new Foo()->makeMessage("foo.welcome", ["place" => "jungle"]);
// Welcome to the jungle, we've got fun and games
```

message registry
----------------

Where did that message come from? _peekaboo_ provides a registry for your application to store, lookup, and format messages. You can register any intl `ResourceBundle`, either available by default or under a named group, and get formatted messages by passing the message key and substitution context.

If a message key is not found under the specified group name, peekaboo falls back on looking in the default registries. You can also set a default locale (used when `$locale` is not provided for a specific message) by assigning to `MessageRegistry::$defaultLocale`.

```php
<?php
use at\peekaboo\MessageRegistry;

MessageRegistry::register($yourDefaultResourceBundle);
MessageRegistry::register($yourUserResourceBundle, "user");

$formatted = MessageRegistry::message("users.namebadge", ["name" => "Adrian"], "ja_JP", "user");
```
If `users.namebadge` was found in the `ja_JP` locale, in either the "user" or the default registers, the above might return something like:
> こんにちは、私の名前はAdrianです

If no matching formatting string was found in the `ja_JP` locale, peekaboo would fall back on the default locale and we might see something like:
> Hello, my name is Adrian

For convenience, `MessageRegistry::message()` is available via a proxy function `at\peekaboo\_`, so the following is functionally the same as above:
```php
<?php
use function at\peekaboo\_;

$formatted = _("users.namebadge", ["name" => "Adrian"], "ja_JP", "user");
```

To avoid the fallback behavior and look for messages only from a specific bundle (whether registered or not), use `::messageFrom()` instead:
```php
<?php

$formatted = MessageRegistry::messageFrom($yourUserResourceBundle, "users.namebadge", ["name" => "Adrian"], "ja_JP");
```

The message registry can also work with peekaboo's own `MessageBundle` class, which, like intl's `ResourceBundle`, is a container for message format strings. These are loaded from a php array, however, rather than an ICU resource file. As the name implies, only message formats are handled (not any other resource type).

```php
<?php
use at\peekaboo\MessageBundle;

$myBundle = new MessageBundle(["welcome-user" => "Welcome to the {place}, {name}!"]);
echo MessageRegistry::messageFrom($myBundle, "welcome-user", ["name" => "Adrian", "place" => "jungle"]);
// Welcome to the jungle, Adrian!
```

messages without a registry
---------------------------

_peekaboo_ allows classes to declare their own message formatting strings, either as a fallback if no matching message is registered or where there is simply no need to support multiple locales.

There are two ways to declare message formatting strings on a class. First, your class can define formats on the `MESSAGES` array. This is useful as a fallback for messages that aren't registered:

```php
<?php

class Foo implements HasMessages {
  use MessageMapper;

  public const array MESSAGES = [
    "foo" => [
      "welcome" => "Welcome to the {place}, we've got fun and games"
    ]
  ];
}

new Foo()->makeMessage("foo.welcome", ["place" => "jungle"]);
// Welcome to the jungle, we've got fun and games
```

Or, you can use an enum to hold message formats with the `MessageEnum` trait. This provides a method `message()` which is equivalent to `makesMessage()` but does not take a key (since none is needed):
```php
<?php

use at\peekaboo\MessageEnum;

enum Woo : string implements EnumeratesMessages {
  use MessageEnum;

  case Welcome = "Welcome to the {place}, we've got fun and games";
}

Woo::Welcome->message(["place" => "jungle"]);
// Welcome to the jungle, we've got fun and games
```
This is a subtle difference, but is worth calling out: when you use `EnumeratesMessages->message()`, peekaboo _does not look up messages_ in the registry. It will only use the message format declared on the enum. The `->makeMessage()` method is still available, though: this method _will_ use the message registry, and will fall back on using `->message()` if no message is found in the registry.

graceful fallback when intl is not loaded
-----------------------------------------

A basic message formatter is included and is used if the `intl` extension is not available. This formatter supports named tokens (e.g., `{name}`), but ignores more complex formatting options like `select`, `plural`, and so forth. Given simple message formats, it should produce the same formatted message that intl's `MessageFormatter` would, and it does its best to reduce complex formatting expressions to a simple replacement.

This is intended more for non-icu usage (applications with simple messages) than to support a heavily localized application with many messages and complex formatting expressions. In the latter case, you should ensure the intl extension is available.


Version 2.0
-----------

**2.0** requires PHP 8.3 or greater.

**Additionally, _peekaboo_ is now released under the Mozilla Public License, version 2.**

Previously, the software was released under the GPLv3. That license's strong copyleft protections were a major factor in the decision to use it, but it has become apparent that their interpretations of what constitutes "combined works" was much broader than I'd understood it to be. Specifically, merely declaring this package as a dependency (e.g., via composer), without actually modifying and/or including/distributing the code with your software, was never intended to trigger these protections.

If you are using a version of this software licensed under the GPLv3, and would prefer to use it under the MPL instead, please [contact me](relicense@enspi.red). I will grant the relicensing and waive any enforcement action against you arising from any noncompliance with the GPLv3 that would be permissible under the MPL.

tests
-----

Run static analysis with `composer test:analyze` and tun unit tests with `composer test:unit`.

Note, the first time you run a `test:` command, dev dependencies will be installed automatically. This requires an internet connection and may take some time.

contributing or getting help
----------------------------

I'm [on IRC at `libera#php-enspired`](https://web.libera.chat/#php-enspired), or open an issue [on github](https://github.com/php-enspired/peekaboo/issues).  Feedback is welcomed as well.
