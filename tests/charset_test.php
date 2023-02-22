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

	protected function tearDown()
	{
		$this->html->clear();
		unset($this->html);
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

	/** @dataProvider fileProvider */
	public function test_convert_text_should_handle_different_encodings($file)
	{
		$testdata = file_get_contents($file);
		$charset = strtoupper(basename($file, '.html'));
		$expected = iconv($charset, 'UTF-8', $testdata);

		$this->html->load(''); // We need at least the root node

		if ($charset === 'UTF-8') {
			$this->html->_charset = 'TryMe'; // Trap the parser
			// Add UTF-8 BOM
			$testdata = "\xef\xbb\xbf" . $testdata;
		} else {
			$this->html->_charset = $charset; // Hint source charset
		}

		$this->html->_target_charset = 'UTF-8'; // Enforce target charset

		$this->assertEquals($expected, $this->html->root->convert_text($testdata));
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
