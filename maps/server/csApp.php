<?php
require_once(INC_DIR.'csFunc.php');
require_once(INC_DIR.'csException.php');
require_once(INC_DIR.'simpleXml.php');

class csApp {
	public $cs_params;
	public $response;
	public $response_plain;//for no JSON responses
	public $mapdata;
	protected $mapdata_file = 'mapdata.xml';

	public $lang;
	protected $lang_default='en_US';
	protected $lang_tbl = array(
		'en_US', 'pl_PL'
	);
	protected $lang_alias_tab = array(
		'en' => 'en_US',
		'pl' => 'pl_PL'
	);

	public $trans_tbl = array();

	public function __construct() {
		$this -> cs_params = $_REQUEST;

		$this -> lang = $this -> detect_lang();

		$this -> mapdata = simplexml_load_file($this -> mapdata_file, 'ExSimpleXMLElement');
	}

	public function doit() {
		try {
			$this -> before_action();
			
			$this -> action();
			
			$this -> after_action();

			$this -> display_response();
			exit();

		} catch (csException $e) {
			
			exit();
		}
	}

	protected function before_action() {
		
	}

	protected function after_action() {
	
	}

	protected function display_response() {			
		if ($this -> response_plain) {
			echo $this -> response_plain;
		} else {
			echo json_encode($this -> response);
		}
	}

	protected function action() {
		if (isset($this -> cs_params['action']) && $this -> cs_params['action']) {
			$cs_action = "action_" . $this -> cs_params['action'];

			if (method_exists($this, $cs_action)) {
				$this -> $cs_action();
			}
		}		
	}

	protected function save_mapdata() {
		$nazwapliku = $this -> mapdata_file;
		$xmldata = $this -> mapdata -> asXml();

		if (is_writable($nazwapliku)) {
			if (!$uchwyt = fopen($nazwapliku, 'w')) {
   				throw new csException("Nie można zapisać danych do pliku XML");
 			}
			if (fwrite($uchwyt, $xmldata) === FALSE) {
  				throw new csException("Nie można zapisać danych do pliku XML");
 			}
			
			fclose($uchwyt);
			return true;
		
		} else {
 			throw new csException("Nie można zapisać danych do pliku XML, nie jest on zapisywalny");
		}
	}

	public function translate($str) {
		if (isset($this -> trans_tbl[$this -> lang][$str]) && $this -> trans_tbl[$this -> lang][$str]) {
			return $this -> trans_tbl[$this -> lang][$str];
		} else {
			return $str;
		}
	}

	public function detect_lang() {
		if (isset($this -> cs_params['lang'])) {
			$lang = $this -> cs_params['lang'];
		}
		
		if ((!isset($lang) || !in_array($lang, $this -> lang_tbl)) && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			if (isset($lang)) unset($lang);
			
			$acc_lang_tab = preg_split('/ *, */', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			
			for ($i = 0; $i < count($acc_lang_tab); $i++) {
				$arr = preg_split('/ *; */', strtolower($acc_lang_tab[$i]));
				$acc_lang = $arr[0];
				if (isset($arr[1])) $acc_lang_weight = $arr[1];
				if (isset($this->lang_alias_tab[$acc_lang]) && in_array($this->lang_alias_tab[$acc_lang], $this -> lang_tbl)) {
					$lang = $this->lang_alias_tab[$acc_lang];
					break;
				}
			}
		}
		
		if (!isset($lang) || !$lang) {
			if (isset($this -> lang_default) && in_array($this -> lang_default, $this -> lang_tbl)) {
				$lang = $this -> lang_default;
			} else {
				$lang = $this -> lang_tbl[0];
			}
		}
		
		return $lang;
	}
}
?>