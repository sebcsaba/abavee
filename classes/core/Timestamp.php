<?php

/**
 * Represents a date and time
 * 
 * @author sebcsaba
 */
class Timestamp {
	
	/**
	 * The stored unix timestamp
	 *
	 * @var int
	 */
	private $seconds;
	
	/**
	 * Create a timestamp by parsing a string
	 *
	 * @param string $dateString
	 * @return Timestamp
	 */
	public static function parse($dateString) {
		if (!preg_match('/^ *(\d+)[-\.](\d+)[-\.](\d+)( *T? *(\d+):(\d+)(:(\d+))?)? *$/',$dateString,$matches)) {
			throw new Expression('unable to parse date: '.$dateString);
		}
		while (count($matches)<=8) $matches []= 0;
		$newDateString = sprintf('%d-%d-%d %d:%d:%d',$matches[1],$matches[2],$matches[3],$matches[5],$matches[6],$matches[8]);
		$parsedTime = strtotime($newDateString);
		if ($parsedTime===false) {
			throw new Exception('unable to parse formatted date: '.$newDateString.' (orig: '.$dateString.')');
		}
		return new Timestamp($parsedTime);
	}
	
	/**
	 * Create a timestamp from a unix timestamp
	 *
	 * @param integer $seconds
	 */
	public function __construct($seconds = null) {
		if (is_null($seconds)) {
			$seconds = time();
		}
		$this->seconds = $seconds;
	}
	
	/**
	 * Converts the date to a full string
	 * 
	 * @return string
	 */
	public function __toString() {
		return $this->toSecondsString();
	}
	
	/**
	 * Converts the timestamp to a string with second precision
	 * 
	 * @return string
	 */
	public function toSecondsString() {
		return date('Y-m-d H:i:s',$this->seconds);
	}
	
	/**
	 * Converts the timestamp to a string with minute precision
	 * 
	 * @return string
	 */
	public function toMinuteString() {
		return date('Y-m-d H:i',$this->seconds);
	}
	
	/**
	 * Converts the timestamp to a string with day precision
	 * 
	 * @return string
	 */
	public function toDayString() {
		return date('Y-m-d',$this->seconds);
	}

	/**
	 * Converts the timestamp to a string using the given format pattern.
	 * 
	 * @param string $pattern A pattern that is accepted by the PHP date() function
	 * @return string
	 */
	public function toUserString($pattern) {
		return date($pattern,$this->seconds);
	}

	/**
	 * Returns the stored unix timestamp
	 * 
	 * @return int
	 */
	public function toUnixTimestamp(){
		return $this->seconds;
	}
	
	/**
	 * Returns true, if this time is before the parameter
	 *
	 * @param Timestamp $that
	 * @return boolean
	 */
	public function isBefore(Timestamp $that) {
		return $this->seconds < $that->seconds;
	}
	
	/**
	 * Returns true, if this time is after the parameter
	 *
	 * @param Timestamp $that
	 * @return boolean
	 */
	public function isAfter(Timestamp $that) {
		return $this->seconds > $that->seconds;
	}
	
	/**
	 * Returns true, if this time is the same second than the parameter
	 *
	 * @param Timestamp $that
	 * @return boolean
	 */
	public function isEquals(Timestamp $that) {
		return $this->seconds == $that->seconds;
	}
	
	/**
	 * Returns the difference between the timestamps, in seconds.
	 * If $this is later, the result will be positive.
	 *
	 * @param Timestamp $that
	 * @return int
	 */
	public function getDifferenceSecond(Timestamp $that) {
		return $this->seconds - $that->seconds;
	}
	
	/**
	 * Returns the difference between the timestamps, in days.
	 * If $this is later, the result will be positive.
	 * 
	 * @param Timestamp $that
	 * @return int
	 */
	public function getDifferenceDay(Timestamp $that) {
		return $this->getDifferenceSecond($that) / 86400;
	}
	
	/**
	 * Returns a new timestamps that is later than this by the given amount of seconds.
	 *
	 * @param integer $seconds
	 * @return Timestamp
	 */
	public function addSeconds($seconds=1) {
		return new Timestamp($this->seconds + $seconds);
	}
	
	/**
	 * Returns a new timestamps that is later than this by the given amount of days.
	 *
	 * @param integer $days
	 * @return Timestamp
	 */
	public function addDays($days=1) {
		return new Timestamp($this->seconds + $days * 86400);
	}
	
}
