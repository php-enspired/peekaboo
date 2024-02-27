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

use ResourceBundle;

use at\peekaboo\ {
  HasMessages,
  MakesMessages,
  MessageRegistry
};

use at\peekaboo\tests\TestCase;

/** Tests for the MakesMessages trait. */
class MakesMessagesTest extends TestCase {

  protected const MESSAGE_EXPECTATIONS = [
    [
      "top-level-key",
      [],
      "hello, world",
      "hello, world"
    ],
    [
      "nested.key",
      [],
      "hello again, world",
      "hello again, world"
    ],
    [
      "missing-from-intl-bundle",
      [],
      "hello, world",
      "hello, world"
    ],
    [
      "simple-replacement",
      ["name" => "world"],
      "hello, world",
      "hello, world"
    ],
    [
      "escaped-characters",
      [],
      "this isn't {obvious}",
      "this isn't {obvious}"
    ],
    [
      "predefined-styles.date-medium",
      ["footprint" => -14241600],
      "one small step for man on Jul 20, 1969",
      "one small step for man on -14241600"
    ],
    [
      "predefined-styles.number-currency",
      ["price" => 20],
      "that will set you back about $20",
      "that will set you back about 20"
    ],
    [
      "predefined-styles.number-integer-width",
      ["id" => 7],
      "agent 007",
      "agent 7"
    ]
  ];

  protected static HasMessages $instance;
  protected static ResourceBundle $bundle;

  public static function setUpBeforeClass() : void {
    self::$instance = new class() implements HasMessages {
      use MakesMessages;

      public const MESSAGES = [
        "top-level-key" => "hello, world",
        "nested" => ["key" => "hello again, world"],
        "missing-from-intl-bundle" => "hello, world",
        "simple-replacement" => "hello, {name}",
        "escaped-characters" => "this isn''t '{obvious}'",
        "predefined-styles" => [
          "date-medium" => "one small step for man on {footprint}",
          "number-currency" => "that will set you back about {price}",
          "number-integer-width" => "agent {id}"
        ]
      ];
    };

    MessageRegistry::$defaultLocale = "en_US";

    if (extension_loaded("intl")) {
      self::$bundle = new ResourceBundle("root", __DIR__ . "/resources");
    }
  }

  public function tearDown() : void {
    $this->setNonpublicStaticProperty(MessageRegistry::class, 'messages', []);
  }

  /** @dataProvider messageFormattingProvider */
  public function testMessageFormatting(
    string $key,
    array $context,
    string $expectedIntl,
    string $expectedFallback
  ) : void {
    $this->assertSame(
      $expectedFallback,
      self::$instance->makeMessage($key, $context),
      "fallback message"
    );

    if (extension_loaded("intl")) {
      MessageRegistry::localize("root", self::$bundle);
      $this->assertSame(
        $expectedIntl,
        self::$instance->makeMessage($key, $context),
        "intl message"
      );
    }
  }

  public static function messageFormattingProvider() : array {
    return self::MESSAGE_EXPECTATIONS;
  }
}
