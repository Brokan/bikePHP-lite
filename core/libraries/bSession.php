<?php
/**
 * bikePHP 0.x framework system file
 * Class work with page request parametrs
 * 
 * Version history:
 * 1.0.0 (2014-10-25) - Created library
 *
 * @copyright Eduard Brokan, 2012
 * @version 1.0.0 (2014-10-25)
 */
class bSession {
    
    /**
     * Is session is started
     * @var Boolean 
     */
    private static $started = FALSE;

    /**
     * Start session
     */
    public static function start() {
        if (self::$started == FALSE) {
            session_start();
            self::$started = TRUE;
        }
    }

    /**
     * Set session value for key
     * @param String $key
     * @param Object $value
     */
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    /**
     * Get session value
     * @param String $key
     * @param Object $default (Optional) Default value
     * @return Object Session value if is set
     */
    public static function get($key, $default=NULL) {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        } else {
            return $default;
        }
    }

    /**
     * Clear session
     */
    public static function unseted() {
        if (self::$_is_started == TRUE) {
            session_unset();
        }
    }

    /**
     * Clear and than destroy session
     */
    public static function destroy() {
        if (self::$_is_started == TRUE) {
            session_unset();
            session_destroy();
        }
    }

}