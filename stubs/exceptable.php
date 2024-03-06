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

/*
 * These are stubs for internal usage when at\exceptable is not loaded.
 * We don't make at\exceptable a direct requirement in order to avoid circular dependencies.
 */

namespace at\exceptable {

  use RuntimeException as SplRuntimeException,
    Throwable;

  use at\exceptable\ {
    Error,
    Exceptable,
    ExceptableError
  };

  use at\peekaboo\HasMessages;

  if (! interface_exists(Error::class, true)) {

    /** @see Error */
    interface Error extends HasMessages {
      public function __invoke(array $context = [], Throwable $previous = null) : Exceptable;
      public function code() : int;
      public function newExceptable(array $context = [], Throwable $previous = null) : Exceptable;
      public function message(array $context) : string;
    }
  }

  if (! interface_exists(Exceptable::class, true)) {

    /** @see Exceptable */
    interface Exceptable extends Throwable {
      public function __construct(Error $e = null, array $context = [], Throwable $previous = null);
      public function context() : array;
      public function error() : Error;
      public function has(Error $e) : bool;
      public function is(Error $e) : bool;
      public function root() : Throwable;
    }
  }
}

namespace at\exceptable\Spl {

  use RuntimeException as SplRuntimeException,
    Throwable;

  use at\exceptable\ {
    Error,
    Exceptable,
    Spl\RuntimeException
  };

  if (! class_exists(RuntimeException::class, true)) {

    /** @see RuntimeException */
    class RuntimeException extends SplRuntimeException implements Exceptable {

      public static function from(Error $e = null, array $context = [], Throwable $previous = null) : Exceptable {
        return new self($e, $context, $previous);
      }

      public function __construct(
        protected ? Error $error = null,
        protected array $context = [],
        Throwable $previous = null
      ) {
        $this->error ??= MessageError::UnknownError;
        parent::__construct($this->error->message($context), $this->error->code(), $previous);
      }

      public function context() : array {
        return $this->context;
      }

      public function error() : Error {
        return $this->error;
      }

      public function has(Error $e) : bool {
        $ex = $this;
        while ($ex instanceof Throwable) {
          if ($ex instanceof Exceptable && $ex->error === $e) {
            return true;
          }

          $ex = $ex->getPrevious();
        }

        return false;
      }

      public function is(Error $e) : bool {
        return $this->error === $e;
      }

      public function root() : Throwable {
        $root = $this;
        while (($previous = $root->getPrevious()) instanceof Throwable) {
          $root = $previous;
        }

        return $root;
      }
    }
  }
}
