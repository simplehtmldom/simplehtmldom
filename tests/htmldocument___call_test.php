<?php
require_once __DIR__ . '/../simple_html_dom.php';
use PHPUnit\Framework\TestCase;

/**
 * Tests if __call properly forwards function calls
 */
class htmldocument___call_test extends TestCase {
	private $html;

	protected function setUp()
	{
		$this->html = new simple_html_dom();
	}

	protected function tearDown()
	{
		$this->html->clear();
		unset($this->html);
	}

	function test_load_file_should_return_loadFile()
	{
		$file = __DIR__ . '/data/htmldocument___call/testdata.html';

		$this->assertEquals(
			$this->html->loadFile($file),
			$this->html->load_file($file)
		);
	}

	/**
	 * @expectedException PHPUnit\Framework\Error\Error
	 */
	function test_unknown_function_should_return_error()
	{
		$this->html->doSomethingStupid();
	}
}
