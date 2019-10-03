<?php
require_once __DIR__ . '/../simple_html_dom.php';
use PHPUnit\Framework\TestCase;

/**
 * Tests file_get_html
 */
class file_get_html_test extends TestCase {
	private $testdata_file = __DIR__ . '/data/file_get_html/testdata.html';

	#region maxLen

	/**
	 * Files equal to maxLen should load normally.
	 * @dataProvider fileProvider
	 */
	public function test_files_equal_to_maxlen_should_load_normally($file)
	{
		$expected = file_get_contents($file);
		$size = filesize($file);

		$this->assertEquals(
			$expected,
			file_get_html(
				$file,
				false,
				null,
				0,
				$size,
				true,
				false,
				DEFAULT_TARGET_CHARSET,
				false,
				DEFAULT_BR_TEXT,
				DEFAULT_SPAN_TEXT
			)->save(),
			'Files equal to maxLen should load normally.'
		);
	}

	/**
	 * Files larger than maxLen should return false.
	 * @dataProvider fileProvider
	 */
	public function test_files_larger_than_maxlen_should_return_false($file)
	{
		$size = filesize($file);

		$this->assertFalse(
			file_get_html(
				$file,
				false,
				null,
				0,
				$size - 1,
				true,
				false,
				DEFAULT_TARGET_CHARSET,
				false,
				DEFAULT_BR_TEXT,
				DEFAULT_SPAN_TEXT
			),
			'Files larger than $maxLen should return false.'
		);
	}

	public function fileProvider()
	{
		$files = array();

		foreach(glob(__DIR__ . '/data/file_get_html/*.html') as $path) {
			$files[strtoupper(basename($path, '.html'))] = array($path);
		}

		return $files;
	}

	#endregion maxLen

}
