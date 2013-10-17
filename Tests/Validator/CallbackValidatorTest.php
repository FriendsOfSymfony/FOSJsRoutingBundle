<?php

namespace FOS\JsRoutingBundle\Tests\Validator;

use FOS\JsRoutingBundle\Validator\CallbackValidator;

class CallbackValidatorTest extends \PHPUnit_Framework_TestCase
{
    const IS_VALID   = true;

    const IS_INVALID = false;

    /**
     * @dataProvider dataProviderForTestIsValid
     */
    public function testIsValid($callback, $expected)
    {
        $validator = new CallbackValidator();
        $this->assertEquals($expected, $validator->isValid($callback));
    }

    public static function dataProviderForTestIsValid()
    {
        return array(
            array('foo',                          self::IS_VALID),
            array('foo123',                       self::IS_VALID),
            array('fos.Router.data',              self::IS_VALID),
            array('$.callback',                   self::IS_VALID),
            array('_.callback',                   self::IS_VALID),
            array('hello',                        self::IS_VALID),
            array('foo23',                        self::IS_VALID),
            array('$210',                         self::IS_VALID),
            array('_bar',                         self::IS_VALID),
            array('some_var',                     self::IS_VALID),
            array('$',                            self::IS_VALID),
            array('somevar',                      self::IS_VALID),
            array('$.ajaxHandler',                self::IS_VALID),
            array('array_of_functions[42]',       self::IS_VALID),
            array('array_of_functions[42][1]',    self::IS_VALID),
            array('$.ajaxHandler[42][1].foo',     self::IS_VALID),
            array('array_of_functions["key"]',    self::IS_VALID),
            array('_function',                    self::IS_VALID),
            array('petersCallback1412331422[12]', self::IS_VALID),
            array('(function xss(x){evil()})',    self::IS_INVALID),
            array('',                             self::IS_INVALID),
            array('alert()',                      self::IS_INVALID),
            array('test()',                       self::IS_INVALID),
            array('a-b',                          self::IS_INVALID),
            array('23foo',                        self::IS_INVALID),
            array('function',                     self::IS_INVALID),
            array(' somevar',                     self::IS_INVALID),
            array('$.23',                         self::IS_INVALID),
            array('array_of_functions[42]foo[1]', self::IS_INVALID),
            array('array_of_functions[]',         self::IS_INVALID),
            array('myFunction[123].false',        self::IS_INVALID),
            array('myFunction .tester',           self::IS_INVALID),
            array(':myFunction',                  self::IS_INVALID),
        );
    }
}
