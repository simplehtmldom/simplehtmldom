<?php
require_once __DIR__ . '/../simple_html_dom.php';
use PHPUnit\Framework\TestCase;

/**
 * Tests the callback feature of the parser
 */
class callback_test extends TestCase {
	private $html;

	protected function setUp()
	{
		$this->html = new simple_html_dom();
		$this->html->set_callback(
			function($element)
			{
				$element->tag = 'surprise';
			}
		);
	}

	protected function tearDown()
	{
		$this->html->clear();
		unset($this->html);
	}

	public function test_htmldocument_set_callback_should_register_function()
	{
		$this->assertNotNull($this->html->callback);
	}

	public function test_htmldocument_remove_callback_should_unregister_function()
	{
		$this->html->remove_callback();
		$this->assertNull($this->html->callback);
	}

	public function test_htmlnode_outertext_uses_callback_function()
	{
		$expected = '<surprise></surprise>';
		$this->html->load('<html></html>');
		$this->assertEquals($expected, $this->html->save());
	}
}
