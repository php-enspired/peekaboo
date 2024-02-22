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

use at\peekaboo\MessageException;

/**
 * For classes that build ICU messages.
 */
interface HasMessages {

  /**
   * Finds and builds a message with the given key and context, if one exists.
   *
   * @param string $key Message identifier
   * @param array $context Contextual information for message replacements
   * @throws MessageException MessageError::NoMessages if no matching message is found
   * @throws MessageException MessageError::NotAMessage if key is found but is not a formatting string
   * @return string Formatted message on success
   */
  public function makeMessage(string $key, array $context) : string;
}
