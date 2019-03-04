<?php
class csException extends Exception {

	public function __construct($message = null, $code = 0) {
		if (! $message) {
			$message = 'Error';
		}
		
		parent::__construct($message, $code);
	}
	
	public function __toString() {
		return $this -> getMessage();
	}

}
?>