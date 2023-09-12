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

use Exception;

/**
 * Messaging errors.
 * Looks like an Exceptable but isn't (circular dependencies).
 */
class MessageError extends Exception {

  const E = MessageErrorCase::class;

  public function __construct(string $message, int $code) {
    parent::__construct($message, $code);
    $frame = $this->getTrace()[0] ?? null;
    if (isset($frame)) {
      // @phan-suppress-next-line PhanAccessPropertyProtected
      $this->file = $frame["file"];
      // @phan-suppress-next-line PhanAccessPropertyProtected
      $this->line = $frame["line"];
    }
  }
}

/**
 * Error cases.
 * Looks like an ErrorCase but isn't (circular dependencies).
 */
enum MessageErrorCase : int {

  case NO_MESSAGES = 0;
  case NOT_A_MESSAGE = 1;
  case FORMAT_MESSAGE_FAILED = 2;

  /**
   * Constructs and throws an exception based on this error case.
   *
   * @param array $context Substitution map for the error message
   */
  public function throw(array $context = []) : void {
    throw new MessageError($this->message($context), $this->value);
  }

  /**
   * Formats an error message for this case, making substitutions from context.
   *
   * @param string[] $context Substitution map for the error message
   * @return string Formatted message
   */
  protected function message(array $context) : string {
    $format = match($this->value) {
      self::NO_MESSAGES => "no messages provided for {class}",
      self::NOT_A_MESSAGE => "value at {class}:{key} is not a message format string\n",
      self::FORMAT_MESSAGE_FAILED => "error formatting message '{class}:{key}': ({error_code}) {error_message}\n" .
        "locale: {locale}\n" .
        "format: {format}\n" .
        "context: {context}"
    };

    $substitutions = [];
    foreach ($context as $find => $replace) {
      if (is_string($replace)) {
        $substitutions["{{$find}}"] = $replace;
      }
    }

    return strtr("[{$this->value}] {$this->name}: {$format}", $substitutions);
  }
}
