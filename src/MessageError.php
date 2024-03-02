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
namespace at\peekaboo;

use Throwable;

use at\exceptable\ {
  Error,
  Exceptable
};
require_once __DIR__ . "/../stubs/exceptable.php";

use at\peekaboo\ {
  MakesMessages,
  MessageException
};

/** @phan-suppress PhanInvalidConstantExpression */
enum MessageError : int implements Error {
  use MakesMessages;

  case UnknownError = 0;
  case NoMessages = 1;
  case NotAMessage = 2;
  case FormatFailed = 3;
  case BadMessages = 4;

  public const MESSAGES = [
    self::UnknownError->name => "unknown error",
    self::NoMessages->name => "no messages provided for {class}",
    self::NotAMessage->name => "value at {bundle}:{key} is not a message format string",
    self::FormatFailed->name => "error formatting message: ({error_code}) {error_message}\n" .
      "locale: {locale}\n" .
      "format: {format}\n" .
      "context: {context}",
    self::BadMessages->name => "MakesMessages::MESSAGES must be an array of message formats; {type} declared"
  ];

  public function __invoke(array $context = [], Throwable $previous = null) : Exceptable {
    return new MessageException($this, $context, $previous);
  }

  public function code() : int {
    return $this->value;
  }

  public function message(array $context) : string {
    $f = self::MESSAGES[$this->name];
    $r = [];
    foreach ($context as $k => $v) {
      $r["{{$k}}"] = $v;
    }
    return strtr($f, $r);

    return $this->makeMessage($this->name, $context);
  }

  public function newExceptable(array $context = [], Throwable $previous = null) : Exceptable {
    return new MessageException($this, $context, $previous);
  }
}
