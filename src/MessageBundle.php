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

/**
 * A ResourceBundle-compatible that takes messages from an array.
 *
 * This class is intended to support internal MakesMessages functionality, but can be used independently.
 * Note, if ext/intl is not loaded, not all expected methods will be available -
 *  and even when intl _is_ available, some native ResourceBundle methods will error if used.
 * Users are very strongly advised to rely only on the methods defined here.
 *
 * Converts backslashes in message keys to underscores.
 * This is done to support the use of (namespaced) classnames in keys.
 *
 * false positive: ResourceBundle is only defined if ext/intl isn't loaded.
 * @phan-suppress PhanRedefinedExtendedClass
 */
class MessageBundle extends ResourceBundle {

  /** @param array $messages A map of message formats. */
  public function __construct(protected array $messages) {
    $this->messages = $this->rekey($messages);
  }

  /**
   * Counts (top-level) message keys in this bundle.
   *
   * @return int
   */
  public function count() : int {
    return count($this->messages);
  }

  /**
   * Looks up a message by key.
   *
   * @param string $key The key to look up
   * @param bool $fallback Unused
   * @return string|MessageBundle|null Message or Bundle at key if exists; null otherwise
   */
  public function get($key, bool $fallback = true) : mixed {
    $key = strtr($key, ["\\" => "_"]);
    if (isset($this->messages[$key])) {
      if (is_scalar($this->messages[$key])) {
        return $this->messages[$key];
      }

      if (is_array($this->messages[$key])) {
        return new self($this->messages[$key]);
      }
    }

    return null;
  }

  protected function rekey(array $messages) : array {
    $rekeyed = [];
    foreach ($messages as $key => $value) {
      $rekeyed[strtr($key, ["\\" => "_"])] = is_array($value) ?
        $this->rekey($value) :
        $value;
    }

    return $rekeyed;
  }
}
