<?php


class Files_Controller extends T_Controller {

	public function __construct(){
		$this->frame = 'clear';

		parent::__construct();
	}

	/**
	 * ���������� �������������� ������
	 *
	 */

	public function get_image($uri) {

		$uri = explode("/", $uri);

		$strElementUri = explode(".", $uri[4]);
		$strElementUri = $strElementUri[0];

		if($uri[0] != 'catalog' && $uri[0] != 'catalog_groups'){
		}

		if($uri[0] == 'catalog_groups'){
		}else{
		}

		$objElement = new Model();
		$arrElement=$objElement->db
			  ->select('*')
			  ->from($strTableName)
			  ->where('uri', $strElementUri)
			  ->get()
			  ->row();

		if($uri[0] == 'catalog_groups'){
			$intElementID = $arrElement['group_id'];
		}else{
            $intElementID = $arrElement['id'];
		}

		$arrFiles = DBFile::select($uri[0], $intElementID);
		$arrImage = "";

		foreach($arrFiles[$uri[1]] as $key => $value){
                 break;
			}
		}

		if(empty($arrImage) && $arrFiles['_rows']['image']['id'] == $uri[2]){
		}

		if(empty($arrImage) && $arrFiles['_rows']['imageleft']['id'] == $uri[2]){
            $arrImage = $arrFiles['_rows']['imageleft'];
		}


		if(!empty($arrImage)){
					$strImgPath = $arrImage['preview_1'];
				break;

				case 2:
					$strImgPath = $arrImage['preview_2'];
				break;

				case 3:
					$strImgPath = $arrImage['preview_3'];
				break;

				default:
					$strImgPath = $arrImage['src'];
				break;
			}

			if(substr($strImgPath,-(strlen($arrImage['ext']))) == $arrImage['ext']){
				$strMime = str_ireplace('jpg', 'jpeg', $arrImage['ext']);

                $tmpUri = $uri;

				unset($tmpUri[count($tmpUri)-1]);
				$strImgDir = "files/".implode("/", $tmpUri)."/";
                mkdir(DOCROOT.$strImgDir, 0777, true);

                copy(DOCROOT.$strImgPath, DOCROOT."files/".implode("/", $uri));
                chmod(DOCROOT.implode("/", $uri),0777);

                readfile(DOCROOT.$strImgPath);
                exit(0);
			}
		}

        Event::run('system.404');
	}
}
?>