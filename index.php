<?php

date_default_timezone_set('Asia/Bangkok');
header('Content-Type: text/html; charset=utf-8');

header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
//header('Content-Type: application/json');


@ini_set('display_errors', '0'); //ไม่แสดงerror

$msg = $_GET["msg"];
//echo $msg;
//exit();

function emoji($input){

    $fullurl = "https://api.aiforthai.in.th/emoji?text=".urlencode($input);

    $header = array(
        "Apikey: NUCyyo4koUbIFFkqxYehuyB4YSJsxFEP"
    );
 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);      
        curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL, $fullurl);
         
        $returned =  curl_exec($ch);
	$err = curl_error($ch);
        curl_close($ch);
	
	if ($err) {	
	  //echo "cURL Error #:" . $err;
	  return($err);
	} else {
	  //echo $response;
	}
	
        //echo $returned;
	$obj = json_decode($returned);
	
	$emo='';
	$num=number_format("0.00000000",8);
	
	foreach($obj as $key => $val) {
		
		if($emo == '' || $num == 0.00000000){
			$emo = 	$key;
			$num = number_format($val,8);	
		}
		else{
			if($val> $num)	{
				$emo = 	$key;
				$num = number_format($val,8);			
			}
			else{
				
			}
		}
	}	
	//echo $emo;
	
        return($emo);	
	
}

//analysis('ทำไมถึงผิดหวังล่ะ---');
function analysis($input){
    $fullurl = "https://api.aiforthai.in.th/ssense?text=".urlencode($input);

    $header = array(
        "Apikey: NUCyyo4koUbIFFkqxYehuyB4YSJsxFEP"
    );
 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);      
        curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL, $fullurl);
         
        $returned =  curl_exec($ch);
	      $err = curl_error($ch);	
        curl_close($ch);

	if ($err) {
	  $obj = json_decode('{"sentiment":{"Polarity":"error"}}');
	  return($obj);	
	  //echo "cURL Error #:" . $err;
	} else {
	  //echo $returned;
	  $obj = json_decode($returned);
	  //echo  $obj;	
	  return($obj);	
	}	
}



  $ress = analysis($msg);
  $message ='';
  if($ress->sentiment->polarity == 'error'){
      $a = array(
        array(
            'type' => 'text',
            'text' => 'ขออภัย api เกิดข้อขัดข้อง'         
        )
      );
      //header('Content-Type: text/html; charset=utf-8');
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode($a);
  }
  else{
      $pos='';
      $think='';
      if($ress->sentiment->polarity == 'positive'){
          $think='บวก';
          $pos='เชิงบวก('.number_format($ress->sentiment->score,2).')';
      }
      else if($ress->sentiment->polarity == 'negative'){
          $think='ลบ';
          $pos='เชิงลบ('.number_format($ress->sentiment->score,2).')';
      }
      else{
          $think='กลาง';
          $pos='ไม่เป็นทั้งบวกและลบ('.number_format($ress->sentiment->score,2).')';
      }
      
      $typ = array();
      $typ['request'] = number_format($ress->intention->request,2);
      $typ['sentiment'] = number_format($ress->intention->sentiment,2);
      $typ['question'] = number_format($ress->intention->question,2);
      $typ['announcement'] = number_format($ress->intention->announcement,2);	
      
      $max = number_format(0.00,2);
      $use ='ไม่สามารถระบุประเภทข้อความได้';
      
      foreach($typ as $key => $val) {
        if($val>$max){
          $max=$val;
          if($key == 'request'){
            $use='ร้องขอ';
          }
          else if($key == 'sentiment'){
            $use='แสดงความคิดเห็น';
          }
          else if($key == 'question'){
            $use='คำถาม';
          }
          else if($key == 'announcement'){
            $use='ประกาศหรือโฆษณา';
          }						
          else{
          }
        }
      }
      

      /*
          $message ='ร้องขอ:'.number_format($ress->intention->request,2).'/n'.
          'แสดงความคิดเห็น:'.number_format($ress->intention->sentiment,2).'/n'.
          'คำถาม:'.number_format($ress->intention->question,2).'/n'.
          'ประกาศหรือโฆษณา:'.number_format($ress->intention->announcement,2).'/n'.
          ',ลักษณะข้อความ:'.$pos;
      */

      

          //$t=array("โอเค","ไม่โอเค","ตกลง","ไม่สะดวก","ไม่ว่าง");
          //$random_keys=array_rand($t,1);
          //$txt = $t[$random_keys];
        
          
        $a = array(
          array(
                'type' => 'text',
                'group' => $use,
                'per_group' => $max,
                'polarity' =>$think,
                'per_polarity' =>number_format($ress->sentiment->score,2),

                'text' => $use.'('.$max.')'.',ลักษณะข้อความ:'.$pos         
          )
        );
        //header('Content-Type: text/html; charset=utf-8');
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($a);
      
    }
    

                
   
?>




