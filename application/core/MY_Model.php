<?php
/**
 * @file
 * Extends the base model to load the PHP session upon construction.
 * Provides a unique key generator.
 */
class MY_Model extends CI_Model  {

	private $TIMEZONE_DEFAULT = "UTC";

  	public function __construct() {
    	parent::__construct();
	}

	/*
	* Returns a unique key, example : e129f27c-5103-5c5c-844b-cdf0a15e160d
	*
	*/
	protected function generate_unique_key($namepsace = null, $name = null) {
		return $this->_v5($namepsace, $name);
	}

	/**
	* Generates version 5 UUID: SHA-1 hash of URL
	*/
	private function _v5($namespace, $name) {
	    $namespace = $this->_v4();
		$name = $this->_generate_random_string();
	    // Get hexadecimal components of namespace
	    $nhex = str_replace(array('-','{','}'), '', $namespace);

	    // Binary Value
	    $nstr = '';

	    // Convert Namespace UUID to bits
	    for($i = 0; $i < strlen($nhex); $i+=2) {
	      	$nstr .= chr(hexdec($nhex[$i].$nhex[$i+1]));
	    }

	    // Calculate hash value
	    $hash = sha1($nstr . $name);

	    return sprintf('%08s-%04s-%04x-%04x-%12s',

			// 32 bits for "time_low"
			substr($hash, 0, 8),

			// 16 bits for "time_mid"
			substr($hash, 8, 4),

			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 5
			(hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x5000,

			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			(hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,

			// 48 bits for "node"
			substr($hash, 20, 12)
	    );
  	}

  	private function _generate_random_string($length = 10) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, strlen($characters) - 1)];
	    }
	    return $randomString;
	}

	/**
	* Generates version 4
	*/
	private function _v4() {
	    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

	      // 32 bits for "time_low"
	      mt_rand(0, 0xffff), mt_rand(0, 0xffff),

	      // 16 bits for "time_mid"
	      mt_rand(0, 0xffff),

	      // 16 bits for "time_hi_and_version",
	      // four most significant bits holds version number 4
	      mt_rand(0, 0x0fff) | 0x4000,

	      // 16 bits, 8 bits for "clk_seq_hi_res",
	      // 8 bits for "clk_seq_low",
	      // two most significant bits holds zero and one for variant DCE1.1
	      mt_rand(0, 0x3fff) | 0x8000,

	      // 48 bits for "node"
	      mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
	    );
	}

	/*
	* Replaces 's with \' 
	*/
	protected function make_mysql_friendly($string) {
		return str_replace("'", "\'", $string);
	}


	 /*
	*Converts the UTC/GMT UNIX standard epoch time to the user specific time zone formatted date. 
	*/
	protected function get_local_date($epoch, $format = 'F j, Y') {
		$date = new DateTime("@$epoch", new DateTimeZone($this->TIMEZONE_DEFAULT));
		if($_SESSION['user']) {
		  $date->setTimezone(new DateTimeZone($_SESSION['user']['timezone']));
		}
		return $date->format($format);
	}

	/*
	*Converts the UTC/GMT UNIX standard epoch time to the user specific time zone formatted date and time.
	*/
	protected function local_to_gmt($local_time)
	{
		return unix_to_human(local_to_gmt($local_time));
	}

	protected function gmt_to_local($gmt_time)
	{
		$daylight_savings = TRUE;
		if($_SESSION['user']) {
		  $time_zone = $_SESSION['user']['time_zone'];
		}
		else
		{
			$time_zone = "UM5";
		}
		return unix_to_human(gmt_to_local($gmt_time, $time_zone, date("I",$gmt_time)));
	}


	protected function get_local_datetime($epoch, $format='F j, Y H:i:s') {
		$date = new DateTime("@$epoch", new DateTimeZone($this->TIMEZONE_DEFAULT));
		if($_SESSION['user']) {
		  $date->setTimezone(new DateTimeZone($_SESSION['user']['time_zone']));
		}
		return $date->format($format);
	}

	protected function get_default_epoch($date) {
		date_default_timezone_set($_SESSION['user']['timezone']);
		$epoch = strtotime($date);
		$defdate = new DateTime("@$epoch",new DateTimeZone($_SESSION['user']['timezone']));
		$defdate->setTimezone(new DateTimeZone($this->TIMEZONE_DEFAULT));
		date_default_timezone_set($this->TIMEZONE_DEFAULT);
		return $defdate->getTimestamp();;
	}
  }
