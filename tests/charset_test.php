<?php
require_once __DIR__ . '/../simple_html_dom.php';
use PHPUnit\Framework\TestCase;

/**
 * Test if the parser properly detects document encodings
 */
class charset_test extends TestCase {

	private $html;

	protected function setUp()
	{
		$this->html = new simple_html_dom;
	}

	/**
	 * @dataProvider fileProvider
	 */
	public function test_charset($path)
	{
		$expected = strtoupper(basename($path, '.html'));

		$this->html->loadFile($path);

		$this->assertEquals($expected, $this->html->_charset);
	}

	/** @dataProvider fileProvider */
	public function test_is_utf8($file)
	{
		$testdata = file_get_contents($file);

		if (strtoupper(basename($file, '.html')) === 'UTF-8') {
			$this->assertTrue(simple_html_dom_node::is_utf8($testdata));
		} else {
			$this->assertFalse(simple_html_dom_node::is_utf8($testdata));
		}
	}

	public function fileProvider()
	{
		$files = array();

		foreach(glob(__DIR__ . '/data/charset/*.html') as $path) {
			$files[strtoupper(basename($path, '.html'))] = array($path);
		}

		return $files;
	}

}
