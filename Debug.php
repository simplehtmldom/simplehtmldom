<?php namespace simplehtmldom;

/**
 * Website: http://sourceforge.net/projects/simplehtmldom/
 * Acknowledge: Jose Solorzano (https://sourceforge.net/projects/php-html/)
 *
 * Licensed under The MIT License
 * See the LICENSE file in the project root for more information.
 *
 * Authors:
 *   S.C. Chen
 *   John Schlick
 *   Rus Carroll
 *   logmanoriginal
 *
 * Contributors:
 *   Yousuke Kumakura
 *   Vadim Voituk
 *   Antcs
 *
 * Version $Rev$
 */

/**
 * Implements functions for debugging purposes. Debugging can be enabled and
 * disabled on demand. Debug messages are send to error_log by default but it
 * is also possible to register a custom debug handler.
 */
class Debug {

	private static $enabled = false;
	private static $debugHandler = null;
	private static $callerLock = array();

	/**
	 * Checks whether debug mode is enabled.
	 *
	 * @return bool True if debug mode is enabled, false otherwise.
	 */
	public static function isEnabled()
	{
		return self::$enabled;
	}

	/**
	 * Enables debug mode
	 */
	public static function enable()
	{
		self::$enabled = true;
		self::log('Debug mode has been enabled');
	}

	/**
	 * Disables debug mode
	 */
	public static function disable()
	{
		self::log('Debug mode has been disabled');
		self::$enabled = false;
	}

	/**
	 * Sets the debug handler.
	 *
	 * `null`: error_log (default)
	 */
	public static function setDebugHandler($function = null)
	{
		if ($function === self::$debugHandler) return;

		self::log('New debug handler registered');
		self::$debugHandler = $function;
	}

	/**
	 * This is the actual log function. It allows to set a custom backtrace to
	 * eliminate traces of this class.
	 */
	private static function log_trace($message, $backtrace)
	{
		$idx = 0;
		$debugMessage = '';

		foreach($backtrace as $caller)
		{
			if (!isset($caller['file']) && !isset($caller['line'])) {
				break; // Unknown caller
			}

			$debugMessage .= ' [' . $caller['file'] . ':' . $caller['line'];

			if ($idx > 1) { // Do not include the call to Debug::log
				$debugMessage .= ' '
				. $caller['class']
				. $caller['type']
				. $caller['function']
				. '()';
			}

			$debugMessage .= ']';

			// Stop at the first caller that isn't part of simplehtmldom
			if (!isset($caller['class']) || strpos($caller['class'], 'simplehtmldom\\') !== 0) {
				break;
			}

			$idx++;
		}

		$output = '[DEBUG] ' . trim($debugMessage) . ' "' . $message . '"';

		if (is_null(self::$debugHandler)) {
			error_log($output);
		} else {
			call_user_func_array(self::$debugHandler, array($output));
		}
	}

	/**
	 * Adds a debug message to error_log if debug mode is enabled. Does nothing
	 * if debug mode is disabled.
	 *
	 * @param string $message The message to add to error_log
	 */
	public static function log($message)
	{
		if (!self::isEnabled()) return;

		$backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
		self::log_trace($message, $backtrace);
	}

	/**
	 * Adds a debug message to error_log if debug mode is enabled. Does nothing
	 * if debug mode is disabled. Each message is logged only once.
	 *
	 * @param string $message The message to add to error_log
	 */
	public static function log_once($message)
	{
		if (!self::isEnabled()) return;

		// Keep track of caller (file & line)
		$backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
		if (in_array($backtrace[0], self::$callerLock, true)) return;

		self::$callerLock[] = $backtrace[0];
		self::log_trace($message, $backtrace);
	}
}
