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

// If intl is not loaded, PSR-0 will find a stub version of the class.
use ResourceBundle;

use at\peekaboo\ {
  HasMessages,
  MessageError
};

/**
 * For classes that build ICU messages.
 *
 * To provide a fallback message bundle, define a const array MESSAGES
 */
trait MakesMessages {

  /** {@inheritDoc} */
  public static function messageBundle() : MessageBundle {
    if (defined("static::MESSAGES")) {
      if (! is_array(static::MESSAGES)) {
        MessageError::E::BAD_MESSAGES_CONST->throw(["type" => get_debug_type(static::MESSAGES)]);
      }

      return new MessageBundle(static::MESSAGES);
    }

    return new MessageBundle([]);
  }

  /** {@inheritDoc} */
  public function message(string $key, array $context) : string {
    assert($this instanceof HasMessages);
    $registry = $this->messageRegistry();
    return $registry::formatMessage($key, $context) ??
      $registry::formatMessageFor($this, $key, $context) ??
      MessageError::E::NO_MESSAGES->throw([
        "registry" => $registry::class,
        "class" => static::class,
        "key" => $key
      ]);
  }
}


trait MakesMessages {

  /** @var string Preferred locale for messages. */
  private static string $locale;

  /** @var string Default locale for messages. */
  private static string $defaultLocale;

  /** @var ResourceBundle ICU messages bundle. */
  private static ResourceBundle $messages;

  /** {@inheritDoc} */
  public static function setDefaultLocale(string $defaultLocale) : void {
    self::$defaultLocale = $defaultLocale;
  }

  /** {@inheritDoc} */
  public function defaultLocale() : string {
    if (! isset(self::$defaultLocale)) {
      self::setDefaultLocale("en");
    }

    return self::$defaultLocale;
  }

  /** {@inheritDoc} */
  public function locale() : string {
    return self::$locale ?? self::defaultLocale();
  }

  /** {@inheritDoc} */
  public static function localize(string $locale, ResourceBundle $messages = null) : void {
    static::$locale = $locale;
    if (! empty($messages)) {
      static::$messages = $messages;
    }
  }

  /** {@inheritDoc} */
  public function makeMessage(string $key, array $context) : ? string {
    assert($this instanceof HasMessages);
    return MessageRegistry::findMessage($this, $key, $this->prepFormattingContext($context, false)) ??
      $this->findMessage($key, $this->prepFormattingContext($context, true)) ??
      MessageError::E::NO_MESSAGES->throw(["class" => static::class, "key" => $key]);
  }

  /**
   * Looks up and formats a message from the MESSAGES const.
   *
   * @param string $key Dot-delimited path to desired key
   * @param array $context Map of contextual info for the message
   * @throws MessageError NOT_A_MESSAGE if key exists but is not a formatting string
   * @return string|null Message format on success; null otherwise
   */
  protected function findMessage(string $key, array $context) : ? string {
    if (! defined("static::MESSAGES")) {
      return null;
    }

    $format = $this->findSubstituterMessageFormat($key);
    if (isset($format)) {
      return strtr($format, $this->prepFormattingContext($context, true));
    }

    return null;
  }

  /**
   * Looks up a message format from the MESSAGES const.
   *
   * @param string $key Dot-delimited path to desired key
   * @throws MessageError NOT_A_MESSAGE if key exists but is not a formatting string
   * @return string|null Message format on success; null otherwise
   */
  protected function findSubstituterMessageFormat(string $key) : ? string {
    if (! defined("static::MESSAGES")) {
      return null;
    }

    // we just checked above.
    // @phan-suppress-next-line PhanUndeclaredConstantOfClass
    $message = static::MESSAGES;
    foreach (explode(".", $key) as $next) {
      // more keys but no more arrays means not found
      if (! is_array($message) || ! isset($message[$next])) {
        return null;
      }

      $message = $message[$next];
    }

    if (! is_string($message)) {
      MessageError::E::NOT_A_MESSAGE->throw(["class" => static::class, "key" => $key]);
    }

    return $message;
  }

  /**
   * Prepares formatting context for the MessageFormatter.
   *
   * Invalid or unusable context is discarded and is not considered an error.
   *
   * @param array $context Map of contextual info for the message.
   * @param bool $for_substituter Prep for the substituter (not the MessageFormatter)?
   * @return array Map of valid formatting values
   */
  protected function prepFormattingContext(array $context, bool $for_substituter) : array {
    $formattingContext = [];
    foreach ($context as $key => $value) {
      $formattingValue = $this->toFormattingValue($value);
      if (isset($formattingValue)) {
        $formattingContext[$for_substituter ? "{{$key}}" : $key] = $formattingValue;
      }
    }

    return $formattingContext;
  }

  /**
   * Converts a context value to a formatting value.
   *
   * @param mixed $value The value to prep
   * @return string|null A string representation of the value on success; null otherwise
   */
  protected function toFormattingValue($value) : ? string {
    return match (gettype($value)) {
      "string" => $value,
      "integer", "double" => (string) $value,
      "object" => $value::class . ":" . (
        method_exists($value, "__toString") ?
          $value->__toString() :
          json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
      ),
      "array", "boolean", "null" =>
        json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
      "resource", "resource (closed)" => get_resource_type($value) . "#" . get_resource_id($value),
      default => null
    };
  }
}
