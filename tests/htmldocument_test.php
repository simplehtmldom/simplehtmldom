<?php
require_once __DIR__ . '/../simple_html_dom.php';
use PHPUnit\Framework\TestCase;

/**
 * Tests for simple_html_dom
 */
class htmldocument_test extends TestCase {

	private $html;

	protected function setUp()
	{
		$this->html = new simple_html_dom;
	}

	protected function tearDown()
	{
		$this->html->clear();
		unset($this->html);
	}

	public function test___get_outertext_should_return_html()
	{
		$doc = '<html></html>';
		$this->html->load($doc);

		$this->assertEquals($doc, $this->html->outertext);
	}

	public function test___get_innertext_should_return_html()
	{
		$doc = '<html></html>';
		$this->html->load($doc);

		$this->assertEquals($doc, $this->html->innertext);
	}

	public function test___get_plaintext_should_return_html_content()
	{
		$expected = 'Hello, World!';
		$doc = '<html><p>Hello, World!</p></html>';
		$this->html->load($doc);

		$this->assertEquals($expected, $this->html->plaintext);
	}

	public function test___get_charset_should_return__charset()
	{
		$expected = 'UTF-8';
		$doc = '<html><p>Hello, World!</p></html>';
		$this->html->load($doc);

		$this->assertEquals($expected, $this->html->charset);
	}

	public function test___get_target_charset_should_return__target_charset()
	{
		$expected = 'UTF-8';
		$doc = '<html><p>Hello, World!</p></html>';
		$this->html->load($doc);

		$this->assertEquals($expected, $this->html->target_charset);
	}

	public function test___get_should_return_null_for_unknown_type()
	{
		$doc = '<html></html>';
		$this->html->load($doc);

		$this->assertNull($this->html->this_type_doesnt_exist_probably);
	}

	public function test_childNodes_should_return_element_by_index()
	{
		$expected = 'PHP Simple HTML DOM Parser';
		$doc = '<p>PHP Simple HTML DOM Parser</p>';
		$this->html->load($doc);

		$this->assertEquals($expected, $this->html->childNodes(0)->text());
	}

	public function test_childNodes_should_return_null_if_index_out_of_range()
	{
		$doc = '<p>PHP Simple HTML DOM Parser</p>';
		$this->html->load($doc);

		$this->assertNull($this->html->childNodes(1));
	}

	public function test_childNodes_should_work_after_remove()
	{
		$doc = '<a href="#"></a><img><p></p>';

		$this->html->load($doc);
		$this->html->find('img', 0)->remove();

		$this->assertCount(2, $this->html->childNodes());
		$this->assertArrayHasKey(0, $this->html->childNodes());
		$this->assertArrayHasKey(1, $this->html->childNodes());
	}

	public function test_getElementById_should_return_matching_element()
	{
		$doc = '<html><p id="claim">PHP Simple HTML DOM Parser</p></html>';

		$this->html->load($doc);

		$this->assertNotNull($this->html->getElementById('claim'));
		$this->assertNull($this->html->getElementById('unknown'));
	}

	public function test_getElementsById_should_return_matching_element()
	{
		// Note, this technically doesn't make sense but it's supported
		$doc = '<html><p id="a"></p><p id="a"></p></html>';

		$this->html->load($doc);

		$this->assertCount(2, $this->html->getElementsById('a'));
	}

	public function test_getElementByTagName_should_return_matching_element()
	{
		$expected = 'Hello';
		$doc = '<html><p>Hello</p><p>World</p></html>';

		$this->html->load($doc);

		$this->assertEquals($expected, $this->html->getElementByTagName('p')->text());
	}

	public function test_getElementsByTagName_should_return_matching_element()
	{
		$doc = '<html><p>Hello</p><p>World</p></html>';

		$this->html->load($doc);

		$this->assertCount(2, $this->html->getElementsByTagName('p'));
	}

	public function test_lastChild_should_return_null_without_children()
	{
		$doc = ' ';
		$this->html->load($doc);

		$this->assertNull($this->html->lastChild());
	}

	public function test_lastChild_should_work_after_remove()
	{
		$doc = '<div><a href="#"></a><p></p></div>';

		$this->html->load($doc);
		$this->html->find('p', 0)->remove();

		$this->assertNotNull($this->html->lastChild());
	}

	public function test_createElement_should_return_an_element()
	{
		$this->assertEquals(
			'<html></html>',
			$this->html->createElement('html')->save()
		);
	}

	public function test_createElement_should_create_element_with_content()
	{
		$this->assertEquals(
			'<html>Hello, World!</html>',
			$this->html->createElement('html', 'Hello, World!')->save()
		);
	}

	/** @dataProvider dataProvider_for_createElement_should_not_affect_the_dom */
	public function test_createElement_should_not_affect_the_dom($doc)
	{
		$this->html->load($doc);

		$this->assertEquals(
			'<html></html>',
			$this->html->createElement('html')->save()
		);
		$this->assertEquals(
			'<head></head>',
			$this->html->createElement('head')->save()
		);
		$this->assertEquals(
			'<body></body>',
			$this->html->createElement('body')->save()
		);
		$this->assertEquals(
			$doc,
			$this->html->save()
		);

		$this->assertNull($this->html->find('html', 0));
		$this->assertNull($this->html->find('head', 0));
		$this->assertNull($this->html->find('body', 0));
	}

	public function dataProvider_for_createElement_should_not_affect_the_dom()
	{
		return array(
			'empty' => array(''),
			'single' => array('<div></div>'),
			'nested' => array('<table><tr></tr><tr></tr></table>'),
		);
	}

	public function test_createTextNode_should_return_a_node()
	{
		$this->assertNotNull($this->html->createTextNode('<html>'));
	}

	public function test_createTextNode_should_create_a_text_node()
	{
		$this->assertEquals(
			'Hello, World!',
			$this->html->createTextNode('Hello, World!')->save()
		);
	}

	/** @dataProvider dataProvider_for_createTextNode_should_not_affect_the_dom */
	public function test_createTextNode_should_not_affect_the_dom($doc)
	{
		$this->html->load($doc);

		$this->assertEquals(
			'Hello, World!',
			$this->html->createTextNode('Hello, World!')->save()
		);
		$this->assertEquals(
			$doc,
			$this->html->save()
		);
	}

	public function dataProvider_for_createTextNode_should_not_affect_the_dom()
	{
		return array(
			'empty' => array(''),
			'single' => array('<div></div>'),
			'nested' => array('<table><tr></tr><tr></tr></table>'),
		);
	}

}
