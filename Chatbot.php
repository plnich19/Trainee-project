<?php 
    /*Get Data From POST Http Request*/
    
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

    

	/*Decode Json From LINE Data Body*/
	$deCode = json_decode($datas,true);

	// file_put_contents('log.txt', file_get_contents('php://input') . PHP_EOL, FILE_APPEND);
    $replyToken = $deCode['events'][0]['replyToken'];
    $messages = [];
    $messages['replyToken'] = $replyToken;

    $userId = $deCode['events'][0]['source']['userId'];
    $userMsg = $deCode['events'][0]['message']['text'];

    $type = $deCode['events'][0]['type'];

    $length = 0;

    if($type === "follow"){
        $messages['messages'][0] = getFormatTextMessage("ยินดีต้อนรับสู่บอทเช็คหวย
        ท่านสามารถใส่หมายเลขหวยได้ที่นี่");  
    }
    else{

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
            }
            
}
    }

    

	$encodeJson = json_encode($messages);

	$LINEDatas['url'] = "https://api.line.me/v2/bot/message/reply";
  	$LINEDatas['token'] = "hk9cuxsytiI5CqBh1GWdcyHKDPye5H/cMeANLF2KsHFE19ZIzHliYqZpWP7RB1ZW6Xei5pz17m+fB+TgVRyilu9rl0Dk7dvtzroqrwGysAJoK4w5rYaRBaMS/3q029VufRupJmCG1ugBp2WP/GxqGQdB04t89/1O/w1cDnyilFU=";

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
?>