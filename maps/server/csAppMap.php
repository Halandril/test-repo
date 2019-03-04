<?php
require_once(INC_DIR.'csApp.php');

class csAppMap extends csApp {
	public $apikey;
	public $mapsettings;

	public function __construct() {
		parent::__construct();

		if (!isset($this -> trans_tbl['pl_PL'])) {
			$this -> trans_tbl['pl_PL'] = array();
		}
		$this -> trans_tbl['pl_PL']['Map menu'] = 'Menu mapy';

		if (isset($this -> mapdata -> Mapsettings)) {
			$this -> mapsettings = json_decode((string)$this -> mapdata -> Mapsettings, true);
		}

		if (isset($this -> mapsettings['apikey']) && $this -> mapsettings['apikey']) {
			$this -> apikey = $this -> mapsettings['apikey'];
		}
	}

	public function bootstrap_data() {
		$this -> before_action();

		$this -> response = array();

		$this -> cs_params['parent'] = 'root';
		$this -> action_get();

		$startdata = $this -> response;

		echo "window.bootstrap_data = {root: ".json_encode($startdata).", mapsettings: ".json_encode($this -> mapsettings)."};";
	}

	public function action_get() {
		$id = @$this -> cs_params['id'];
		$parent = @$this -> cs_params['parent'];

		if ($id) {
			//konkretny feature
			if ($feature_xml = $this -> mapdata -> xpath("//Feature[@id='".$id."']")) {
				$this -> response = json_decode((string)$feature_xml[0]);			
			} else {
				//TODO - blad
			}

		} else if ($parent) {
			//wszystkie feature danego rodzica
			//wraz z ewentualnymi folderami potomnymi jesli maja atrybut default_display
			
			$this -> response = array();
			$this -> get_folder($parent);

			/*if ($parent == 'root') {
				$query = "//Feature[@parent='".$parent."' or @type='Folder']";
			} else {
				$query = "//Feature[@parent='".$parent."']";
			}*/

			/////// posortowanie wynikow
			//1. najpierw alfabetycznie
			//usort($this -> response, 'cmp_name');
			//2. pozniej wedlug wagi
			//usort($this -> response, 'cmp_sort');
			//3. na koncu wg typu (czyli Foldery wyzej)
			//usort($this -> response, 'cmp_type');
			usort($this -> response, 'cmp_all');

			/*echo '<pre>';
			var_dump($this -> response);
			echo '</pre>';*/
		}
	}

	public function action_mapsettings() {
		if (isset($this -> mapdata -> Mapsettings)) {
			$this -> response = json_decode((string)$this -> mapdata -> Mapsettings);
		}
	}

	public function action_gettree() {
		$this -> response = array();

		$feature_xml = $this -> mapdata -> xpath("//Feature[@type='Folder']");

		foreach ($feature_xml as $fold) {
			$feat = json_decode((string)$fold, true);
			$this -> response[] = $feat;
		}

		//TODO: sortowanie
	}

	protected function get_folder($parent) {
		if (!$parent) return;
		$feature_xml = $this -> mapdata -> xpath("//Feature[@parent='".$parent."']");

		foreach ($feature_xml as $fold) {
			$feat = json_decode((string)$fold, true);
			$this -> response[] = $feat;

			///// recursion
			if (@$feat['type'] == 'Folder' && @$feat['default_display'] && @$feat['id']) {
				$this -> get_folder($feat['id']);
			}
		}
	}
}
?>