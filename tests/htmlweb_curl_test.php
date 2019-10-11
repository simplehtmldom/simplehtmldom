<?php
require_once __DIR__ . '/../HtmlWeb.php';
use simplehtmldom\HtmlWeb;
use PHPUnit\Framework\TestCase;

/**
 * Tests the cURL part of HtmlWeb
 */
class htmlweb_curl_test extends TestCase {
	private $web;

	protected function setUp()
	{
		if (!extension_loaded('curl')) {
			$this->markTestSkipped('The cURL extension must be enabled for this test.');
		}

		$this->web = new HtmlWeb();
	}

	protected function tearDown()
	{
		unset($this->web);
	}

	public function urlProvider()
	{
		return array(
			'Google' => array('https://www.google.com/'),
			'GitHub' => array('https://www.github.com/'),
		);
	}

	/** @dataProvider urlProvider */
	public function test_load_should_return_dom_object($url)
	{
		$this->assertNotNull($this->web->load($url));
	}

	public function test_load_should_return_null_on_negative_response()
	{
		$this->assertNull($this->web->load('https://simplehtmldom.sourceforge.io/a.html'));
	}

	public function test_load_should_return_null_for_pages_larger_than_max_file_size()
	{
		$this->assertNull($this->web->load('https://www.w3.org/TR/html52/single-page.html'));
	}
}
