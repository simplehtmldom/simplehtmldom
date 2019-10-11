<?php
require_once __DIR__ . '/../HtmlWeb.php';
use simplehtmldom\HtmlWeb;
use PHPUnit\Framework\TestCase;

/**
 * Tests the general behavior of HtmlWeb
 */
class htmlweb_test extends TestCase {
	private $web;

	protected function setUp()
	{
		$this->web = new HtmlWeb();
	}

	protected function tearDown()
	{
		unset($this->web);
	}

	public function urlProvider()
	{
		return array(
			'Empty URL' => array(''),
			'Scheme Missing' => array('//github.com/simplehtmldom/'),
			'Wrong Scheme' => array('ssh://github.com/'),
		);
	}

	/** @dataProvider urlProvider */
	public function test_load_should_return_null_for_invalid_url($url)
	{
		$this->assertNull($this->web->load($url));
	}

	public function test_load_should_return_null_without_curl_and_fopen()
	{
		if (extension_loaded('curl')) {
			$this->markTestSkipped('The cURL extension must be disabled for this test.');
		}

		if (ini_get('allow_url_fopen')) {
			$this->markTestSkipped('allow_url_fopen must be disabled for this test.');
		}

		$this->assertNull($this->web->load('https://www.google.com/'));
	}
}
