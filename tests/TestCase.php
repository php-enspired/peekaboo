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

use BadMethodCallException,
  ReflectionClass,
  ReflectionObject;

use at\peekaboo\ {
  MessageError,
  MessageErrorCase
};

use PHPUnit\Framework\TestCase as PhpunitTestCase;

/** Base Test Case. */
abstract class TestCase extends PhpunitTestCase {

  /**
   * Sets phpunit's expectException*() methods from an example Error.
   *
   * @param MessageError $e the Error expected to be thrown
   */
  public function expectError(MessageError $case) : void {
    $this->expectException(MessageException::class);
    $this->expectExceptionCode($case->value);
  }

  /**
   * Gets the value of a nonpublic property of an object under test.
   *
   * @param object $object The object to inspect
   * @param string $property The property to access
   * @return mixed Property value on success
   */
  protected function getNonpublicProperty(object $object, string $property) {
    $ro = new ReflectionObject($object);
    if (! $ro->hasProperty($property)) {
      throw new BadMethodCallException("Object [" . $object::class . "] has no property '{$property}'");
    }

    $rp = $ro->getProperty($property);
    $rp->setAccessible(true);
    return $rp->getValue($object);
  }

  /**
   * Gets the value of a nonpublic static property of class under test.
   *
   * @param string $fqcn FQCN of the class to modify
   * @param string $property The property to access
   * @return mixed Property value on success
   */
  protected function getNonpublicStaticProperty(string $fqcn, string $property) {
    $rc = new ReflectionClass($fqcn);
    if (! $rc->hasProperty($property)) {
      throw new BadMethodCallException("Class {$fqcn} has no property '{$property}'");
    }

    $rp = $rc->getProperty($property);
    $rp->setAccessible(true);
    return $rp->getValue();
  }

  /**
   * Invokes a nonpublic method on an object under test.
   *
   * Note, it's VERY EASY to BREAK EVERYTHING using this method.
   *
   * @param object $object The object under test
   * @param string $method The method to invoke
   * @param mixed ...$args Argument(s) to use for invocation
   * @return mixed The return value from the method invocation
   */
  protected function invokeNonpublicMethod(object $object, string $method, ...$args) {
    $ro = new ReflectionObject($object);
    if (! $ro->hasMethod($method)) {
      throw new BadMethodCallException("Object [" . $object::class . "] has no method '{$method}'");
    }

    $rm = $ro->getMethod($method);
    $rm->setAccessible(true);
    return $rm->invoke($object, ...$args);
  }

  /**
   * Sets the value of a nonpublic property of an object under test.
   *
   * Note, it's VERY EASY to BREAK EVERYTHING using this method.
   *
   * @param object $object Object to modify
   * @param string $property Property to set
   * @param mixed $value Value to set
   * @return void
   */
  protected function setNonpublicProperty(object $object, string $property, $value) : void {
    $ro = new ReflectionObject($object);
    if (! $ro->hasProperty($property)) {
      throw new BadMethodCallException("Object [" . $object::class . "] has no property '{$property}'");
    }

    $rp = $ro->getProperty($property);
    $rp->setAccessible(true);
    $rp->setValue($object, $value);
  }

  /**
   * Sets the value of a nonpublic static property of a class under test.
   *
   * Note, it's VERY EASY to BREAK EVERYTHING using this method.
   *
   * @param string $fqcn FQCN of class to modify
   * @param string $property Property to set
   * @param mixed $value Value to set
   * @return void
   */
  protected function setNonpublicStaticProperty(string $fcqn, string $property, $value) : void {
    $rc = new ReflectionClass($fcqn);
    if (! $rc->hasProperty($property)) {
      throw new BadMethodCallException("Class {$fqcn} has no property '{$property}'");
    }

    $rp = $rc->getProperty($property);
    $rp->setAccessible(true);
    $rp->setValue($value);
  }
}
