<?php
/**
 * @package    at.peekaboo
 * @subpackage tests
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
namespace at\peekaboo\tests;

use at\peekaboo\MessageFormatter;
use at\peekaboo\tests\TestCase;

require_once __DIR__ . "/../stubs/MessageFormatter.php";

/** Tests for the MessageFormatter class. */
class MessageFormatterTest extends TestCase {

  /** @dataProvider formatTestProvider */
  public function testFormat(string $format, array $context, string $expected) {
    $this->assertSame(
      $expected,
      (new MessageFormatter("root", $format))->format($context)
    );
  }

  /**
   * @return array[]
   *  - string $0 Message format
   *  - string[] $1 Contextual replacements
   *  - string $2 Expected result
   */
  public static function formatTestProvider() : array {
    return [
      "simple token" => [
        "hello, {token}!",
        ["token" => "world"],
        "hello, world!"
      ],
      "intl token" => [
        "hello, {token, with {{intl} junk}}!",
        ["token" => "world"],
        "hello, world!"
      ],
      "token with whitespace" => [
        "a malformed { token} appears",
        ["token" => "world"],
        "a malformed world appears"
      ],
      "escaped single quote" => [
        "well that wasn''t expected",
        [],
        "well that wasn't expected"
      ],
      "escaped braces" => [
        "this is not a '{token}'!",
        ["token" => "world"],
        "this is not a {token}!"
      ]
    ];
  }
}
