<?php
/**
 * @package    at.peekaboo
 * @author     Adrian <adrian@enspi.red>
 * @copyright  2023
 * @license    GPL-3.0 (only)
 *
 *  This program is free software: you can redistribute it and/or modify it
 *  under the terms of the GNU General Public License, version 3.
 *  The right to apply the terms of later versions of the GPL is RESERVED.
 *
 *  This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 *  without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *  See the GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License along with this program.
 *  If not, see <http://www.gnu.org/licenses/gpl-3.0.txt>.
 */
declare(strict_types = 1);
namespace at\peekaboo;

use MessageFormatter,
  ResourceBundle,
  SplObjectStorage as Store;

use at\peekaboo\ {
  HasMessages,
  MessageError
};

/**
 * Wraps ICU message bundles and formatting process.
 *
 * Use of this class requires the intl extension,
 *  and will fail gracefully if not enabled with the exception of the `register()` method
 *  (though note, you won't be able to call `register()` with the proper args if intl is not enabled).
 *
 * @internal For use by MakesMessages::localize() and ::makeMessage()
 */
class MessageRegistry {

  protected static ResourceBundle $defaultMessages;
  protected static string $defaultLocale = "en";
  protected static Store $messages;

  public static function findMessage(
    HasMessages $object,
    string $key,
    array $context,
    string $locale = null
  ) : ? string {
    $format = static::findMessageFormat($object, $key);
    if (empty($format)) {
      return null;
    }

    $locale ??= static::defaultLocale($object);
    $formatter = new MessageFormatter($locale, $format);
    return $formatter->format($context) ?:
      MessageError::E::FORMAT_MESSAGE_FAILED->throw([
        "error_code" => $formatter->getErrorCode(),
        "error_message" => $formatter->getErrorMessage(),
        "class" => static::class,
        "key" => $key,
        "locale" => $locale,
        "format" => $format,
        "context" => json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)
      ]);
  }

  protected static function defaultLocale(HasMessages $object) : string {
    if (empty(static::$messages) || ! static::$messages->contains($object)) {
      return static::$defaultLocale;
    }

    return static::$messages->offsetGet($object)->defaultLocale;
  }

  protected static function findMessageFormat(HasMessages $object, string $key) : ? string {
    if (isset(static::$messages) && static::$messages->contains($object)) {
      $message = static::$messages->offsetGet($object)->messages;
    } elseif (isset(static::$defaultMessages)) {
      $message = static::$defaultMessages;
    } else {
      return null;
    }

    foreach (explode(".", $key) as $next) {
      // more keys but no more message bundles means not found
      if (! $message instanceof ResourceBundle) {
        return null;
      }

      $message = $message->get($next);
    }

    if (! is_string($message)) {
      MessageError::E::NOT_A_MESSAGE->throw(["class" => static::class, "key" => $key]);
    }

    return $message;
  }

  public static function register(
    HasMessages $object,
    ResourceBundle $messages,
    string $defaultLocale = "en"
  ) : void {
    static::$messages ??= new Store();
    static::$messages->attach(
      $object,
      new class($messages, $defaultLocale) {
        public function __construct(
          public readonly ResourceBundle $messages,
          public readonly string $defaultLocale
        ) {}
      }
    );
  }

  public static function registerDefault(ResourceBundle $messages, string $defaultLocale = "en") : void {
    static::$defaultMessages = $messages;
    static::$defaultLocale = $defaultLocale;
  }
}
