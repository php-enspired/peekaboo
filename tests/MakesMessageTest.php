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
  MakesMessages
};

use at\peekaboo\tests\TestCase;

/** Tests for the MakesMessages trait. */
abstract class MakesMessageTest extends TestCase {

  // private const LOCALE_DEFAULT = "en";

  // /**
  //  * Path to resource bundle used for this test.
  //  * This is specific to the base test suite and MUST NOT be used by child tests.
  //  *
  //  * @var string
  //  */
  // private const RESOURCE_BUNDLE = __DIR__ . "/resources/language/";

  // public function setUp() {
  //   $this->clearMessageRegistry();
  // }

  // public function testMakeMessagePrefersIntl() : void {
  //   $subject = $this->newSubject();
  //   $this->localize($subject, self::LOCALE_DEFAULT);
  // }

  // protected function clearMessageRegistry() : void {
  //   $this->setNonpublicStaticProperty(MessageRegistry::class, "messages", null);
  //   $this->setNonpublicStaticProperty(MessageRegistry::class, "defaultMessages", null);
  //   $this->setNonpublicStaticProperty(MessageRegistry::class, "defaultLocale", "en");
  // }

  // protected function localize(HasMessages $subject, string $locale) : void {
  //   $subject->localize($locale, new ResourceBundle($locale, self::RESOURCE_BUNDLE));
  // }

  // /**
  //  * Provides a test subject that implements HasMessages.
  //  *
  //  * This method can be overridden to provide an instance of your own implementation,
  //  *  to verify you've done it correctly and haven't broken anything.
  //  *
  //  * @return HasMessages A test subject
  //  */
  // protected function newSubject() : HasMessages {
  //   return new class implements HasMessages {
  //     use MakesMessages;
  //   };
  // }
}
