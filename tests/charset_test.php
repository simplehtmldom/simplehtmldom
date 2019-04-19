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

		$this->html = file_get_html($path);

		$this->assertEquals($expected, $this->html->_charset);
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
