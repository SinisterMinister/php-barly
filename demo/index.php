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

// Require Barly
 require_once('../handlebars.php');

// Set the handlebars location
 \Barly\Handlebars::$handlebars_location = '../handlebars-1.0.rc.1.js';

 // Build the template data
$data = array(
	'title' => 'Barly Demo',
	'body' => 'This is a demonstration of handlebars and PHP working together!',
	'list' => array('one', 'two', 'three'),
	'footer' => '(c) 2013 SinisterMinister'
);

// Start a timer
$time = microtime(TRUE);

// Get the template
$template = @file_get_contents('templates/demo.hbs');

// Make sure it exists
 if ( ! $template)
 	throw new Exception('Could not load the template!');

 $template = \Barly\Handlebars::compile($template);

// Output the template
try
{
	$time = microtime(TRUE);

	echo \Barly\Handlebars::render($template, $data);

	$stop = microtime(TRUE);

	$v8_time = ($stop - $time) * 1000;
	echo 'Built in V8 in '.number_format($v8_time, 3).'ms';
}
catch(V8JsException $e)
{
	echo $template."\n\n";
	echo $e->getJsSourceLine()."\n\n";
	echo $e->getJsTrace();
}

echo '<br /><br />';

// Start a timer
$time = microtime(TRUE);

require_once('pure-php.php');

$stop = microtime(TRUE);

$php_time = ($stop - $time) * 1000;

echo 'Built in PHP in '.number_format($php_time, 3).'ms <br />';

$v8_slow = $v8_time / $php_time;

echo 'V8 is '.number_format($v8_slow*100).'% slower!';