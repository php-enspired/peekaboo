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

// if intl is loaded, then the autoloader shouldn't even try us. but just in case.
if (extension_loaded("intl")) {
  return;
}

/**
 * @internal
 * @see https://php.net/ResourceBundle
 *
 * This is a stub/fallback for internal usage when ext/intl is not loaded.
 * ResourceBundle->getErrorCode() and ->getErrorMessage() always tell you "no error."
 * Other methods are not emulated.
 */
abstract class ResourceBundle {

  public function getErrorCode() : int {
    return 0;
  }

  public function getErrorMessage() : string {
    return "U_ZERO_ERROR";
  }

  abstract public function count() : int;
  abstract public function get($key, bool $fallback = true) : mixed;
}
