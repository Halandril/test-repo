<?php
require_once(INC_DIR.'csAppMap.php');
include('log.php');

class csAppMapAdmin extends csAppMap {
	
	protected $logged_in = false;
	public $warning = '';
	public $success = '';
	protected $hsalt = HSALT;
	
	public function __construct() {
		$this -> trans_tbl['pl_PL'] = array(
			'To long session time. Log in again.' => 'Zbyt długi czas sesji. Zaloguj się ponownie.',
			'Wrong username or password.' => 'Nieprawidłowy login lub hasło.',
			'Password is too short. At least 6 characters reqired.' => 'Hasło jest zbyt krótkie. Powinno mieć co najmniej 6 znaków.',
			'Password does not match its repetition.' => 'Hasło nie pasuje do jego powtórzenia',
			'Login is required.' => 'Login jest wymagany',
			'Access data has not changed.' => 'Dane dostępowe nie zostały zmienione',
			'Access data changed.' => 'Dane dostępowe zostały zmienione.',
			'Internal error.' => 'Błąd wewnętrzny',
			'Error' => 'Błąd',
			'Go to main map' => 'Przejdź do mapy głównej',
			'Wrong login or password.' => 'Nieprawidłowy login lub hasło',
			'Log in' => 'Zaloguj się',
			'Login' => 'Login',
			'Password' => 'Hasło',
			'Sign in' => 'Zaloguj się',
			'mapArtisan admin panel' => 'mapArtisan - panel administracyjny'
		);

		parent::__construct();
		if ($this -> login_check()) {
			$this -> logged_in = true;
			$this -> mapsettings['username'] = ADMIN_LOGIN;

		} else {
			$this -> logged_in = false;
		}		
	}

	public function doit() {
		if (!$this -> logged_in) {
			exit();
		} else {
			return parent::doit();
		}
	}

	public function make_login() {
		//TODO: moze sie zrobic kicha po np. poprawnej zmianie hasla, a pozniej odswiezeniu strony

		//if logout
		if (isset($this -> cs_params['action']) && $this -> cs_params['action'] == 'logout') {
			if (!isset($_SESSION)) session_start();
			session_destroy();
			$this -> logged_in = false;
		}

		// if passchange
		if (isset($this -> cs_params['new_password']) && isset($this -> cs_params['new_login']) && isset($this -> cs_params['re_new_password'])) {
			$this ->  save_new_pass($this -> cs_params['new_password'], $this -> cs_params['re_new_password'], $this -> cs_params['new_login']);
			$this -> logged_in = false;
		}

		if ($this -> logged_in) return true;

		
		

		// wyswietlenie strony logowania
		include('login.php');
		exit();
	}

	protected function save_new_pass($pass, $re_pass, $login) {
			if (!$pass || $pass != $re_pass) {
				$this -> warning = $this -> translate('Error').':'.
				$this -> translate('Password does not match its repetition.').'<br>'.
				$this -> translate('Acces data has not changed.');
				return;
			}
			
			if (strlen($pass) < 6) {
				$this -> warning = $this -> translate('Error').':'.
				$this -> translate('Password is too short. At least 6 characters reqired.').'<br>'.
				$this -> translate('Acces data has not changed.');
				return;
			}

			if (!$login) {
				$this -> warning = $this -> translate('Error').':'.
				$this -> translate('Login is required.').'<br>'.
				$this -> translate('Acces data has not changed.');
				return;
			}
			
			if (!is_writable('log.php')) {
				$this -> warning = $this -> translate('Internal error.').'<br>'.
				$this -> translate('Acces data has not changed.');
				return;
			}

			if (!$uchwyt = fopen('log.php', 'w')) {
   			$this -> warning = $this -> translate('Internal error.').'<br>'.
				$this -> translate('Acces data has not changed.');
				return;
 			}

			$enc_pass = md5 ($pass . $this -> hsalt);

			$log = "<?php\nif (!defined('INC_DIR')) exit();\n define('ADMIN_PASS', '".$enc_pass."');\n define('ADMIN_LOGIN', '".$login."');\n?>";

			if (fwrite($uchwyt, $log) === FALSE) {
				$this -> warning = $this -> translate('Internal error.').'<br>'.
				$this -> translate('Acces data has not changed.');
				return;
 			}
			
			$this -> success = $this -> translate('Access data changed.');
			fclose($uchwyt);

			return;
	}

	protected function login_check() {
		if (!isset($_SESSION)) {
			session_start();
		}
		$inactive = 7200;

		if(isset($_SESSION['timeout']) ) {
			$session_life = time() - $_SESSION['timeout'];
			if($session_life > $inactive) { 
				session_destroy();
				$this -> warning = $this -> translate('To long session time. Log in again.');
				return false; 
			}
		}
		$_SESSION['timeout'] = time();
		
		if (!isset($_SESSION['_csadmin'])) $_SESSION['_csadmin'] = array();
		
		if (isset($this -> cs_params['new_cs_username']) && isset($this -> cs_params['new_cs_password'])) {
			$username = $this -> cs_params['new_cs_username'];
			$password = md5($this -> cs_params['new_cs_password'] . $this -> hsalt);
		} else if (isset($_SESSION['_csadmin']['username']) && isset($_SESSION['_csadmin']['password'])) {
			$username = $_SESSION['_csadmin']['username'];
			$password = $_SESSION['_csadmin']['password'];
		} else {
			return false;
		}
		
		if ($username == ADMIN_LOGIN && $password == ADMIN_PASS) {
			$_SESSION['_csadmin']['username'] = $username;
			$_SESSION['_csadmin']['password'] = $password;
			
			return true;
		
		} else {
			$this -> warning = $this -> translate('Wrong login or password.');
			return false;
		}
	}

	public function action_create() {
		//TODO: walidacja
		$data = validate_bool($_POST);//type, parent, name

		$data['id'] = uniqid('feat_');

		$newobj = $this -> mapdata -> Features -> addChild('Feature');
		$newobj -> addAttribute('id', $data['id']);
		$newobj -> addAttribute('parent', $data['parent']);
		$newobj -> addAttribute('type', $data['type']);
		$newobj -> addCData(json_encode($data));

		$this -> save_mapdata();

		//create, update maja zwracac zapisany obiekt
		$this -> response = $data;
	}

	public function action_addiconset() {
		$this -> response = array();
		
		if (@$this -> cs_params['setname']) {
			$set = array(
				'id' =>  uniqid('set_'),
				'name' => $this -> cs_params['setname']
			);

			$newobj = $this -> mapdata -> Iconsets -> addChild('Iconset');
			$newobj -> addAttribute('id', $set['id']);
			$newobj -> addCData(json_encode($set));

			$this -> save_mapdata();

			$this -> response = $set;
		}
	}

	public function action_addicons() {
		$data = $_POST;
		$this -> response = array();
		
		if (@$data['url'] && @$data['setid']) {
			$ico = array(
				'id' => uniqid('ico_'),
				'url' => $data['url'],
				'set_id' => $data['setid']
			);

			$newobj = $this -> mapdata -> Icons -> addChild('Icon');
			$newobj -> addAttribute('id', $ico['id']);
			$newobj -> addAttribute('setid', $ico['set_id']);
			$newobj -> addCData(json_encode($ico));

			$this -> save_mapdata();

			$this -> response = $ico;
		}
	}

	public function action_removeicon() {
		$this -> response = array();
		if (($iconid = @$this -> cs_params['iconid']) && ($icon_xml = $this -> mapdata -> xpath("//Icon[@id='".$iconid."']"))) {
			$ret = json_decode((string)$icon_xml[0]);
			
			$dom=dom_import_simplexml($icon_xml[0]);
			$dom->parentNode->removeChild($dom);

			$this -> save_mapdata();

			$this -> response = $ret;
		}
	}

	public function action_removeiconset() {
		$this -> response = array();
		if (($setid = @$this -> cs_params['setid']) && ($icon_xml = $this -> mapdata -> xpath("//Iconset[@id='".$setid."']"))) {
			$ret = json_decode((string)$icon_xml[0]);
			
			$dom=dom_import_simplexml($icon_xml[0]);
			$dom->parentNode->removeChild($dom);

			$this -> save_mapdata();

			$this -> response = $ret;
		}
	}

	public function action_destroy() {
		//TODO: zastanowic sie nad usuwaniem ewentualnych dzieci, wtedy musialoby byc przeladowanie frontendu
		
		if (($id = @$this -> cs_params['id']) && ($feature_xml = $this -> mapdata -> xpath("//Feature[@id='".$id."']"))) {
			$ret = json_decode((string)$feature_xml[0]);
			
			$dom=dom_import_simplexml($feature_xml[0]);
			$dom->parentNode->removeChild($dom);

			$this -> save_mapdata();

			$this -> response = $ret;
		} else {
			//error
		}
	}

	public function action_update() {
		//TODO: walidacja
		$data = validate_bool($_POST);

		if (($id = @$this -> cs_params['id']) && ($feature_xml = $this -> mapdata -> xpath("//Feature[@id='".$id."']"))) {
			$feature_xml[0] -> removeCData();
			$feature_xml[0] -> addCData(json_encode($data));
			$feature_xml[0]['id'] = $data['id'];
			$feature_xml[0]['parent'] = $data['parent'];
			$feature_xml[0]['type'] = $data['type'];

			$this -> save_mapdata();
			$this -> response = json_decode((string)$feature_xml[0]);
		} else {
			//errror
		}
	}

	public function action_iconsets() {
		$this -> response = array();
		$iconset_xml = $this -> mapdata -> xpath("//Iconset");

		foreach ($iconset_xml as $iconset) {
			$set = json_decode((string)$iconset, true);
			$this -> response[] = $set;
		}
	}

	public function action_icons() {
		$this -> response = array();
		if (!($setid = @$this -> cs_params['setid'])) return;

		$icons_xml = $this -> mapdata -> xpath("//Icon");

		foreach ($icons_xml as $icon) {
			$i = json_decode((string)$icon, true);

			if ($i['set_id'] != $setid) continue;

			$this -> response[] = $i;
		}
	}

	public function action_mapsettings_update() {
		$data = validate_bool($_POST);

		if (isset($this -> mapdata -> Mapsettings)) {
			$this -> mapdata -> Mapsettings -> removeCData();
			$this -> mapdata -> Mapsettings -> addCData(json_encode($data));

			$this -> save_mapdata();

			$this -> response = json_decode((string)$this -> mapdata -> Mapsettings);
		} else {
			//error
		}
	}
}
?>