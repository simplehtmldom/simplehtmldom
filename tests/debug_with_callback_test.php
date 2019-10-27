<?php
require_once __DIR__ . '/../Debug.php';
use PHPUnit\Framework\TestCase;
use simplehtmldom\Debug;

/**
 * Tests the Debug class with custom callback
 */
class debug_with_callback_test extends TestCase {
	private $html;
	private $debug_message;

	protected function setUp()
	{
		Debug::setDebugHandler(array($this, 'debugMessageHandler'));
		Debug::enable();

		// Discard initial message
		$this->debug_message = null;
	}

	protected function tearDown()
	{
		Debug::disable();
		Debug::setDebugHandler();
	}

	public function debugMessageHandler($message)
	{
		$this->debug_message = $message;
	}

	public function test_enable_should_issue_a_message()
	{
		$this->assertNull($this->debug_message);
		Debug::enable();
		$this->assertNotNull($this->debug_message);
	}

	public function test_disable_should_issue_a_message()
	{
		$this->assertNull($this->debug_message);
		Debug::disable();
		$this->assertNotNull($this->debug_message);
	}

	public function test_log_should_issue_the_message()
	{
		$expected = 'Hello, World!';
		$this->assertNull($this->debug_message);
		Debug::log('Hello, World!');
		$this->assertContains($expected, $this->debug_message);
	}

	public function test_log_should_issue_the_same_message_multiple_times()
	{
		$expected = 'Hello, World!';
		$this->assertNull($this->debug_message);

		for($i = 0; $i < 2; $i++)
		{
			Debug::log('Hello, World!');
			$this->assertContains($expected, $this->debug_message);
			$this->debug_message = null;
		}
	}

	public function test_log_once_should_issue_the_message_only_once()
	{
		$this->assertNull($this->debug_message);

		for($i = 0; $i < 2; $i++)
		{
			Debug::log_once('Hello, World!');
			if ($i === 0) {
				$this->assertContains('Hello, World!', $this->debug_message);
			} else {
				$this->assertNull($this->debug_message);
			}
			$this->debug_message = null;
		}
	}
}
