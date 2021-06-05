<?php
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

namespace simplehtmldom;

use function call_user_func;
use function debug_backtrace;
use function error_log;
use function in_array;
use function strncmp;
use function trim;

/**
 * Implements functions for debugging purposes. Debugging can be enabled and
 * disabled on demand. Debug messages are send to error_log by default but it
 * is also possible to register a custom debug handler.
 */
class Debug
{
    /** @var bool */
    private static $enabled = false;

    /** @var callable|null */
    private static $debugHandler;

    /** @var array */
    private static $callerLock = [];

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
     *
     * @param callable|null $function
     */
    public static function setDebugHandler($function = null)
    {
        if ($function === self::$debugHandler) {
            return;
        }

        self::log('New debug handler registered');
        self::$debugHandler = $function;
    }

    /**
     * This is the actual log function. It allows to set a custom backtrace to
     * eliminate traces of this class.
     *
     * @param string $message
     * @param array $backtrace
     */
    private static function log_trace($message, $backtrace)
    {
        $idx = 0;
        $debugMessage = '';

        foreach ($backtrace as $caller) {
            if (! isset($caller['file']) && ! isset($caller['line'])) {
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
            if (! isset($caller['class']) || strncmp($caller['class'], 'simplehtmldom\\', 14) !== 0) {
                break;
            }
        }

        $output = '[DEBUG] ' . trim($debugMessage) . ' "' . $message . '"';

        if (self::$debugHandler === null) {
            /** @noinspection ForgottenDebugOutputInspection */
            error_log($output);
        } else {
            call_user_func(self::$debugHandler, $output);
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
        if (! self::isEnabled()) {
            return;
        }

        $backtrace = debug_backtrace();
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
        if (! self::isEnabled()) {
            return;
        }

        // Keep track of caller (file & line)
        $backtrace = debug_backtrace();
        if (in_array($backtrace[0], self::$callerLock, true)) {
            return;
        }

        self::$callerLock[] = $backtrace[0];
        self::log_trace($message, $backtrace);
    }
}
