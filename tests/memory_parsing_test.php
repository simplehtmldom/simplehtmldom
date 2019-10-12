<?php
require_once __DIR__ . '/../simple_html_dom.php';
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestResult;

/**
 * Test memory behavior of parsing operations
 *
 * **Note**:
 *
 * These tests are based on /testcase/memory_test.php
 */
class memory_parsing_test extends TestCase {
	/** File for memory tests */
	private $file = __DIR__ . '/data/memory/index.html';

	public function setUp()
	{
		/**
		 * The first time we access a file, PHP acquires additional memory that
		 * breaks some assertions. For some reason, loading the contents once
		 * fixes this issue.
		 */
		gc_enable();
		$contents = file_get_contents($this->file, false, null, 0, filesize($this->file));
		$html = new simple_html_dom($contents);
		unset($html);
		$contents = null;
		$file = null;
		gc_collect_cycles();
	}

	/**
	 * Test if the parser properly releases memory using simple_html_dom (50x)
	 *
	 * Memory usage should stay stable when using the parser in a loop.
	 */
	public function test_simple_html_dom()
	{
		$contents = file_get_contents($this->file, false, null, 0, filesize($this->file));

		if (is_file($this->file)) {
			// Cleanup before doing anything
			gc_enable();
			gc_collect_cycles();

			for ($i = 0; $i <= 50; $i++) {
				$memory_start = memory_get_usage();

				$html = new simple_html_dom($contents);
				unset($html);
				gc_collect_cycles(); // Trigger garbage collection

				$memory_end = memory_get_usage();

				$this->assertEquals($memory_start, $memory_end, 'Iteration: ' . $i);
			}
		} else {
			throw new Exception('Unable to perform test, file doesn\'t exist!');
		}
	}

	/**
	 * Test if the parser properly releases memory using loadFile (50x)
	 *
	 * Memory usage should stay stable or slightly decrease (out of our control)
	 * when using the parser in a loop.
	 */
	public function test_loadFile()
	{

		if (is_file($this->file)) {
			// Cleanup before doing anything
			gc_enable();
			gc_collect_cycles();

			for ($i = 0; $i <= 50; $i++) {
				$memory_start = memory_get_usage();

				$html = new simple_html_dom();
				$html->loadFile($this->file, false, null, 0, filesize($this->file));
				unset($html);
				gc_collect_cycles(); // Trigger garbage collection

				$memory_end = memory_get_usage();

				$this->assertEquals($memory_start, $memory_end, 'Iteration: ' . $i);
			}
		} else {
			throw new Exception('Unable to perform test, file doesn\'t exist!');
		}
	}

	/**
	 * Test if the parser correctly handles large files (optional)
	 *
	 * Uses the single page representation of the HTML Specification to perform
	 * tests on large files (>10 MB).
	 *
	 * @link https://www.w3.org/TR/html/single-page.html HTML Specification (single page)
	 */
	public function test_large_file()
	{
		// Note: The HTML Specification is VERY large (> 10 MB) and takes a very
		// long time to download. Thus, it should be placed in a local directory
		$file = __DIR__ . '/data/HTML 5.2.html';

		if (!is_file($file)) {
			$this->markTestSkipped(
				'Download the HTML Specification as single page to "' . $file . '"'
			);
		}

		// Cleanup before doing anything
		gc_enable();
		gc_collect_cycles();

		$memory_start = memory_get_usage();

		// Use actual file size to load the entire file
		$html = new simple_html_dom;
		$html->load($file);
		unset($html);
		gc_collect_cycles(); // Trigger garbage collection

		$memory_end = memory_get_usage();

		$this->assertEquals($memory_start, $memory_end);
	}

}
