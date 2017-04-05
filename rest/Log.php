<?php

class Log {
	const SEPERATOR = '##~##';
	private static $bucket = array();

	public static function info($message) {
		error_log("[cd-info] " . $message . " ");
	}

	public static function debug($message, Exception $exception = null) {
		error_log("[cd-debug] " . $exception->getTraceAsString() . " ");
	}

	public static function error($message, Exception $exception = null) {
		error_log("[cd-error] [" . @$_SERVER['REQUEST_URI'] . "] " . $message . " ");
		if(Config::DEBUG && $exception instanceof Exception) {
			self::debug($message, $exception);
		}
	}

	public static function usage($message, $write = false) {
		array_push(self::$bucket, $message);
		if($write) {
			error_log("[[" . implode(self::SEPERATOR, self::$bucket) . "]]", 3, APPLICATION_DATA . "/rest.usage_" . date('dmY'));
		}
	}
}
?>
