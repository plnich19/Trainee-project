<?php 
    /*Get Data From POST Http Request*/
    //require __DIR__ . '/vendor/autoload.php';
    //use Google\Cloud\Vision\V1\ImageAnnotatorClient;

    $datas = file_get_contents('php://input');
    
    // {
    //     "events":[
    //         {
    //             "type":"message","replyToken":"7d7d02d64b954dfcb93683de8cc41f5f",
    //             "source":{"userId":"U408d7883b3c5391189fe4be0bacc510d","type":"user"},
    //             "timestamp":1552505328709,
    //             "message":{
    //                 "type":"text",
    //                 "id":"9509179836165",
    //                 "text":"ทดสอบ"
    //             }
    //         }
    //     ],"destination":"Uf34ef2c60c48fb450b5abba7bda444cf"
    
    // }

        //ได้ id มาเอาไปหาภาพอีกทีหนุึ่ง
    //{
    //    "events":[
//         {
//             "type":"message","replyToken":"9a9c39247cee4894853fb7096df72876",
//             "source":{"userId":"U6c8d9c32882439159292ff6d8e5f7b4b","type":"user"},
//             "timestamp":1559880656735,
//             "message":{
//                 "type":"image",
//                 "id":"9998872496217",
//                 "contentProvider":{"type":"line"}
//             }
//         }
//     ],"destination":"U7e344ad935ab7fba93e339b7208b212d"
// }

    

	/*Decode Json From LINE Data Body*/
	$deCode = json_decode($datas,true);

	// file_put_contents('log.txt', file_get_contents('php://input') . PHP_EOL, FILE_APPEND);
    $replyToken = $deCode['events'][0]['replyToken'];
    $messages = [];
    $messages['replyToken'] = $replyToken;

    $userId = $deCode['events'][0]['source']['userId'];
    $userMsg = $deCode['events'][0]['message']['text'];
    $userImg = $deCode['events'][0]['message']['id'];
    $msgType = $deCode['events'][0]['message']['type'];

    $type = $deCode['events'][0]['type'];

    $length = 0;

    $LINEDatas['url'] = "https://api.line.me/v2/bot/message/reply";
    $LINEDatas['token'] = "Nk3abhHVxBzKAWlabRc5Zv26MJICEUAePNJheXK/5hi3+XdfigCpR/RzRpqkPgGv6Xei5pz17m+fB+TgVRyilu9rl0Dk7dvtzroqrwGysALsoyXa01XZG4z/XIfyQKpSnuUBHIVJ+y3c2o10zG1clwdB04t89/1O/w1cDnyilFU=";
      
    if($type === "follow"){
        $messages['messages'][0] = getFormatTextMessage("ยินดีต้อนรับสู่บอทเช็คหวย ท่านสามารถใส่หมายเลขหวยได้ที่นี่ค่ะ");  
    }
    else{
        if($msgType === "text"){
            if($userMsg === "ตรวจหวย"){
                $messages['messages'][0] = getFormatTextMessage("โปรดใส่เลขหวยของท่าน");  
            }else{
                $error = false;
                $lotto = str_split($userMsg);
                $length = count($lotto);
                foreach($lotto as &$value) {
                    if(!ctype_digit($value)){
                        $error = true;
                    }
                } 
                if(!$error){
                    if($length === 6){
                        $messages['messages'][0] = getFormatTextMessage("Text type correct");    
                            //พื้นที่เช็คหวย ต้องต่อกับดาต้าเบส solr ก่อน if(){
                
                            // }
                            // else{
                
                            // }
                    }
                    else{
                        $messages['messages'][0] = getFormatTextMessage("กรุณากรอกตัวเลขให้ครบ 6 หลัก");
                    }
                }
                
                else{
                    $messages['messages'][0] = getFormatTextMessage("กรุณาตอบแต่ตัวเลข");
                    //$messages['messages'][0] = getFormatTextMessage($datas);
                }    
            }
        }
        else if($msgType === "image"){
            //ทำ GOOGLE OCR ที่นี่
            //Save รูปลง local host
            $result = testCurl($userImg,$LINEDatas['token']);
            // echo "<pre>";
            // print_r($result);
            // echo "</pre>";
            $filename = ".$userId..jpg";

            $file = fopen($filename,"w");
            fwrite($file,$result);
            fclose($file);
            $messages['messages'][0] = getFormatImageMessage("https://sv1.picz.in.th/images/2019/06/10/13KwXP.jpg","https://sv1.picz.in.th/images/2019/06/10/13KtEI.jpg");
        }

        else if($msgType === "video"){
            //ส่งเป็นวิดีโอกลับไป
            $messages['messages'][0] = getFormatVideoMessage("https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_5mb.mp4","https://sv1.picz.in.th/images/2019/06/10/13KtEI.jpg");
        }
	else if($msgType === "audio"){
            //ส่งเป็นเสียงกลับไป
            $messages['messages'][0] = getFormatAudioMessage("http://techslides.com/demos/samples/sample.m4a");
        }

        else if($msgType === "location"){
            //ส่งเป็นโลเคชันกลับไป
            $messages['messages'][0] = getFormatLocationMessage("the sims");
        }

        else if($msgType === "sticker"){
            //ส่งเป็นสติกเกอร์กลับไป
            $messages['messages'][0] = getFormatStickerMessage("1","1");
        }
    }
        
    

//     $strUrl = "https://vision.googleapis.com/v1/images:annotate?key=AIzaSyDzwELQjOIjz2m3R0ZpHCPDLWMS-KsE1HY";
    
//     function detectText($file){
//         $imageAnnotator = new ImageAnnotatorClient();
//         $response = $imageAnnotator->textDetection($file);
//         $texts = $response->getTextAnnotations();
//     }
//     printf('%d texts found:' . PHP_EOL, count($texts));
//     foreach ($texts as $text) {
//         print($text->getDescription() . PHP_EOL);
//         # get bounds
//         $vertices = $text->getBoundingPoly()->getVertices();
//         $bounds = [];
//         foreach ($vertices as $vertex) {
//             $bounds[] = sprintf('(%d,%d)', $vertex->getX(), $vertex->getY());
//         }
//         print('Bounds: ' . join(', ',$bounds) . PHP_EOL);
//     }
//     if ($error = $response->getError()) {
//         print('API Error: ' . $error->getMessage() . PHP_EOL);
//     }
//     $imageAnnotator->close();
// }

	$encodeJson = json_encode($messages);

	

  	$results = sentMessage($encodeJson,$LINEDatas);

	/*Return HTTP Request 200*/
	http_response_code(200);

	function getFormatTextMessage($text)
	{
		$datas = [];
		$datas['type'] = 'text';
		$datas['text'] = $text;

		return $datas;
	}

	function sentMessage($encodeJson,$datas)
	{
		$datasReturn = [];
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $datas['url'],
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => $encodeJson,
		  CURLOPT_HTTPHEADER => array(
		    "authorization: Bearer ".$datas['token'],
		    "cache-control: no-cache",
		    "content-type: application/json; charset=UTF-8",
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		    $datasReturn['result'] = 'E';
		    $datasReturn['message'] = $err;
		} else {
		    if($response == "{}"){
			$datasReturn['result'] = 'S';
			$datasReturn['message'] = 'Success';
		    }else{
			$datasReturn['result'] = 'E';
			$datasReturn['message'] = $response;
		    }
		}

		return $datasReturn;
    }
    
    function _secureRequest($secret,$secretKey,$url = ''){
        if(!$url){ exit('empty requestUrl');}
        $massage = _request($url);
        
        if(true){
            return $massage;
        }else{
            return array('status'=>'Unauthorized');
        }
    }
    
    function _request($url){
    
        $ch = curl_init();
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL,$url);
        $result=curl_exec($ch);
        curl_close($ch);
        return json_decode($result, true);
        // return $result;
    
    }

   function testCurl($id,$token){
       $url = "https://api.line.me/v2/bot/message/".$id."/content";
       $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "authorization: Bearer ".$token,
            "cache-control: no-cache",
            "content-type: application/json; charset=UTF-8",
          ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
       return $response;
   }

   function getFormatImageMessage($oriContent, $preImg)
	{
		$datas = [];
		$datas['type'] = 'image';
        $datas['originalContentUrl'] = $oriContent;
        $datas['previewImageUrl'] = $preImg;

		return $datas;
    }
    
    function getFormatVideoMessage($oriContent, $preImg)
	{
		$datas = [];
		$datas['type'] = 'video';
        $datas['originalContentUrl'] = $oriContent;
        $datas['previewImageUrl'] = $preImg;

		return $datas;
    }

    function getFormatAudioMessage($oriContent)
	{
		$datas = [];
		$datas['type'] = 'audio';
        $datas['originalContentUrl'] = $oriContent;
        $datas['duration'] = 240000;

		return $datas;
    }

    function getFormatLocationMessage($lo)
	{
		$datas = [];
		$datas['type'] = 'location';
        $datas['title'] = $lo;
        $datas['address'] = 'Bangkok Thailand';
        $datas['latitude'] = 35.65910807942215;
        $datas['longitude'] = 139.70372892916203;
        
        return $datas;
    }

    function getFormatStickerMessage($packetID,$stickerID)
	{
		$datas = [];
		$datas['type'] = 'sticker';
        $datas['packageId'] = $packetID;
        $datas['stickerId'] = $stickerID;

		return $datas;
    }
?>

