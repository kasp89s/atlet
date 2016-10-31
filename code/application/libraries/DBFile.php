<?
/**
 * Модуль по работе с файлами (загрузка, хранение, download)
 *
 * @author Sinner
 */
	class DBFile
         {

         	  protected static $config;
              private static $errors;



              /**
               * Загрузка файл на сервер
               *
               * @param string $fileKey название ключа массива _FILES
               * @param array $arrExtentions допустимые расширения файлов
               * @param int $intItemId идентификатор элемента-владельца файла
               * @param string $strItemTable таблица (без префикса), где хранятся элементы-владельцы файлов
               * @return int id идентификатор вставленного файла
               *
               * @example
               * <input name="first_file" type="file" value="">
               * DBFile::uploadByKey('first_file',FALSE,23,"items_table")
               *
               */
              public function uploadByKey($fileKey,$arrExtentions=Array(),$intItemId=0,$strItemTable="",$genUnicalInputName =false)
                  {
                       $strDirForWriting=DBFile::getDirForWriting();
                       if(isset($_FILES[$fileKey])&&file_exists($_FILES[$fileKey]['tmp_name']))
                           {
                                if(!DBFile::validateUploadFile($_FILES[$fileKey],$arrExtentions))
                                    return false;

                                $_FILES[$fileKey]['input_name']=$fileKey;
                                return((DBFile::saveFile($_FILES[$fileKey],$strDirForWriting,$intItemId,$strItemTable,$genUnicalInputName)));
                           }
                       else
                           {
                                return false;
                           }
                  }


              /**
               * Обложка для функции uploadByKey
               *
               */
              public function save($strItemTable="", $intItemId=0, $fileKey, $genUnicalInputName=false)
                  {
                       return DBFile::uploadByKey($fileKey, array(), $intItemId, $strItemTable,$genUnicalInputName);
                  }

              /**
               * Массовая загрузка файлов на сервер (может и один)
               *
               * @param array $fileKey массив ключей массива _FILES
               * @param array $arrExtentions допустимые расширения файлов
               * @param int $intItemId идентификатор элемента-владельца файла
               * @param string $strItemTable таблица (без префикса), где хранятся элементы-владельцы файлов
               * @return array int id массив идентификаторов вставленных файлов
               */
              public function uploadMass($fileKey=Array(),$arrExtentions=Array(),$intItemId=0,$strItemTable="",$genUnicalInputName=false)
                  {

                       $strDirForWriting=DBFile::getDirForWriting();

                       $arrUploadedIds=Array();
                       if(count($fileKey)>0)
                           {
                                foreach($fileKey as $key => $value)
                                    {
                                         if(!DBFile::validateUploadFile($_FILES[$value],$arrExtentions))
                                             continue;

                                         if(isset($_FILES[$value])&&file_exists($_FILES[$value]['tmp_name']))
                                             {
                                             	  $_FILES[$value]['input_name']=$value;
                                                  $intNewId=(DBFile::saveFile($_FILES[$value],$strDirForWriting,$intItemId,$strItemTable,$genUnicalInputName));
                                                  if(intval($intNewId)>0){
                                                      $arrUploadedIds[]=$intNewId;
                                                      $arrUploadedIds[$value]=$intNewId;
                                                  }
                                             }
                                    }
                           }
                       else
                           {
                                foreach($_FILES as $key => $value)
                                    {
                                         if(!DBFile::validateUploadFile($_FILES[$key],$arrExtentions))
                                             continue;

                                         if(file_exists($value['tmp_name']))
                                             {
                                             	  $value['input_name']=$key;
                                                  $intNewId=((DBFile::saveFile($value,$strDirForWriting,$intItemId,$strItemTable,$genUnicalInputName)));
                                                  $arrUploadedIds[$key]=$intNewId;
                                             }
                                    }
                           }

                       return((count($arrUploadedIds)>0?$arrUploadedIds:false));
                  }

              /**
               * Проверка расширений файлов по заданному списку допустимых расширений
               *
               * @param array $arrFilesArray _FILES[key]
               * @param array $arrExtentions допустимые расширения
               * @return boolean
               */
              public function validateUploadFile($arrFilesArray,$arrExtentions=Array())
                  {
                       if(!is_file($arrFilesArray['tmp_name']))
                           return false;

                       if($arrFilesArray['error']!=0)
                           return false;

                       if(count($arrExtentions)>0)
                           {
                                $extentionMissing=true;
                                foreach($arrExtentions as $key => $value)
                                    {
                                         if(preg_match("#\.".$value."$#i",$arrFilesArray['name']))$extentionMissing=false;
                                    }

                                if($extentionMissing)return false;
                           }

                       return true;
                  }

              /**
               * Сохранение файла
               *
               * @param array $arrSource _FILES[key]
               * @param string $strDest папка - пункт назначения
               * @param int $intItemId идентификатор элемента-владельца файла
               * @param string $strItemTable таблица (без префикса), где хранятся элементы-владельцы файлов
               * @return unknown
               */
              public function saveFile($arrSource,$strDest,$intItemId=0,$strItemTable="",$genUnicalInputName=false)
                  {
                       $altFileName=$arrSource['name'];
                       $strNewFileName=(md5(microtime(true)).".".DBFile::getFileExtention($altFileName));

                       $arrSource['input_name']=($genUnicalInputName?($arrSource['input_name'].md5(microtime(true).rand(1,99999))):$arrSource['input_name']);

                       if($intItemId>0&&strlen($strItemTable)>0&&!DBFile::isItemExist($intItemId,$strItemTable))
                           return false;

                       $mime = Kohana::config('mimes.'.DBFile::getFileExtention($altFileName));
                       $strFileType=@explode("/",$mime[0]);
                       $strFileType=$strFileType[0];

                       $delete_id = false;

                       //ищем аналогичную запись для удаления (ЗАМЕНА)
                       if(!$genUnicalInputName){
                        	$row = DBFile::getFiles($intItemId, $strItemTable, $arrSource['input_name']);
                        	$delete_id = (!empty($row['_rows'][$arrSource['input_name']]['id'])) ? $row['_rows'][$arrSource['input_name']]['id'] : 0;
                       }

                       if($strFileType!="video"&&!($strFileType=="image"&&in_array(DBFile::getFileExtention($altFileName),Array('jpg','jpeg','gif','png'))))
                           {
		                       if(!copy($arrSource['tmp_name'],$strDest."/".$strNewFileName))
		                           return false;

		                       $strRelativePath=DBFile::getRelativePath($strDest."/".$strNewFileName);
		                       $strRelativePath=preg_replace("#([/])+#i","/",$strRelativePath);

		                       $res = db::insert('files', array(
		                           'src'         => preg_replace("#([/]+)#i","/",$strRelativePath),
		                           'type'        => $strFileType,
		                           'item_id'     => $intItemId,
		                           'item_table'  => $strItemTable,
		                           'name'        => $arrSource['name'],
		                           'input_name'  => $arrSource['input_name']
		                       ));

		                       $intFileId=$res->insert_id();
		                       if(intval($intFileId)<=0)
		                           {
		                               unlink($strDest."/".$strNewFileName);
		                               return false;
		                           }

		                       $intFilesAdded=1;
		                       $intTotalAddedSize=filesize($strDest."/".$strNewFileName);

                               $arrDirIniFile=DBFile::getDirIniFile($strDest);
		                       $arrDirIniFile['size']+=$intTotalAddedSize;
		                       $arrDirIniFile['files']+=$intFilesAdded;
		                       if(is_file($strDest."/dir_info.php"))
		                           {
		                                $resDirIni=fopen($strDest."/dir_info.php","w");
		                                fputs($resDirIni,serialize($arrDirIniFile));
		                                fclose($resDirIni);

		                           }
                           }
                       elseif($strFileType=="video")
                           {
                               $intFileId=DBFile::saveVideo($arrSource,$strDest,$intItemId,$strItemTable);
                           }
                       elseif($strFileType=="image"&&in_array(DBFile::getFileExtention($altFileName),Array('jpg','jpeg','gif','png')))
                           {
                               $intFileId=DBFile::saveImage($arrSource,$strDest,$intItemId,$strItemTable);
                           }

                       if(intval($intFileId)>0 && $delete_id)
                           {
                           		DBFile::deleteFileByIds($delete_id);
                           }
                  }


              public function getFiles($intItemId=false,$strItemTable=false,$arrInputName=array(),$type_return=false)
                  {
                  	   if(!$intItemId || !$strItemTable)
                  			return false;

                  	   if(!is_array($arrInputName))
                           {
                                $arrInputName=Array($arrInputName);
                           }

                  	   if(!is_array($intItemId))
                           {
                                $intItemId=Array($intItemId);
                           }

                       $table = db::build();
              	       $table
              	       	->select('*')
              	       	->from('files')
              	       	->where('item_table', $strItemTable)
              	       	->where('item_id', 'in', $intItemId);


              	       if(count($arrInputName)) {
	              	       $table->open();
	              	       $isFirst = true;
	              	       foreach ($arrInputName as $k=>$v){	              	       		if($isFirst){	              	       			$isFirst = false;
	              	       			$table->where('input_name', 'like', $v);	              	       		}else{                                    $table->or_where('input_name', 'like', $v);	              	       		}

	              	       }
	              	       $table->close();
              	       }


              	       $rows = $table->get()->rows();
              	       $result = array();
              	       if($type_return){
	              	       foreach ($rows as $v){
	              	       		$arrRes = explode('_', $v['input_name']);
	              	       		$group = (isset($arrRes[0])?$arrRes[0]:"");
	              	       		$tail = (isset($arrRes[1])?$arrRes[1]:"");

	              	       		$strExt = explode(".", $v['src']);
	              	       		$strExt = $strExt[count($strExt)-1];
	              	       		$v['ext'] = $strExt;

	              	       		if($tail)
	              	       			$result[$v['item_id']][$group][] = $v;
	              	       		else
	              	       			$result[$v['item_id']]['_rows'][$group] = $v;
	              	       }
              	       } else {
	              	       foreach ($rows as $v){
	              	       		$arrRes = explode('_', $v['input_name']);
	              	       		$group = (isset($arrRes[0])?$arrRes[0]:"");
	              	       		$tail = (isset($arrRes[1])?$arrRes[1]:"");

	              	       		$strExt = explode(".", $v['src']);
	              	       		$strExt = $strExt[count($strExt)-1];
	              	       		$v['ext'] = $strExt;

	              	       		if($tail)
	              	       			$result[$group][] = $v;
	              	       		else
	              	       			$result['_rows'][$group] = $v;
	              	       }
              	       }

              	       return $result;
                  }


              /**
               * Обложка для функции saveFile
               *
               */
              public function save_base($strItemTable="", $intItemId=0, $arrSource, $strDest)
                  {
              	    return DBFile::saveFile($arrSource, $strDest, $intItemId, $strItemTable);
              	  }


              public function saveVideo($arrSource,$strDest,$intItemId=0,$strItemTable="")
                  {
                  	    set_time_limit(0);

                        $altFileName=$arrSource['name'];
                        $strFileExtention=DBFile::getFileExtention($altFileName);


                        $strTmpFileName=md5(microtime(true));
                        $strNewFileName=($strTmpFileName.".".$strFileExtention);
                        $strNewFilePreview1Name=($strTmpFileName."1.jpg");
                        $strNewFilePreview2Name=($strTmpFileName."2.jpg");
                        $strNewFilePreview3Name=($strTmpFileName."3.jpg");

						$videoFile = $strDest . "/tmp1" . $strNewFileName;
						$flvTempFile = $strDest . "/tmp2" . $strTmpFileName . ".flv";
						$flvFile = $strDest . "/" . $strTmpFileName . ".flv";
						$jpgFile = $strDest . "/" . $strNewFilePreview1Name;


                        if($intItemId>0&&strlen($strItemTable)>0&&!DBFile::isItemExist($intItemId,$strItemTable))
                           return false;

                        $mime = Kohana::config('mimes.'.DBFile::getFileExtention($altFileName));
                        $strFileType=@explode("/",$mime[0]);
                        $strFileType=$strFileType[0];

                        if(!copy($arrSource['tmp_name'],$videoFile))
                          return false;
                        $f = fopen($file, 'r');
					    $buf = fgets($f, 1024);
					    fclose($f);

					    $info = DBFile::get_video_info($videoFile);

					    if ( !preg_match('/^(FLV)/', $buf) ){

							if ( ($info['width'] > 240) || ($info['height'] > 180) )
							{
								if ( $info['width'] >= $info['height'] )
								{
									$n_width = 240;
									$n_height = round((240/$info['width'])*$info['height'],0);
								}
								else
								{
									$n_height = 180;
									$n_width = round((180/$n_height)*$info['width'],0);
								}

								$n_width=$n_width+($n_width%2);
								$n_height=$n_height+($n_height%2);

								$_s = ' -s '.$n_width.'x'.$n_height;
							}

							$cmd = Kohana::config('dbfile.path_ffmpeg')." -i $videoFile -y ".$_s." -b ".$info['bitrate']."k -r 25 -ar ".$info['sampling']." $flvTempFile";
							system($cmd);
							unlink($videoFile);

                        }else{
							if ( ($info['width'] > 240) || ($info['height'] > 180) )
							{
								if ( $info['width'] >= $info['height'] )
								{
									$n_width = 240;
									$n_height = round((240/$info['width'])*$info['height'],0);
								}
								else
								{
									$n_height = 180;
									$n_width = round((180/$n_height)*$info['width'],0);
								}

							}

							$n_width=$n_width+($n_width%2);
							$n_height=$n_height+($n_height%2);
                        	rename($videoFile, $flvTempFile);
                        }

                        if(!is_file($flvTempFile)){                        	return false;
                        }

						$cmd =  Kohana::config('dbfile.path_yamdi')." -i $flvTempFile -o $flvFile";
						system($cmd);
						unlink($flvTempFile);

						if(!is_file($flvFile)){
                        	return false;
                        }

						$cmd = Kohana::config('dbfile.path_ffmpeg')." -i $flvFile -an -ss ".$info['length']." -r 1 -vframes 1 -y -f mjpeg $jpgFile";
						system($cmd);

						if(!is_file($jpgFile)){
                        	return false;
                        }

					    $strRelativePreview1Path = DBFile::getRelativePath($strDest."/".$strNewFilePreview1Name);

                        $arrSizes=preg_split("#([^0-9])+#",Kohana::config('dbfile.videoPreview2Size'),2);
                        $arrSizes[0]=intval($arrSizes[0]);
                        $arrSizes[1]=intval($arrSizes[1]);

                        $objImage=new Image($strDest."/".$strNewFilePreview1Name);
                        $objImage->resize($arrSizes[0],$arrSizes[1],Image::AUTO_NOSTRETCH);
                        $objImage->save($strDest."/".$strNewFilePreview2Name,0777);
                        $strRelativePreview2Path  = DBFile::getRelativePath($strDest."/".$strNewFilePreview2Name);

                        $arrSizes=preg_split("#([^0-9])+#",Kohana::config('dbfile.videoPreview3Size'),2);
                        $arrSizes[0]=intval($arrSizes[0]);
                        $arrSizes[1]=intval($arrSizes[1]);

                        $objImage=new Image($strDest."/".$strNewFilePreview1Name);
                        $objImage->resize($arrSizes[0],$arrSizes[1],Image::AUTO_NOSTRETCH);
                        $objImage->save($strDest."/".$strNewFilePreview3Name,0777);
                        $strRelativePreview3Path  = DBFile::getRelativePath($strDest."/".$strNewFilePreview3Name);

                        $strRelativePath=DBFile::getRelativePath($flvFile);
                        $strRelativePath=preg_replace("#([/])+#i","/",$strRelativePath);

                        $res = db::insert('files', array(
                           'src'          => preg_replace("#([/]+)#i","/",$strRelativePath),
                           'type'         => $strFileType,
                           'item_id'      => $intItemId,
                           'item_table'   => $strItemTable,
                           'name'         => $arrSource['name'],
                           'preview_1'    => preg_replace("#([/]+)#i","/",$strRelativePreview1Path),
                           'preview_2'    => preg_replace("#([/]+)#i","/",$strRelativePreview2Path),
                           'preview_3'    => preg_replace("#([/]+)#i","/",$strRelativePreview3Path),
		                   'input_name'   => $arrSource['input_name']
                        ));

                        $intFileId=$res->insert_id();
                        if(intval($intFileId)<=0)
                            {
                                unlink($strDest."/".$strNewFileName);
                                return false;
                            }

                        $intFilesAdded=4;
                        $intTotalAddedSize=filesize($strDest."/".$strNewFileName);
                        $intTotalAddedSize+=filesize($strDest."/".$strNewFilePreview1Name);
                        $intTotalAddedSize+=filesize($strDest."/".$strNewFilePreview2Name);
                        $intTotalAddedSize+=filesize($strDest."/".$strNewFilePreview3Name);

                        $arrDirIniFile=DBFile::getDirIniFile($strDest);
                        $arrDirIniFile['size']+=$intTotalAddedSize;
                        $arrDirIniFile['files']+=$intFilesAdded;
                        if(is_file($strDest."/dir_info.php"))
                            {
                                 $resDirIni=fopen($strDest."/dir_info.php","w");
                                 fputs($resDirIni,serialize($arrDirIniFile));
                                 fclose($resDirIni);

                            }

                        return($intFileId);
                  }

              public function saveImage($arrSource,$strDest,$intItemId=0,$strItemTable="",$genUnicalInputName=false)
                  {
                       $altFileName=$arrSource['name'];
                       $strFileExtention=DBFile::getFileExtention($altFileName);
                       $strTmpFileName=md5(microtime(true));

                       $strNewFileName=($strTmpFileName.".".$strFileExtention);
                       $strNewFilePreview1Name=($strTmpFileName."1.".$strFileExtention);
                       $strNewFilePreview2Name=($strTmpFileName."2.".$strFileExtention);
                       $strNewFilePreview3Name=($strTmpFileName."3.".$strFileExtention);

                       if($intItemId>0&&strlen($strItemTable)>0&&!DBFile::isItemExist($intItemId,$strItemTable))
                           return false;

                       $mime = Kohana::config('mimes.'.DBFile::getFileExtention($altFileName));
                       $strFileType=@explode("/",$mime[0]);
                       $strFileType=$strFileType[0];

                       if(!copy($arrSource['tmp_name'],$strDest."/".$strNewFileName))
                          return false;

                       $arrSizes=preg_split("#([^0-9])+#",Kohana::config('dbfile.imagesPreview1Size'),2);
                       $arrSizes[0]=intval($arrSizes[0]);
                       $arrSizes[1]=intval($arrSizes[1]);

                       $objImage=new Image($strDest."/".$strNewFileName);
                       $objImage->resize($arrSizes[0],$arrSizes[1],Image::AUTO_NOSTRETCH);
                       $objImage->save($strDest."/".$strNewFilePreview1Name,0777);
                       $strRelativePreview1Path  = DBFile::getRelativePath($strDest."/".$strNewFilePreview1Name);

                       $arrSizes=preg_split("#([^0-9])+#",Kohana::config('dbfile.imagesPreview2Size'),2);
                       $arrSizes[0]=intval($arrSizes[0]);
                       $arrSizes[1]=intval($arrSizes[1]);

                       $objImage=new Image($strDest."/".$strNewFileName);
                       $objImage->resize($arrSizes[0],$arrSizes[1],Image::AUTO_NOSTRETCH);
                       $objImage->save($strDest."/".$strNewFilePreview2Name,0777);
                       $strRelativePreview2Path  = DBFile::getRelativePath($strDest."/".$strNewFilePreview2Name);

                       $arrSizes=preg_split("#([^0-9])+#",Kohana::config('dbfile.imagesPreview3Size'),2);
                       $arrSizes[0]=intval($arrSizes[0]);
                       $arrSizes[1]=intval($arrSizes[1]);

                       $objImage=new Image($strDest."/".$strNewFileName);
                       $objImage->resize($arrSizes[0],$arrSizes[1],Image::AUTO_NOSTRETCH);
                       $objImage->save($strDest."/".$strNewFilePreview3Name,0777);
                       $strRelativePreview3Path  = DBFile::getRelativePath($strDest."/".$strNewFilePreview3Name);

                       $strRelativePath=DBFile::getRelativePath($strDest."/".$strNewFileName);
                       $strRelativePath=preg_replace("#([/])+#i","/",$strRelativePath);

                       $arrSizes=getimagesize(DOCROOT.$strRelativePath);

                       $intOrigWidth=$arrSizes[0];
                       $intOrigHeight=$arrSizes[1];

                       if(abs($intOrigWidth-$intOrigHeight)<=(max(Array($intOrigWidth,$intOrigHeight))/100*2) && $intOrigWidth*$intOrigHeight>0){
                       		$strOrientation="s";
                       }elseif($intOrigWidth<$intOrigHeight && $intOrigWidth*$intOrigHeight>0){                       	    $strOrientation="v";
                       }elseif($intOrigWidth>$intOrigHeight && $intOrigWidth*$intOrigHeight>0){                       	    $strOrientation="h";                       }else{                       		$strOrientation="notset";                       }

                       $res = db::insert('files', array(
                           'src'          => preg_replace("#([/]+)#i","/",$strRelativePath),
                           'type'         => $strFileType,
                           'item_id'      => $intItemId,
                           'item_table'   => $strItemTable,
                           'name'         => $arrSource['name'],
                           'preview_1'    => preg_replace("#([/]+)#i","/",$strRelativePreview1Path),
                           'preview_2'    => preg_replace("#([/]+)#i","/",$strRelativePreview2Path),
                           'preview_3'    => preg_replace("#([/]+)#i","/",$strRelativePreview3Path),
		                   'input_name'   => $arrSource['input_name'],
		                   'image_orientation'=>$strOrientation,
		                   'image_width'  => $intOrigWidth,
		                   'image_height' => $intOrigHeight,
		                   'file_size'    => filesize(DOCROOT.$strRelativePath)
                       ));

                       $intFileId=$res->insert_id();
                       if(intval($intFileId)<=0)
                           {
                               unlink($strDest."/".$strNewFileName);
                               return false;
                           }

                       $intFilesAdded=4;
                       $intTotalAddedSize=filesize($strDest."/".$strNewFileName);
                       $intTotalAddedSize+=filesize($strDest."/".$strNewFilePreview1Name);
                       $intTotalAddedSize+=filesize($strDest."/".$strNewFilePreview2Name);
                       $intTotalAddedSize+=filesize($strDest."/".$strNewFilePreview3Name);

                       $arrDirIniFile=DBFile::getDirIniFile($strDest);
                       $arrDirIniFile['size']+=$intTotalAddedSize;
                       $arrDirIniFile['files']+=$intFilesAdded;
                       if(is_file($strDest."/dir_info.php"))
                           {
                                $resDirIni=fopen($strDest."/dir_info.php","w");
                                fputs($resDirIni,serialize($arrDirIniFile));
                                fclose($resDirIni);

                           }


                       return($intFileId);
                  }

              /**
               * Удаление файлов(!) по идентификаторам
               *
               * @param array $arrFilesIds массив идентификаторов файлов
               * @param array $arrItemsIds массив записей-владельцев файлов
               * @param string $strItemsTable таблица-владелец
               * @return array int идентификаторы удаленных файлов
               */

              public function deleteFileByIds($arrFilesIds,$arrItemsIds=Array(),$strItemsTable="")
                  {
                       if(!is_array($arrFilesIds))
                           {
                                $arrFilesIds=Array($arrFilesIds);
                           }

                       if(!is_array($arrItemsIds))
                           {
                                $arrItemsIds=Array($arrItemsIds);
                           }

                       $strFilesIds=implode(",",$arrFilesIds);
                       $strItemsIds=implode(",",$arrItemsIds);

                       if(strlen($strFilesIds)<=0&&strlen($strItemsIds)<=0)
                           return false;

                       $sqlWhereClause="";

                       $table = db::build();
                       $table
                       		->select('*')
                       		->from('files');
                       if(strlen($strFilesIds)>0)
                           {
                                $table->where('id', 'in', $arrFilesIds);
                           }

                       if(strlen($strItemsIds)>0 && strlen($strItemsTable)>0)
                           {
                                $table->where('item_table', $strItemsTable);
                                $table->where('item_id', 'in', $arrItemsIds);
                           }

					   $resFilesInfo = $table->get()->rows();



                       if(count($resFilesInfo)<=0)return false;

                       $strFilesIds="";
                       $arrFilesIds=Array();

                       $intFilesDroped=0;
                       $intTotalDroppedSize=0;

                       foreach ($resFilesInfo as $arrFileInfo)
                           {
                                if(strlen($strFilesIds)>0)$strFilesIds .=",";
                                $strFilesIds .=$arrFileInfo['id'];
                                $arrFilesIds[]=$arrFileInfo['id'];

	                             $intTotalDroppedSize+=@filesize(DOCROOT.$arrFileInfo['src']);
			                     if(@unlink(DOCROOT.$arrFileInfo['src']))
			                         $intFilesDroped++;

			                     $intTotalDroppedSize+=@filesize(DOCROOT.$arrFileInfo['preview_1']);
			                     if(@unlink(DOCROOT.$arrFileInfo['preview_1']))
			                         $intFilesDroped++;

			                     $intTotalDroppedSize+=@filesize(DOCROOT.$arrFileInfo['preview_2']);
			                     if(@unlink(DOCROOT.$arrFileInfo['preview_2']))
			                         $intFilesDroped++;

			                     $intTotalDroppedSize+=@filesize(DOCROOT.$arrFileInfo['preview_3']);
			                     if(@unlink(DOCROOT.$arrFileInfo['preview_3']))
			                         $intFilesDroped++;


		                        $strFileDirectory=str_replace("\\","/",$arrFileInfo['src']);
		                        preg_match("#(.*)[/]([^/]+)$#i",$strFileDirectory,$strFileDirectory);
		                        $strFileDirectory=$strFileDirectory[1];

		                        $arrFilesInDir=@scandir(DOCROOT.$strFileDirectory);

		                        if(count($arrFilesInDir)<=3)
		                            {
		                                 @unlink(DOCROOT.$strFileDirectory."/dir_info.php");
		                                 @rmdir(DOCROOT.$strFileDirectory."/");
		                            }

		                        $arrDirIniFile=DBFile::getDirIniFile(DOCROOT.$strFileDirectory);
		                        $arrDirIniFile['size']-=$intTotalDroppedSize;
		                        $arrDirIniFile['files']-=$intFilesDroped;
		                        if(is_file(DOCROOT.$strFileDirectory."/dir_info.php"))
		                            {
		                                 $resDirIni=fopen(DOCROOT.$strFileDirectory."/dir_info.php","w");
		                                 fputs($resDirIni,serialize($arrDirIniFile));
		                                 fclose($resDirIni);

		                            }
                           }

                       $table
                       	 ->where('id', 'in', $arrFilesIds)
                       	 ->delete('files')->get()->rows();


                       return($arrFilesIds);
                  }


              /**
               * Обложка для функции deleteFileByIds
               *
               */
              public function delete_files($strItemsTable="", $arrFilesIds)
                  {
              	    return DBFile::deleteFileByIds($arrFilesIds, Array(), $strItemsTable);
              	  }

              /**
               * Обложка для функции deleteFileByIds
               *
               */
              public function delete_items($strItemsTable="", $arrItemsIds)
                  {
              	    return DBFile::deleteFileByIds(Array(), $arrItemsIds, $strItemsTable);
              	  }

              /**
               * Удаление всех файлов, принадлежащих заданным владельцам $arrItemsIds
               *
               * @param array $arrItemsIds массив владельцев (идентификаторы)
               * @param string $strItemsTable таблица владельца
               * @return array int массив удаленных файлов
               */
              public function deleteByItemsIds($arrItemsIds,$strItemsTable)
                  {
                       return DBFile::deleteFileByIds(Array(),$arrItemsIds,$strItemsTable);
                  }


              /**
               * Жесткая переиндексация всех папок
               *
               */
              public function updateDirsIni()
                  {
                       $strDirForFiles=DOCROOT . Kohana::config('dbfile.files_dir');
                       DBFile::scanDirInfo($strDirForFiles,true);
                  }

              /**
               * Возвращает относительный путь
               *
               * @param string $strAbsolutePath абсолютный путь
               * @return string
               */
              public function getRelativePath($strAbsolutePath)
                  {

                       $strAbsolutePath=str_replace("\\","/",$strAbsolutePath);
                       $doc_root = str_replace("\\","/",DOCROOT);
                       $strRelativePath=str_replace($doc_root,"",$strAbsolutePath);

                       if(substr($strRelativePath,0,1)!="/")
                           $strRelativePath="/".$strRelativePath;

                       return($strRelativePath);
                  }


              /**
               * Возвращает расширение файла, если разрешено (с заменой)
               *
               * @param unknown_type $strFileName
               * @return unknown
               */
              public function getFileExtention($strFileName)
                  {
                       preg_match("/\.([a-zA-Z0-9]+)$/i",$strFileName,$arrRegs);

                       $strExtention=$arrRegs[1];

                       $arrDeniedExt = Kohana::config('dbfile.denied_extentions');
                       foreach($arrDeniedExt as $key => $value)
                           {
                                 $strExtention=str_replace($key,$value,$strExtention);
                           }

                       $strExtention=strtolower($strExtention);

                       return($strExtention);
                  }

              /**
               * В какую папку писать.
               *
               * @return string текущая папка для записи
               */
              public function getDirForWriting()
                  {
                       $strDirForFiles=DOCROOT. Kohana::config('dbfile.files_dir');
                       $arrRootDirIni=DBFile::getDirIniFile($strDirForFiles);

                       $strLastDirAbsPath=$strDirForFiles."/".$arrRootDirIni['last_dir_name'];
                       $arrDirIni=DBFile::getDirIniFile($strLastDirAbsPath);

                       if(( (Kohana::config('dbfile.max_dir_size')) && ($arrDirIni['size']/1024/1024)>(Kohana::config('dbfile.max_dir_size')))||
                          ($arrDirIni['files']>(Kohana::config('dbfile.max_files_in_dir')))||
                          !file_exists($strDirForFiles."/".$arrRootDirIni['last_dir_name'])||
                          strlen(trim($arrRootDirIni['last_dir_name']))==0)
                           {
                                 $intDirNameLen=Kohana::config('dbfile.dir_name_len');
                                 $intDirName=intval($arrRootDirIni['last_dir_name']);
                                 $intDirName+=1;
                                 $strDirName=str_pad($intDirName,$intDirNameLen,"0",STR_PAD_LEFT);

                                 if(!mkdir($strDirForFiles."/".$strDirName,0777))return false;
                                 chmod($strDirForFiles."/".$strDirName,0777);
                                 DBFile::getDirIniFile($strDirForFiles."/".$strDirName);

                                 $resDirIniFile=fopen($strDirForFiles."/dir_info.php","r");
                                 $arrDirIniFile=fgets($resDirIniFile);
                                 $arrDirIniFile=unserialize($arrDirIniFile);
                                 fclose($resDirIniFile);

                                 $arrDirIniFile['last_dir_name']=$strDirName;

                                 $resDirIniFile=fopen($strDirForFiles."/dir_info.php","w");
                                 fputs($resDirIniFile,serialize($arrDirIniFile));
                                 fclose($resDirIniFile);
                                 chmod($strDirForFiles."/dir_info.php",0777);

                                 return($strDirForFiles."/".$strDirName);
                           }
                       else
                           {
                                 $strLastDirAbsPath=$strDirForFiles."/".$arrDirIni['last_dir_name'];
                                 $arrDirIni=DBFile::getDirIniFile($strLastDirAbsPath);
                                 @mkdir($strDirForFiles."/".$arrDirIni['last_dir_name'],0777,true);
                                 return($strDirForFiles."/".$arrDirIni['last_dir_name']);
                           }
                  }

              /**
               * Получения ini-файла для папки $dirPath
               *
               * @param string $dirPath
               * @param boolean $reload
               * @return string
               */
              public function getDirIniFile($dirPath,$reload=false)
                  {
                       $arrDirInfo=Array('size'=>-1,'last_dir_name'=>'','files'=>-1);

                       if(file_exists($dirPath."/dir_info.php")&&!$reload)
                           {
                                 $resDirIniFile=fopen($dirPath."/dir_info.php","r");
                                 $arrDirIniFile=fgets($resDirIniFile);
                                 $arrDirIniFile=unserialize($arrDirIniFile);
                                 fclose($resDirIniFile);
                           }
                       else
                           {
                                 $arrDirIniFile=DBFile::scanDirInfo($dirPath,$reload);
                                 $resDirIniFile=fopen($dirPath."/dir_info.php","w");
                                 fputs($resDirIniFile,serialize($arrDirIniFile));
                                 fclose($resDirIniFile);
                                 chmod($dirPath."/dir_info.php",0777);
                           }

                       return($arrDirIniFile);
                  }

              /**
               * Проверка существования владельца
               *
               * @param int $itemId запись-владелец
               * @param string $itemTable таблица владелец
               * @return boolean
               */
              public function isItemExist($itemId,$itemTable)
                  {
                       $res = db::build()
                        ->from($itemTable)
                       	->where('id', $itemId)
                       	->count_records();

                       if($res>0)
                           return(true);
                       else
                           return(false);
                  }

              /**
               * Переиндексация
               *
               * @param string $dirPath
               * @param boolean $reload
               * @return unknown
               */
              private function scanDirInfo($dirPath,$reload=false)
                  {
                       $arrDirInfo=Array('size'=>-1,'last_dir_name'=>'','files'=>-1);

                       $intTotalSize=0;
                       $intTotalFiles=0;
                       $strLastDirName="";

                       $dirPath=$dirPath."/";
                       $arrDirs=@scandir($dirPath);

                       $intDirFiles=count($arrDirs);
                       for($i=2;$i<$intDirFiles;$i++)
                           {
                               $current=$dirPath.$arrDirs[$i];
                               if(is_file($current))
                                   {
                                        $intTotalSize+=(filesize($current)/1024/1024);
                                        $intTotalFiles++;
                                   }
                               else
                                   {
                                        $arrTmpDirInfo=DBFile::getDirIniFile($current,$reload);
                                        $intTotalSize+=$arrTmpDirInfo['size'];
                                        $intTotalFiles+=$arrTmpDirInfo['files'];
                                        $strLastDirName=$arrDirs[$i];
                                   }
                           }

                       $arrDirInfo=Array('size'=>$intTotalSize,'last_dir_name'=>$strLastDirName,'files'=>$intTotalFiles);

                       return($arrDirInfo);
                  }



              public function select($itemTable, $itemId){
              		if(empty($itemTable) || empty($itemId))
              			return FALSE;

              		return DBFile::getFiles($itemId,$itemTable);
              }

              public function select_all($itemTable, $itemId){
              		if(empty($itemTable) || empty($itemId))
              			return FALSE;

              		return DBFile::getFiles($itemId,$itemTable, array(), true);
              }

		        /**
		         * Получение мета-данных видеофайла
		         *
		         * @param unknown_type $file
		         * @return unknown
		         */
				function get_video_info($file)
				{
					$return = array();
					$bitrate = '/bitrate: ([0-9]+) kb\/s/i';
					$sampling = '/, ([0-9]+) Hz/i';
					$pregLength='/Duration: ([^,]+),/i';
					$pregSizes='/([0-9]{2,}x[0-9]{2,})/';

					$return['bitrate'] = 128; //786
					$return['sampling'] = 22050;
					$return['length']=1;
					$return['width']=1;
					$return['height']=1;

					$descriptorspec = array(
						0 => array("pipe", "r"),	// stdin is a pipe that the child will read from
						1 => array("pipe", "w"),	// stdout is a pipe that the child will write to
						2 => array("pipe", "w")	// stderr is a file to write to
					);


					$cmd = Kohana::config('dbfile.path_ffmpeg')." -i $file";

					$process = proc_open($cmd, $descriptorspec, $pipes);

					while ( !feof($pipes[2]) )
					{
						$buf = fgets($pipes[2]);

						if ( preg_match($bitrate, $buf, $result) )
						{
							$return['bitrate'] = $result[1];
						}

						if ( preg_match($sampling, $buf, $result) )
						{
							$return['sampling'] = $result[1];
						}

						if(preg_match($pregLength, $buf, $matches)){
							$length = $matches[1];
							list($h, $m, $s) = explode(':', $length);

							$length = $h*3600 + $m*60 + $s;

							$return['length'] = ceil($length / 3);
						}

						if(preg_match($pregSizes, $buf, $size)){							list($return['width'], $return['height']) = explode('x', $size[0]);
						}
					}
					proc_close($process);

					return $return;
				}
         }




?>