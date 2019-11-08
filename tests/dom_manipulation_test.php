<?php
require_once __DIR__ . '/../simple_html_dom.php';
use PHPUnit\Framework\TestCase;

/**
 * Tests the DOM manipulation ability of the parser
 */
class dom_manipulation_test extends TestCase {
	private $dom;

	protected function setUp()
	{
		$this->dom = new simple_html_dom();
	}

	protected function tearDown()
	{
		$this->dom->clear();
		unset($this->dom);
	}

	public function test_dom_should_accept_nested_elements()
	{
		$expected = '<html><head></head><body></body></html>';

		$html = $this->dom->createElement('html');
		$head = $this->dom->createElement('head');
		$body = $this->dom->createElement('body');

		$this->dom->root->appendChild($html);

		$html
		->appendChild($head)
		->appendChild($body);

		$this->assertEquals($expected, $this->dom->save());
	}

	public function test_dom_should_find_added_elements()
	{
		$html = $this->dom->createElement('html');
		$head = $this->dom->createElement('head');
		$body = $this->dom->createElement('body');

		$this->dom->root->appendChild($html);

		$html
		->appendChild($head)
		->appendChild($body);

		$this->assertNotNull($this->dom->find('html', 0));
		$this->assertNotNull($this->dom->find('head', 0));
		$this->assertNotNull($this->dom->find('body', 0));
	}

	public function test_dom_should_find_elements_added_to_existing_dom()
	{
		$this->dom->load('<html></html>');

		$head = $this->dom->createElement('head');
		$body = $this->dom->createElement('body');

		$this->dom->find('html', 0)
		->appendChild($head)
		->appendChild($body);

		$this->assertNotNull($this->dom->find('html', 0));
		$this->assertNotNull($this->dom->find('head', 0));
		$this->assertNotNull($this->dom->find('body', 0));
	}

	public function test_dom_should_find_elements_added_to_existing_nested_dom()
	{
		$this->dom->load('<html><body></body></html>');

		$table = $this->dom->createElement('table');
		$tr = $this->dom->createElement('tr');

		$this->dom->find('body', 0)->appendChild($table);
		$table->appendChild($tr);

		$this->assertNotNull($this->dom->find('table', 0));
		$this->assertNotNull($this->dom->find('tr', 0));
	}

	public function test_dom_should_find_elements_add_in_reverse()
	{
		$html = $this->dom->createElement('html');
		$head = $this->dom->createElement('head');
		$body = $this->dom->createElement('body');

		$html
		->appendChild($head)
		->appendChild($body);

		$this->dom->root->appendChild($html);

		$this->assertNotNull($this->dom->find('html', 0));
		$this->assertNotNull($this->dom->find('head', 0));
		$this->assertNotNull($this->dom->find('body', 0));
	}
}
