<?php
require_once __DIR__ . '/../simple_html_dom.php';
use PHPUnit\Framework\TestCase;

/**
 * Checks if the parser properly handles server-side scripts
 */
class server_side_script_test extends TestCase {

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

	public function test_html_inside_sss_should_not_appear_in_the_dom()
	{
		$this->html->load('<?php <div>Hello, World!</div> ?>');
		$this->assertNull($this->html->find('div', 0));
	}
}
