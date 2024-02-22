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
  ResourceBundle;

use at\peekaboo\MessageError;

/**
 * Wraps ICU message bundles and formatting process;
 *  serves as a centralized place to register and access message bundles.
 *
 * Use of intl features will fail gracefully if the extension is not enabled.
 */
class MessageRegistry {

  /** @var string The "root" locale name. */
  protected const ROOT_LOCALE = "root";

  /** @var string|null The default locale to use for message lookups/formatting. */
  public static ? string $defaultLocale = null;

  /** @var array Available message bundles, grouped by locale. */
  protected static array $messages = [];

  /**
   * Sets up a resource bundle for a given locale.
   * If no default locale/bundle is registered, the first-registered locale+bundle will be used.
   *
   * @param string $locale Preferred locale
   * @param ?ResourceBundle $messages Message format patterns
   */
  public static function localize(string $locale, ResourceBundle $messages = null) : void {
    static::$messages[$locale][] = $messages;
  }

  /**
   * Looks up and formats the message identified by key, using the given locale and context.
   *
   * @param string $key Dot-delimited key path to target message
   * @param array $context Contextual replacements
   * @param ?string $locale Target message locale
   * @throws MessageException MessageError::FormatFailed on error
   * @return string|null Formatted message on success
   */
  public static function message(string $key, array $context, string $locale = null) : ? string {
    $locale ??= static::$defaultLocale ?? static::ROOT_LOCALE;
    $format = static::findFormat($key, $locale);
    if (empty($format)) {
      return null;
    }

    return static::formatMessage($locale, $format, $context);
  }

  /**
   * Looks up and formats the message identified by key, using the given locale and context.
   *
   * @param string $key Dot-delimited key path to target message
   * @param array $context Contextual replacements
   * @param ?string $locale Target message locale
   * @throws MessageException MessageError::FormatFailed on error
   * @return string|null Formatted message on success
   */
  public static function messageFrom(ResourceBundle $messages, string $key, array $context, string $locale = null) : ? string {
    $locale ??= static::$defaultLocale ?? static::ROOT_LOCALE;
    $format = static::findFormatIn($messages, $locale);
    if (empty($format)) {
      return null;
    }

    return static::formatMessage($locale, $format, $context);
  }

  /**
   * Finds a message format string for the given locale.
   * Falls back on the root locale if needed.
   *
   * @param string $key Dot-delimited key path to target message
   * @param string $locale Target message locale
   * @throws MessageException MessageError::NotAMessage if key exists but is not a formatting string
   * @return string|null Formatting message if found; null otherwise
   */
  protected static function findFormat(string $key, string $locale) : ? string {
    if (! empty(static::$messages)) {
      foreach (static::$messages[$locale] ?? reset(static::$messages) as $messages) {
        $format = static::findFormatIn($messages, $locale);
        if (isset($format)) {
          return $format;
        }
      }
    }

    return null;
  }

  /**
   * Finds a message format string in a message bundle.
   *
   * @param ResourceBundle $messages The message bundle to look in
   * @param string $key Dot-delimited key path to target message
   * @throws MessageException MessageError::NotAMessage if key exists but is not a formatting string
   * @return string|null Formatting message if found; null otherwise
   */
  protected static function findFormatIn(ResourceBundle $messages, string $key) : ? string {
    $message = $messages;
    foreach (explode(".", $key) as $next) {
      // more keys but no more message bundles means not found
      if (! $message instanceof ResourceBundle) {
        return null;
      }

      $message = $message->get($next);
    }
    if (! is_string($message)) {
      throw (MessageError::NotAMessage)(["bundle" => $messages::class, "key" => $key]);
    }

    return $message;
  }

  /**
   * Formats a message.
   *
   * @param string $locale Target message locale
   * @param string $format Message formatting string
   * @param array $context Contextual replacements
   * @throws MessageException MessageError::FormatFailed on error
   * @return string|null Formatted message on success
   */
  protected static function formatMessage(string $locale, string $format, array $context) : ? string {
    $formatter = new MessageFormatter($locale, $format);
    return $formatter->format($context) ?:
      throw (MessageError::FormatFailed)([
        "error_code" => $formatter->getErrorCode(),
        "error_message" => $formatter->getErrorMessage(),
        "locale" => $locale,
        "format" => $format,
        "context" => $context
      ]);
  }
}
