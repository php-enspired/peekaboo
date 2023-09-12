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

use at\peekaboo\MessageError;

/**
 * Provides ICU message formatting support.
 */
interface HasMessages {

  /**
   * Sets the default locale for messages.
   *
   * @param string $defaultLocale The default locale to use
   */
  public static function setDefaultLocale(string $defaultLocale) : void;

  /**
   * Gets the default locale, setting it to "en" if not already configured.
   *
   * @return string The default locale
   */
  public function defaultLocale() : string;

  /**
   * Gets the current locale for messages.
   *
   * @return string The current locale
   */
  public function locale() : string;

  /**
   * Sets up localized message support for the concrete implementation(s).
   *
   * @param string           $locale   Preferred locale
   * @param ?ResourceBundle $messages Message format patterns
   */
  public static function localize(string $locale, ResourceBundle $messages = null) : void;

  /**
   * Finds and builds a message with the given key and context, if one exists.
   *
   * @param string $key Message identifier
   * @param array $context Contextual information for message replacements
   * @throws MessageError NO_MESSAGES if no ResourceBundle provided and no MESSAGES const defined
   * @throws MessageError NOT_A_MESSAGE if key is found but is not a formatting string
   * @return string|null Formatted message on success
   */
  public function makeMessage(string $key, array $context) : ? string;
}
