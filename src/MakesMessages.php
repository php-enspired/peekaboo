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

use ResourceBundle;
require_once __DIR__ . "/../stubs/intl.php";

use at\peekaboo\ {
  HasMessages,
  MessageBundle,
  MessageError,
  MessageRegistry
};

/**
 * For classes that build ICU messages.
 *
 * By default, looks on MessageRegistry for message bundles.
 * To provide a fallback message bundle,
 *  define a const array MESSAGES like [$key => $format, ...]
 */
trait MakesMessages {

  /**
   * Gets a default/fallback ResourceBundle for this class.
   *
   * By default, builds a MessageBundle from the MESSAGES const, if defined.
   *
   * We check before referencing (it's defined on the interface).
   * @phan-suppress PhanUndeclaredConstantOfClass
   *
   * @throws MessageException MessageError::BadMessages if MESSAGES is not an array
   * @return ResourceBundle
   */
  public static function messageBundle() : ResourceBundle {
    assert(is_a(static::class, HasMessages::class, true));
    if (! is_array(static::MESSAGES)) {
      throw (MessageError::BadMessages)(["type" => get_debug_type(static::MESSAGES)]);
    }

    return new MessageBundle(static::MESSAGES);
  }

  /** {@inheritDoc} */
  public function makeMessage(string $key, array $context = [], string $locale = null) : string {
    assert($this instanceof HasMessages);
    $context = $this->prepFormattingContext($context);
    $registry = $this->messageRegistry();
    return $registry::message($key, $context, $locale) ??
      $registry::messageFrom(static::messageBundle(), $key, $context, $locale) ??
      throw (MessageError::NoMessages)([
        "registry" => $registry,
        "class" => static::class,
        "key" => $key
      ]);
  }

  /**
   * Gets the message registry class to use for making messages.
   * Override this method to substitute a different registry.
   *
   * @return string FQCN of the MessageRegistry to use
   */
  protected function messageRegistry() : string {
    return MessageRegistry::class;
  }

  /**
   * Prepares formatting context for the MessageFormatter.
   * Invalid or unusable context is discarded and is not considered an error.
   *
   * @param array $context Map of contextual info for the message.
   * @return array Map of valid formatting values
   */
  protected function prepFormattingContext(array $context) : array {
    $formattingContext = [];
    foreach ($context as $key => $value) {
      $formattingContext[$key] = $this->toFormattingValue($value);
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
      default => get_debug_type($value)
    };
  }
}
