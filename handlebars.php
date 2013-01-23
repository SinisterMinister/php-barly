<?php

/* Copyright (C) 2012 Codey Whitt
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 */

namespace Barly;

// Pull in V8 for use
require_once('v8.php');

class Handlebars {

	/**
	 * @var  string  Location of the handlebars file
	 */
	public static $handlebars_location = 'handlebars-1.0.rc.1.js';

	/**
	 * @var  V8Js  Container for the V8Js instance
	 */
	protected static $_v8;

	/**
	 * Initializes V8 with handlebars.
	 * @return void
	 */
	protected static function _init()
	{
		// Make sure we haven't already initialized
		if (self::$_v8 !== NULL)
			return;

		// Setup the V8 instance
		self::$_v8 = new \Barly\V8();

		// Load Handlebars for use
		$handlebars = @file_get_contents(self::$handlebars_location);

		// Make sure that we actually loaded it
		if ( ! $handlebars)
			throw new \Exception('The specified handlebars file could not be loaded or found!');

		// Load HB into V8
		self::$_v8->executeString($handlebars);
	}

	/**
	 * Compiles a handlebars template for use. Adds the template to the global
	 * scope under `Barly.template`.
	 * 
	 * @param string $template 
	 * @return string  String representation of the compiled template function
	 */
	public static function compile($template)
	{
		// Fire off init to make sure we've loaded V8
		self::_init();

		// Add template to the V8 instance
		self::$_v8->addVariable('Barly', array('template' => $template));

		// Send the template to handlebars for parsing and return it
		return self::$_v8->executeString("Handlebars.precompile(Barly.template);");
	}

	/**
	 * Compiles a handlebar template using the provided data.
	 * @param string $template 
	 * @param mixed $data 
	 * @return string
	 */
	public static function render($template, $data)
	{
		// Fire off init to make sure we've loaded V8
		self::_init();

		// Add the template and data to V8
		self::$_v8->addVariable('Barly', array('template' => $template, 'data' => $data));

		// Check to see if the template was already compiled
		try
		{
			self::$_v8->executeString("(function(){return eval('('+Barly.template+')')})();");
		}
		catch (\V8JsException $e)
		{
			// Compile the template
			$compiled_template = self::compile($template);

			// Add the template and data to V8
			self::$_v8->addVariable('Barly', array('template' => $compiled_template, 'data' => $data));
		}

		$js = "(function (Barly){"
		    . "    // Eval the string into a function\n"
		    . "    var template = eval('('+Barly.template+')');"
		    . "    return Handlebars.template(template)(Barly.data);"
		    . "})(Barly);";

		// Compile the template and return it
		return self::$_v8->executeString($js);
	}

	/**
	 * Registers a helper with handlebars. Function must be written in Javascript
	 * and passed as a string!
	 * 
	 * @param string $name 
	 * @param string $function 
	 * @return void
	 */
	public static function register_helper($name, $function)
	{
		// Fire off init to make sure we've loaded V8
		self::_init();

		// Add variables to the V8 instance
		self::$_v8->addVariable('Barly', array('name' => $name, 'func' => $function));

		// Register the helper
		self::$_v8->executeString("(function (Barly){return Handlebars.registerHelper(Barly.name, eval('('+Barly.func+')'));})(Barly);");
	}
}