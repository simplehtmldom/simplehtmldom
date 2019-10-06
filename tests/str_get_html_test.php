<?php
require_once __DIR__ . '/../simple_html_dom.php';
use PHPUnit\Framework\TestCase;

/**
 * Tests str_get_html
 */
class str_get_html_test extends TestCase {

	/**
	 * str_get_html should return false on empty string.
	 */
	public function test_empty_string_should_return_false()
	{
		$this->assertFalse(str_get_html(''));
	}

}
