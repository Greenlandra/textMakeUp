<?php

$declareChar = '：';
$semicolonChar = '；';
$commaChar = '，';

$splitChar = "\t";

$sceneData = require 'sceneData.php';
$sceneIdData = require 'sceneIdData.php';
$characterData = require 'characterData.php';
$roleData = require 'roleData.php';
$dressData = require 'dressData.php';
$avatarData = require 'avatarData.php';
$expressData = require 'expressData.php';
$moveData = require 'moveData.php';
$sideData = require 'sideData.php';
$eventFirstLastData = require 'eventFirstLastData.php';
$textLimitData = require 'textLimitData.php';
$rolePicData = require 'rolePicData.php';
$faceCityEventData = require 'faceCityEventData.php';
$colorData = require 'colorData.php';

$textMapDataFile = fopen('textMapData.txt', 'w');
$dialogDataFile = fopen('dialogData.txt', 'w');
$plotDataFile = fopen('plotData.txt', 'w');
$happeningDataFile = fopen('happeningData.txt', 'w');

$firstEvent = $eventFirstLastData['起始'];
$endEvent = $eventFirstLastData['结束'];

$sheetCityEventPhoto = array();

for ($event = $firstEvent; $event <= $endEvent; $event++){
	$fileNum = $event;
	$fileName = $fileNum . '.txt';
	if(file_exists($fileName)){
		$file_arr = file($fileName);
		$sheetCityEventPhoto[$event] = array(); //记录相册信息
		$sheetCityEventPhoto[$event]['eventID'] = $event;
		$sheetCityEventPhoto[$event]['cycleID'] = '101';
		$sheetCityEventPhoto[$event]['plotName'] = 'CityEventName_'.$event;
		$sheetCityEventPhoto[$event]['plotNameText'] = '';
		$sheetCityEventPhoto[$event]['type'] = ''; 
		$sheetCityEventPhoto[$event]['role'] = '0'; 
		$sheetCityEventPhoto[$event]['rolePic'] = ''; 
		$sheetCityEventPhoto[$event]['face'] = '';
		$sheetCityEventPhoto[$event]['backGround'] = ''; 
		}	
	else{
		continue;
		}
	$flagSafeRunning = true;
	for($i = 0; $i < count($file_arr); $i++){
		$tokens = explode($declareChar, $file_arr[$i]);
		if($i == 0){
			if(count($tokens) < 2){
				echo '文件 '.$fileName.' 第'.$i.'行声明无内容';
				echo '<br />';
				$flagSafeRunning = false;
				break;				
				}				
			if(strpos(trim($tokens[0]), '事件ID') === false){
				echo '文件 '.$fileName.' 检测不到事件ID';
				echo '<br />';
				$flagSafeRunning = false;
				break;				
				}			
			$eventID = trim($tokens[1]);
			if($eventID != $event){
				echo '文件 '.$fileName.' 事件ID有误';
				echo '<br />';
				$flagSafeRunning = false;
				break;
				}
			else{
				continue;
				}
			continue;
			}
		if($i == 1){
			$feelingNum = '0';
			if(count($tokens) < 2){
				echo '文件 '.$fileName.' 第'.$i.'行声明无内容';
				echo '<br />';
				$flagSafeRunning = false;
				break;				
				}			
			if(strpos(trim($tokens[0]), '场景') === false){
				echo '文件 '.$fileName.' 检测不到场景';
				echo '<br />';
				$flagSafeRunning = false;
				break;				
				}				
			$sceneStr = trim($tokens[1]);
			if(!array_key_exists($sceneStr, $sceneData)){
				echo '文件 '.$fileName.' 场景错误';
				echo '<br />';
				$flagSafeRunning = false;
				break;
				}
			else{
				$sceneID = $sceneData[$sceneStr];
				$sheetCityEventPhoto[$event]['backGround'] = $sceneID;
				$sceneIDNum = $sceneIdData[$sceneStr];
				$comment = trim($sceneStr);
				$comment .= ':';
				}
			if(count($tokens) > 2){
				$feelingNum = trim($tokens[2]); // 好感度阈值
				}	
			continue;				
			}
		if($i == 2){
			$avatarMapByFakeName = array();	
			$charaMapByFakeName = array();	
			if(count($tokens) < 2){
				echo '文件 '.$fileName.' 第'.$i.'行声明无内容';
				echo '<br />';
				$flagSafeRunning = false;
				break;				
				}				
			if(strpos(trim($tokens[0]), '角色') === false){
				echo '文件 '.$fileName.' 检测不到角色';
				echo '<br />';
				$flagSafeRunning = false;
				break;				
				}
			if(strpos($tokens[1], $semicolonChar) === false){
				echo '文件 '.$fileName.' 角色声明缺失分号';
				echo '<br />';
				$flagSafeRunning = false;
				break;					
				}
			$tokensChara = explode($semicolonChar, $tokens[1]);	
			$tokensCharaError = -1;
			for($j = 0; $j < count($tokensChara) - 1; $j++){
				 $charaTemp = explode($commaChar, $tokensChara[$j]);
				 if(count($charaTemp) < 2){
				 	$tokensCharaError = $j;
				 	break;
				 	}
				 $charaName = trim($charaTemp[0]);
				 if(strlen($sheetCityEventPhoto[$event]['rolePic']) == 0) $sheetCityEventPhoto[$event]['rolePic'] = $rolePicData[$charaName];	
				 $charaAvatar = trim($charaTemp[1]);
				 foreach($avatarData as $avatarKey => $avatarValue){
				 	if(strpos($charaAvatar, $avatarKey) === false) continue;
				 	else{
				 		$charaAvatar = $avatarKey;
				 		break;
				 		}
				 	}				 
				 if(count($charaTemp) < 3){
				 	$charaNameAlias = $charaName;
				 	}
				 else if(strlen($charaTemp[2]) < 3){
				 	$tokensCharaError = $j;
				 	break;
				 	}
				 else{
				 	$charaNameAlias = $charaTemp[2];
				 	}	
				 if(!array_key_exists($charaName, $characterData)){
					$tokensCharaError = $j;
					break;
					}				 	
				 $characterID = $characterData[$charaName];
				 if(strpos($comment, $characterID) === false){
				 	$comment .= $characterID;
				 	}				 
				 if($j + 1 < count($tokensChara) - 1){
				 	$comment .= ',';
				 	}
				 if($j == 0){
				 	$roleID = $roleData[$charaName];
				 	$sheetCityEventPhoto[$event]['role'] = $roleID;
				 	}
				 $avatarID = $avatarData[$charaAvatar];
				 $avatarMapByFakeName[$charaNameAlias] = $avatarID; //键值使用别名，以便让单一角色出现多个版本
				 $fakeNameMapByNum[$j] = $charaNameAlias;
				 $charaMapByFakeName[$charaNameAlias] = $charaName;
				}
			if($tokensCharaError >= 0){
				echo '文件 '.$fileName.' 中第'. $tokensCharaError .'个角色不符合规范';
				echo '<br />';
				$flagSafeRunning = false;
				break;				
				}
			continue;					
			}
		if($i == 3){
			$dressMapByFakeName = array();			
			if(count($tokens) < 2){
				continue;	//跳过皮肤声明		
				}	
			if(strpos(trim($tokens[0]), '皮肤') === false){
				echo '文件 '.$fileName.' 检测不到皮肤';
				echo '<br />';
				$flagSafeRunning = false;
				break;				
				}
			if(strpos($tokens[1], $semicolonChar) === false){
				echo '文件 '.$fileName.' 皮肤声明缺失分号';
				echo '<br />';
				$flagSafeRunning = false;
				break;					
				}	
			$tokensDress = explode($semicolonChar, $tokens[1]);	
			if(count($tokensDress) != count($tokensChara)){
				echo '文件 '.$fileName.' 皮肤声明个数错误';
				echo '<br />';
				$flagSafeRunning = false;
				break;					
				}
			for($j = 0; $j < count($tokensDress) - 1; $j++){
				$charaNameAlias = $fakeNameMapByNum[$j];
				$dressName = $tokensDress[$j];
				if(array_key_exists($dressName, $dressData)){
					$dressID = $dressData[$dressName];
					$dressMapByFakeName[$charaNameAlias] = $dressID; //键值使用别名，以便让单一角色出现多个版本
					}
				else{
					$dressMapByFakeName[$charaNameAlias] = '0';
					}
				}				
			continue;
			}
		if($i == 4){
			$dialogID = 1;
			$branchID = 0;
			$encodeDialogID = '';
			$dialogType = 1;
			$preDialog = '0';
			$preDialogStack = array();
			$dialogData = array();
			$textMap = array();
			$textMapID = '';
			$dialogLine = '';
			$name = '';	
			$side = ''; //位置
			$sideMapByFakeName = array();
			$transparency = '0.9'; //透明度
			$transparencyMapByFakeName = array();
			$face = ''; //表情
			$move = ''; //动作
			$moveMapByFakeName = array();
			$bgmCover = '';
			$cgId = '';
			$backgroundCG = '';			
			$winDialog = array();
			$loseDialog = array();
			$finalDialog = array();
			$checkPointMap = array();			
			$checkPoint = null;
			//设定初始值
			}
		if($i >= 4){
			if(count($tokens) < 2){
				$thisLineTemp = trim($tokens[0]);
				if(strpos($thisLineTemp, '>') === false) continue; //空行
				else if(strpos($thisLineTemp, '>>>>') === false){
					echo '文件 '.$fileName.' 第'.$i.'行出现错误分支符号';
					echo '<br />';
					$flagSafeRunning = false;
					break;					
					}
				else if(strpos($thisLineTemp, '>>>>') > 0){
					echo '文件 '.$fileName.' 第'.$i.'行出现错误分支符号';
					echo '<br />';
					$flagSafeRunning = false;
					break;					
					}
				else{ //处理分支符号>>>>
					$choiceText = substr($thisLineTemp, 4);
					if(strncmp($choiceText, 'WIN', 3) == 0){
						if (count($preDialogStack) < 1){
							break; //end
							}
						else{
							$winDialog[] = $preDialog;
							$finalDialog[] = $preDialog;
							$preDialog = array_pop($preDialogStack);
							}
						}
					else if(strncmp($choiceText, 'LOSE', 4) == 0){
						if (count($preDialogStack) < 1){
							echo '文件 '.$fileName.' 第'.$i.'行出现错误END符号';
							echo '<br />';
							$flagSafeRunning = false;							
							break;
							}
						else{
							$loseDialog[] = $preDialog;
							$finalDialog[] = $preDialog;
							$preDialog = array_pop($preDialogStack);
							}																			
						}
					else if(strncmp($choiceText, 'GOTO ', 5) == 0){
						$tempCheckPoint = trim(explode(' ', $choiceText)[1]);
						if(!array_key_exists($tempCheckPoint, $checkPointMap)){
							$checkPointMap[$tempCheckPoint] = $preDialog;
							}
						else{
							$strGoto = $checkPointMap[$tempCheckPoint] . "," . $preDialog;
							$checkPointMap[$tempCheckPoint] = $strGoto;
							}
						$preDialog = array_pop($preDialogStack);	
						}
					else if(strncmp($choiceText, 'CHECK ', 6) == 0){
						$tempCheckPoint = trim(explode(' ', $choiceText)[1]);						
						if(array_key_exists($tempCheckPoint, $checkPointMap)){
							$checkPoint = $checkPointMap[$tempCheckPoint];
							}
						else{
							echo '文件 '.$fileName.' 第'.$i.'行出现错误的checkpoint';
							echo '<br />';	
							$flagSafeRunning = false;						
							break;							
							}
						}
					else{
						$branchID++;
						$dialogID = 0;
						$encodeID = encodeIDGet($branchID, $dialogID);
						$encodeDialogID = encodeDialogIDGet($eventID, $encodeID);
						$dialogType = 2;
						$textMapID = textMapIDGet($encodeDialogID);
						$textMap[] = textLineMakeUp($textMapID, $choiceText);
						if(checkTextLenth($choiceText) >= $textLimitData['选项卡']){
							echo '文件 '.$fileName.' 第'.$i.'行字数超标('.checkTextLenth($choiceText).'): '.$choiceText;
							echo '<br />';	
							$flagSafeRunning = false;						
							break;							
							}
						$name = 'R_Captain';
						$avatarID = '0';
						$dressID = '0';
						$audioID = '0';
						$side = '0';
						$face = '0';
						$move = '0';
						$transparency = '0.9';
						$bgmCover = '';
						$cgId = '';
						$backgroundCG = '';
						if(isset($checkPoint)){
							$preDialog = $checkPoint . "," . $preDialog;
							$checkPoint = null;
							}
            $dialogLine = dialogLineMakeUp($encodeDialogID, $preDialog, $dialogType, $name, $avatarID, $dressID, $side, $face, $move, $transparency, $bgmCover, $cgId, $textMapID, $sceneID, $backgroundCG, $audioID);
            $dialogData[] = $dialogLine;
            $preDialogStack[] = $preDialog;	
            $preDialog = $encodeDialogID;	
            $dialogID++;					
						}	
					}
				}
			else{ //对话行
				$nameFake = trim($tokens[0]);
				$flagNoFaceNoMove = false;			
				$parentheseLeftExplode = explode('（', $nameFake);
				$nameFake = trim($parentheseLeftExplode[0]);
				if(array_key_exists($nameFake, $characterData)){
					$name = $characterData[$nameFake]; //Data格式
					if(isNoFaceNoMove($name)){ // 不需要立绘的说话者
						$flagNoFaceNoMove = true;
						$face = '0';
						$move = '0';
						$avatarID = '0';
						$dressID = '0';
						$audioID = '0';
						$side = $characterData[$name];
						$transparency = '0.9';
						$bgmCover = '';
						$cgId = '';
						$backgroundCG = '';								
						}						
					}					
				if(count($parentheseLeftExplode) < 2){ //没有括号					
					$face = '0';
					if(!$flagNoFaceNoMove && array_key_exists($nameFake, $moveMapByFakeName)){ //之前做过动作
						$move = '0';
						}
					else if(!$flagNoFaceNoMove){
							echo '文件 '.$fileName.' 第'.$i.'行动作代号错误：不能为空';
							echo '<br />';	
							$flagSafeRunning = false;						
							break;							
						}
					}
				else{ //名字后面至少有第一套括号
					$nameFake = trim($parentheseLeftExplode[0]); 					
					$parentheseInside = trim(explode('）', $parentheseLeftExplode[1])[0]);
					$parentheseCommaExplode = explode($commaChar, $parentheseInside);
					$face = trim($parentheseCommaExplode[0]);
					if(array_key_exists($face, $expressData)) $face = $expressData[$face];
					else if(strlen($face) == 0)	$face = $expressData['无'];
					else{
							echo '文件 '.$fileName.' 第'.$i.'行人物表情错误：'.$face;
							echo '<br />';	
							$flagSafeRunning = false;						
							break;							
							}						
					if(count($parentheseCommaExplode) < 2){ //括号内不包含动作
						if($flagNoFaceNoMove){
							$move = '0';
							}
						else if(array_key_exists($nameFake, $moveMapByFakeName)){ 
							$move = '0';
							}
						else{
							$move = '5'; //默认姿势
							$moveMapByFakeName[$nameFake] = $move;						
							}					
						}
					else{ //括号内包含动作
						$move = trim($parentheseCommaExplode[1]);
						if(array_key_exists($move, $moveData)){ //动作存在
							$name = $charaMapByFakeName[$nameFake];
							if(array_key_exists($name, $moveData[$move])){ //归属人存在
								$move = $moveData[$move][$name];
								$moveMapByFakeName[$nameFake] = $move; //历史非0表情记录在案
								}
							else{
								echo '文件 '.$fileName.' 第'.$i.'行动作代号错误：动作 '.$move.' 和角色 '.$name.' 不搭配';
								echo '<br />';	
								$flagSafeRunning = false;						
								break;								
								}
							}
						else{
							echo '文件 '.$fileName.' 第'.$i.'行动作代号错误：动作 '.$move.' 不存在';
							echo '<br />';	
							$flagSafeRunning = false;						
							break;							
							}	
						}
					if(!$flagNoFaceNoMove && array_key_exists($nameFake, $sideMapByFakeName)){
						$side = $sideMapByFakeName[$nameFake]; // 顺延
						}
					else{
						$side = $sideData['单独']; // 获得缺省值
						} 
					if(count($parentheseLeftExplode) > 2){ //名字后面有第二套括号——人物位置
						$parentheseInside = explode('）', $parentheseLeftExplode[2])[0];
						$side = trim($parentheseInside);
						if(array_key_exists($side, $sideData)){
							$side = $sideData[$side];
							$sideMapByFakeName[$nameFake] = $side; //历史位置记录在案
							}
						else if(array_key_exists($nameFake, $sideMapByFakeName)){
							$side = $sideMapByFakeName[$nameFake];
							}
						else{
							$side = $sideData['单独']; //默认值——清空其他人，仅保留此人
							}	
						}
					if(array_key_exists($nameFake, $transparencyMapByFakeName)) $transparency = $transparencyMapByFakeName[$nameFake]; // 获得缺省值
					else $transparency = '0.9'; // 获得缺省值	
					$bgmCover = '';
					$cgId = '';
					$backgroundCG = '';					
					if(count($parentheseLeftExplode) > 3){ // 名字后面有第三套括号—— 调用CG的id $cgId , 对白背景（CG）$backgroundCG , 透明度 $transparency , 覆盖当前BGM的地址 $bgmCover 
						$parentheseInside = explode('）', $parentheseLeftExplode[3])[0];
						$parentheseCommaExplode = explode($commaChar, $parentheseInside);							
						$cgId = trim($parentheseCommaExplode[0]);	// 调用CG的id		
						if(count($parentheseCommaExplode) > 1){
							$backgroundCG = trim($parentheseCommaExplode[1]);	 // 对白背景（CG）
							}						
						if(count($parentheseCommaExplode) > 2){ // 透明度
							if(is_numeric(trim($parentheseCommaExplode[2])) && trim($parentheseCommaExplode[2]) >= 0 && trim($parentheseCommaExplode[2]) <= 1){ // 校验
								$transparency = trim($parentheseCommaExplode[2]);
								$transparencyMapByFakeName[$nameFake] = $transparency;
								}								
							}					
						if(count($parentheseCommaExplode) > 3){
							$bgmCover = trim($parentheseCommaExplode[3]);	// 覆盖当前BGM的地址
							}						
						}			
					}
				if(array_key_exists($nameFake, $charaMapByFakeName)){ //确定对话者姓名
					if(strpos($nameFake, '旁白') === false){
						$name = $charaMapByFakeName[$nameFake]; //单个台本内，代号与姓名的映射			
						$name = $characterData[$name]; //Data格式						
						}
					else{
						$name = $characterData['旁白'];
						}
					} 
				else if(!$flagNoFaceNoMove){
					echo '文件 '.$fileName.' 第'.$i.'行人物代号错误：'.$nameFake;
					echo '<br />';		
					$flagSafeRunning = false;					
					break;						
					}					
				if(!$flagNoFaceNoMove){
					$avatarID = $avatarMapByFakeName[$nameFake];
					$dressID = '0';
					if(array_key_exists($nameFake, $dressMapByFakeName)){ //有定义皮肤的情形
						$dressID = $dressMapByFakeName[$nameFake];
						}					
					}
				if(count($tokens) > 2){ // 有语音id的情形
					$audioID = trim($tokens[2]);
					}	
				else{
					$audioID = '0';					
					}				
				$encodeID = encodeIDGet($branchID, $dialogID);
				$encodeDialogID = encodeDialogIDGet($eventID, $encodeID);
				$dialogType = 1;
				$textMapID = textMapIDGet($encodeDialogID);
				$textText = trim($tokens[1]);
				if(checkTextLenth($textText) >= $textLimitData['对话框']){
							echo '文件 '.$fileName.' 第'.$i.'行字数超标('.checkTextLenth($tokens[1]).'): '.$tokens[1];
							echo '<br />';		
							$flagSafeRunning = false;						
							break;							
							}
				$textText = '<color=' . $colorData[$name] . '>' . $textText . '</color>';
				$textMap[] = textLineMakeUp($textMapID, $textText);					
				if(isset($checkPoint)){
							$preDialog = $checkPoint . "," . $preDialog;
							$checkPoint = null;
							}	
				$dialogLine = dialogLineMakeUp($encodeDialogID, $preDialog, $dialogType, $name, $avatarID, $dressID, $side, $face, $move, $transparency, $bgmCover, $cgId, $textMapID, $sceneID, $backgroundCG, $audioID);	
				$dialogData[] = $dialogLine;
				$preDialog = $encodeDialogID;	
        $dialogID++;					
				}	//对话行-分歧处理结束				
			} //单行正文-分歧处理结束
		continue;
		} //按行循环
	if($flagSafeRunning === false){ // not safe
		continue; // pass this file
		}	
	else{
		foreach($dialogData as $dialogLine){
			fputcsv($dialogDataFile, $dialogLine, $splitChar);
			}//写入dialogData
		foreach($textMap as $textLine){
			fputcsv($textMapDataFile, $textLine, $splitChar);
			}//写入textMap
		$happeningLine = happeningLineMakeUp($eventID, $comment, $roleID, $sceneIDNum, $feelingNum);
		//var_dump($happeningLine);
		fputcsv($happeningDataFile, $happeningLine, $splitChar); //写入happeningData
		$plotLine = plotLineMakeUp($eventID, $winDialog, $encodeDialogID);
		fputcsv($plotDataFile, $plotLine, $splitChar); //写入plotData	
		echo '★☆★☆★☆ 事件 '.$event.' 完成 ☆★☆★☆★';
		echo '<br />';
		}	
	} //按文件循环

fclose($dialogDataFile);
fclose($textMapDataFile);
fclose($happeningDataFile);
fclose($plotDataFile);

$fileName = 'cityEventPhotoSet.txt';
if(file_exists($fileName)){
	$file_arr = file($fileName);
	$cityEventPhotoDataFile = fopen('cityEventPhotoData.txt', 'w');
	$textMapCityEventDataFile = fopen('textMapCityEventData.txt', 'w');
	for($i = 0; $i < count($file_arr); $i++){
		$tokens = explode($semicolonChar, $file_arr[$i]);
		$dialogLine = array();
		$textLine = array();
		$tempToken = explode($declareChar, $tokens[0]);
		$event = trim($tempToken[1]);
		if(array_key_exists($event, $sheetCityEventPhoto)){
			$dialogLine[] = $sheetCityEventPhoto[$event]['eventID'];
			$dialogLine[] = $sheetCityEventPhoto[$event]['cycleID'];
			$dialogLine[] = $sheetCityEventPhoto[$event]['plotName'];
			// plotNameText
			$tempToken = explode($declareChar, $tokens[1]);
			$sheetCityEventPhoto[$event]['plotNameText'] = trim($tempToken[1]);
			$textLine[] = $sheetCityEventPhoto[$event]['plotName'];
			$textLine[] = $sheetCityEventPhoto[$event]['plotNameText'];
			fputcsv($textMapCityEventDataFile, $textLine, $splitChar);
			// type
			$tempToken = explode($declareChar, $tokens[2]);
			$sheetCityEventPhoto[$event]['type'] = trim($tempToken[1]);
			if($sheetCityEventPhoto[$event]['type'] != 2){
				$sheetCityEventPhoto[$event]['role'] = '0';
				}
			$dialogLine[] = $sheetCityEventPhoto[$event]['type'];
			$dialogLine[] = $sheetCityEventPhoto[$event]['role'];
			$dialogLine[] = $rolePicData['Path'] . $sheetCityEventPhoto[$event]['rolePic'];
			// face
			$tempToken = explode($declareChar, $tokens[3]);
			$faceStr = trim($tempToken[1]);
			if(array_key_exists($faceStr, $faceCityEventData)){
				$sheetCityEventPhoto[$event]['face'] = $faceCityEventData['Path'] . $sheetCityEventPhoto[$event]['rolePic'] .  $faceCityEventData[$faceStr];
				}
			else{
				$sheetCityEventPhoto[$event]['face'] = $faceCityEventData['Path'] . $sheetCityEventPhoto[$event]['rolePic'] .  $faceCityEventData['Error'];
				echo '事件 '.$event.' 相册表情配置错误，已用默认表情代替';
				echo '<br />';				
				}
			$dialogLine[] = $sheetCityEventPhoto[$event]['face'];
			$dialogLine[] = $sheetCityEventPhoto[$event]['backGround'];
			fputcsv($cityEventPhotoDataFile, $dialogLine, $splitChar);
			}
		else continue;	
		}		
	fclose($cityEventPhotoDataFile);
	fclose($textMapCityEventDataFile);
	echo '★☆★☆★☆ 相册表情配置完成 ☆★☆★☆★';
	echo '<br />';	
	}

echo '<br />';
echo 'END';
	
function encodeIDGet($branchID, $dialogID){
	return $branchID * 100 + $dialogID;
	}
	
function encodeDialogIDGet($eventID, $encodeID){
	$answerTemp = $eventID . ($encodeID < 1000 ? '0' : '') . ($encodeID < 100 ? '0' : '') . ($encodeID < 10 ? '0' : '') . $encodeID;
	if($eventID > 99999){
		$answerTemp = $eventID .  ($encodeID < 100 ? '0' : '') . ($encodeID < 10 ? '0' : '') . $encodeID;
	}
	return $answerTemp;
	}
	
function textMapIDGet($encodeDialogID){
	return 'Chat_' . $encodeDialogID;
	}
	
function dialogLineMakeUp($encodeDialogID, $preDialog, $dialogType, $name, $avatarID, $dressID, $side, $face, $move, $transparency, $bgmCover, $cgId, $textMapID, $sceneID, $backgroundCG, $audioID){
  $dialogLine = array();         
  $dialogLine[] = $encodeDialogID;
  $dialogLine[] = $preDialog;
  $dialogLine[] = $dialogType;
  $dialogLine[] = $name;
  $dialogLine[] = $avatarID;
  $dialogLine[] = $dressID;
  $dialogLine[] = $side;
  $dialogLine[] = $face;
  $dialogLine[] = $move; //animID
  $dialogLine[] = '0';
  $dialogLine[] = $transparency; // 透明度
  $dialogLine[] = $bgmCover; // bgmCover
  $dialogLine[] = $cgId; // cgId
  $dialogLine[] = $textMapID;
  $dialogLine[] = $sceneID;
  $dialogLine[] = $backgroundCG; // backgroundCG
  $dialogLine[] = $audioID;
  $dialogLine[] = '0';            
  return $dialogLine;		
	}
	
function textLineMakeUp($textMapID, $textText){
	$textLine = array(); 
	$textLine[] = $textMapID;
	$textLine[] = $textText;
  return $textLine;		
	}
	
function happeningLineMakeUp($eventID, $comment, $roleID, $sceneIDNum, $feelingNum){
	$happeningLine = array();
	$happeningLine[] = $eventID;
	$happeningLine[] = $comment;
	$happeningLine[] = 2; // 1:家园剧情 2：夏活 //制作成选择不同源文件倒入的形式吧，写两份函数
	$happeningLine[] = $roleID;
	if($eventID > 20000){ //是否重复 0-不重复 1-重复
		$happeningLine[] = '0';
		}
	else{
		$happeningLine[] = '1';
		}
	$happeningLine[] = '1';	//循环方式
	$happeningLine[] = '1,2,3,4,5,6,7';//循环数据	
	$happeningLine[] = '00:00:00'; //开始时间
	$happeningLine[] = '23:59:59'; //结束时间
	$happeningLine[] = '205'; //触发方式：所属夏活建筑
	$happeningLine[] = $sceneIDNum;	//触发条件：夏活建筑ID
	$happeningLine[] = '4';	// 触发方式：夏活好感度
	$happeningLine[] = $feelingNum;	// 触发条件：好感度阈值
	$happeningLine[] = '0'; //触发方式3
	$happeningLine[] = '0'; //触发数据3	
	//优先级
	if(($eventID > 20000) && ($eventID % 10 != 1)){ //大于20000是连续事件，模10不等于1是连续事件排除开头的情况
		$happeningLine[] = '101'; 
		}
	else{
		$happeningLine[] = '100';  
		}
	//PlotID与eventID一致
	$happeningLine[] = $eventID;
	return $happeningLine;		
	}

function plotLineMakeUp($eventID, $winDialog, $encodeDialogID){
	$plotLine = array();
	$winDialogID = '';
	for($i = 0; $i < count($winDialog); $i++){
		$winDialogID .= $winDialog[$i];
		if($i < count($winDialog) - 1) $winDialogID .= ',';
		}
	if(count($winDialog) < 1){ //没有分支
		$winDialogID = $encodeDialogID; //最末一次编码
		}	
	$plotLine[] = $eventID;
	$plotTemp = $eventID . '0001';
	if($eventID > 99999) $plotTemp = $eventID . '001';
	$plotLine[] = $plotTemp;
	$plotLine[] = $winDialogID;
	$plotLine[] = '0'; //随机事件奖励
	$plotLine[] = '0.2'; //BGM音量控制
	return $plotLine;
	}
	
function isNoFaceNoMove($name){
	global $characterData;
	if(array_key_exists($name, $characterData)){
		return true;
		}
	else return false;
	}
	
function checkTextLenth($text){
	$lenth = 0;
	for($i=0; $i < strlen($text); $i++){
		$lenth++;
    if(ord(substr($text, $i, 1))> 0b11000000){  //utf-8两字节字符（希腊字母等），第一字节均为110xxxxx的格式  		
        $i++; 
    		}				
    if(ord(substr($text, $i, 1))> 0b11100000){  //utf-8三字节字符（汉字等），第一字节均为1110xxxx的格式  		
        $lenth++; //全角
        $i++; 
    		}
		}
	return $lenth / 2.0;
	}
?>