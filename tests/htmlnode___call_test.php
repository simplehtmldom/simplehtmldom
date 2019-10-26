<?php
require_once __DIR__ . '/../simple_html_dom.php';
use PHPUnit\Framework\TestCase;

/**
 * Tests if __call properly forwards function calls
 */
class htmlnode___call_test extends TestCase {
	private $html;

	protected function setUp()
	{
		$this->html = new simple_html_dom();
		$this->html->load('<html><head /><body /></html>');
	}

	protected function tearDown()
	{
		$this->html->clear();
		unset($this->html);
	}

	function test_children_should_return_childNodes()
	{
		$this->assertEquals($this->html->root->childNodes(), $this->html->root->children());
		$this->assertEquals($this->html->root->childNodes(0), $this->html->root->children(0));
		$this->assertEquals($this->html->root->childNodes(1), $this->html->root->children(1));
	}

	function test_first_child_should_return_firstChild()
	{
		$this->assertEquals(
			$this->html->root->firstChild(),
			$this->html->root->first_child()
		);
	}

	function test_has_child_should_return_hasChildNodes()
	{
		$this->assertEquals(
			$this->html->root->hasChildNodes(),
			$this->html->root->has_child()
		);
	}

	function test_last_child_should_return_lastChild()
	{
		$this->assertEquals(
			$this->html->root->lastChild(),
			$this->html->root->last_child()
		);
	}

	function test_next_sibling_should_return_nextSibling()
	{
		$this->assertEquals(
			$this->html->find('head', 0)->nextSibling(),
			$this->html->find('head', 0)->next_sibling()
		);
	}

	function test_prev_sibling_should_return_previousSibling()
	{
		$this->assertEquals(
			$this->html->find('body', 0)->previousSibling(),
			$this->html->find('body', 0)->prev_sibling()
		);
	}

	/**
	 * @expectedException PHPUnit\Framework\Error\Error
	 */
	function test_unknown_function_should_return_error()
	{
		$this->html->root->doSomethingStupid();
	}
}
