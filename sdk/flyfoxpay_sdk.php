<?php
require_once("flyfoxpay_config/flyfoxpay.config.php");
function printf_info($data)
{
    foreach($data as $key=>$value){
                echo "<font color='#f00;'>";
		if($key=='status'){echo '狀態';}
		elseif($key=='okpay'){echo '支付成功';}
		elseif($key=='nopay'){echo '未結帳';}
		elseif($key=='errorpay'){echo '支付成功但金額錯誤';}
		elseif($key=='msg'){echo '說明';}
		elseif($key=='money'){echo '金額';}
		elseif($key=='error'){echo '錯誤';}
		elseif($key=='time'){echo '建立時間';}
		elseif($key=='timeok'){echo '處理日期';}
		elseif($key=='idcode'){echo '交易序號';}
		elseif($key=='status_trade'){echo '交易狀態';}
		elseif($key=='type'){echo '支付方式';}
		elseif($key=='fee'){echo '手續費';}
		elseif($key=='new_money'){echo '帳號內剩餘金額';}
		elseif($key=='id'){echo '提現ID';}
		else{echo $key;}
		echo "</font> : ".htmlspecialchars($value, ENT_QUOTES)." <br/>";
    }
}
function printf_infos($data)
{
	$fa=$data['number'];
    for ($x=0; $x<=$fa; $x++) {
		$dats=$data['list_withdraw'][$x];
    if($x==$fa){die;}else{
    foreach($dats as $key=>$value){
                echo "<font color='#f00;'>";
		if($key=='status'){echo '狀態';}
		elseif($key=='okpay'){echo '支付成功';}
		elseif($key=='nopay'){echo '未結帳';}
		elseif($key=='errorpay'){echo '支付成功但金額錯誤';}
		elseif($key=='msg'){echo '說明';}
		elseif($key=='money'){echo '金額';}
		elseif($key=='error'){echo '錯誤';}
		elseif($key=='time'){echo '建立時間';}
		elseif($key=='timeok'){echo '處理日期';}
		elseif($key=='idcode'){echo '交易序號';}
		elseif($key=='status_trade'){echo '交易狀態';}
		elseif($key=='type'){echo '支付方式';}
		elseif($key=='fee'){echo '手續費';}
		elseif($key=='new_money'){echo '帳號內剩餘金額';}
		elseif($key=='id'){echo '提現ID';}
		else{echo $key;}
		echo "</font> : ".$value." <br/>";
}	        echo '<HR>';
} 
}
}
function curl_post($url,$post)
{
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST,true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
$result = curl_exec($ch);
curl_close ($ch);
return $result;
}

$flyfoxpay = new flyfoxpay($flyfoxpay_config);
class flyfoxpay {
    function __construct($flyfoxpay_config){
	$this->flyfoxpay_config = $flyfoxpay_config;
	}
    function flyfoxpay($flyfoxpay_config) {
    	$this->__construct($flyfoxpay_config);
    }
    /**
     * 建立訂單API
	 * @type如果未設定將直接選擇全支付方式
     */
function addpay($trade_no, $trade_name, $amount, $type,$customize1,$customize2,$customize3) {
 $key=$this->flyfoxpay_config['key'];
 $id=$this->flyfoxpay_config['id'];
 $mail=$this->flyfoxpay_config['mail'];
 $return=$this->flyfoxpay_config['return'];
 $url = "https://api.flyfoxpay.com/api/";//API位置
 $post_value=array(
       "key"=>$key, //商家KEY
       "id"=>$id, //商家ID
       "mail"=>$mail, //商家EMAIL
       "trade_no"=>$trade_no, //商家訂單ID
       "amount"=>$amount, //訂單金額(需大於50)
       "trade_name"=>$trade_name, //訂單名稱
       "type"=>$type, //指定付款方式，預設為all
       "return"=>$return, //支付完成返回網址
       "customize1"=>$customize1,//自訂義1
       "customize2"=>$customize2,//自訂義2
       "customize3"=>$customize3,//自訂義3
                    ); 
 $output = curl_post($url,$post_value);
 $json=json_decode($output, true);

return array('status' => $json['status'], 'url' => $json['url'], 'error' => $json['error']);
}
/**
     * 檢查訂單API
     */
function check($trade_no,$trade_nos) {
 $key=$this->flyfoxpay_config['key'];
 $id=$this->flyfoxpay_config['id'];
 $mail=$this->flyfoxpay_config['mail'];
 $url = "https://api.flyfoxpay.com/api/check/";//API位置
 $post_value= array(
       "key"=>$key, //商家KEY
       "id"=>$id, //商家ID
       "mail"=>$mail, //商家EMAIL
       "trade_no"=>$trade_no, //商家訂單ID
       "trade_nos"=>$trade_nos, //支付號ID
                    ); 
   $output = curl_post($url,$post_value);
   $json=json_decode($output, true);
   $security1  = array();

   $security1['mchid']      = $id;//商家ID

   $security1['status']        = "7";//驗證，請勿更改

   $security1['mail']      = $mail;//商家EMAIL

   $security1['trade_no']      = $json['trade_no'];//商家訂單ID
	
   $o='';
     foreach ($security1 as $k=>$v)
  {
    $o.= "$k=".($v)."&";
  }

    $sign1 = md5(substr($o,0,-1).$key);//**********請替換成商家KEY
if($json['sign']==$sign1){
  $sHtml = array('status' => $json['status'],'status_trade' => $json['status_trade'],'msg' => '驗證成功','type'=>$json['type'],'trade_no'=>$json['trade_no']);
}else{
  $sHtml = array('status' => $json['status'],'error' => $json['error'],'msg' => '驗證失敗');
}

return $sHtml;
}
/**
     * 查詢訂單數量API
     */
function check_order() {
$key=$this->flyfoxpay_config['key'];
$id=$this->flyfoxpay_config['id'];
$mail=$this->flyfoxpay_config['mail'];
$url = "https://api.flyfoxpay.com/api/check_order/";//API位置
 
$post_value= array("key"=>$key, //商家KEY
       "id"=>$id, //商家ID
       "mail"=>$mail, //商家EMAIL
       ); 
$output = curl_post($url,$post_value);

$json=json_decode($output, true);


return $json;
}
/**
     * 查詢帳號餘額API
     */
function search() {
$key=$this->flyfoxpay_config['key'];
$id=$this->flyfoxpay_config['id'];
$mail=$this->flyfoxpay_config['mail'];
$url = "https://api.flyfoxpay.com/api/search/";//API位置
$post_value = array("key"=>$key, //商家KEY
       "id"=>$id, //商家ID
       "mail"=>$mail, //商家EMAIL
       ); 
$output = curl_post($url,$post_value);

$json=json_decode($output, true);


return $json;
}
/**
     * 提現列表API
     */
function list_withdraw() {
$key=$this->flyfoxpay_config['key'];
$id=$this->flyfoxpay_config['id'];
$mail=$this->flyfoxpay_config['mail'];
$url = "https://api.flyfoxpay.com/api/list_withdraw/";//API位置
 $post_value= array("key"=>$key, //商家KEY
       "id"=>$id, //商家ID
       "mail"=>$mail, //商家EMAIL
       ); 
$output = curl_post($url,$post_value);
$json=json_decode($output, true);


return $json;
}
/**
     * 提現狀態查詢API
     */
function check_withdraw($withdrawid) {
$key=$this->flyfoxpay_config['key'];
$id=$this->flyfoxpay_config['id'];
$mail=$this->flyfoxpay_config['mail'];
$url = "https://api.flyfoxpay.com/api/check_withdraw/";//API位置
 $post_value= array("key"=>$key, //商家KEY
       "id"=>$id, //商家ID
       "mail"=>$mail, //商家EMAIL
	   "withdrawid"=>$withdrawid,//提現ID
       ); 
$output = curl_post($url,$post_value);
$json=json_decode($output, true);


return $json;
}
/**
     * 提現狀態查詢API
     */
function withdraw($type,$money,$bank,$bank_name,$bank_code) {
$key=$this->flyfoxpay_config['key'];
$id=$this->flyfoxpay_config['id'];
$mail=$this->flyfoxpay_config['mail'];
$url = "https://api.flyfoxpay.com/api/withdraw/";//API位置
$post_value= array("key"=>$key, //商家KEY
       "id"=>$id, //商家ID
       "mail"=>$mail, //商家EMAIL
       "money"=>$money, //提現金額(依照"費率表-提現手續費"中為準)
       "type"=>$type, //提現方式(這裡以支付寶為例)
       "alipay"=>$bank, //支付寶帳號
       "alipay_name"=>$bank_name, //支付寶帳號所有人名字
       //銀行提現方式
       "bank"=>$bank, //銀行帳號
       "bank_name"=>$bank_name, //銀行帳號收款人名字
       //銀行提現方式(台灣)
       "bank_code"=>$bank_code, //台灣地區銀行代碼
       ); 
$output = curl_post($url,$post_value);
$json=json_decode($output, true);


return $json;
}
/**
     * 取得新商家KEY API
     */
function rekey() {
$key=$this->flyfoxpay_config['key'];
$id=$this->flyfoxpay_config['id'];
$mail=$this->flyfoxpay_config['mail'];
$url = "https://api.flyfoxpay.com/api/rekey/";//API位置
 
$post_value= array("key"=>$key, //商家KEY
       "id"=>$id, //商家ID
       "mail"=>$mail, //商家EMAIL
       ); 
$output = curl_post($url,$post_value);

$json=json_decode($output, true);


return $json;
}
/**
     * 修改後台登入密碼 API
     */
function passwd($passwdold,$passwdnew,$passwdnews) {
$key=$this->flyfoxpay_config['key'];
$id=$this->flyfoxpay_config['id'];
$mail=$this->flyfoxpay_config['mail'];
$url = "https://api.flyfoxpay.com/api/passwd/";//API位置
$post_value= array("key"=>$key, //商家KEY
       "id"=>$id, //商家ID
       "mail"=>$mail, //商家EMAIL
	   "passwdold"=>$passwdold, //舊密碼
       "passwdnew"=>$passwdnew, //新密碼
       "passwdnews"=>$passwdnews, //再次輸入新密碼
       ); 
$output = curl_post($url,$post_value);
$json=json_decode($output, true);


return $json;
}
/**
     * 修改後台登入密碼 API
     */
function callback($notify_url) {
$key=$this->flyfoxpay_config['key'];
$id=$this->flyfoxpay_config['id'];
$mail=$this->flyfoxpay_config['mail'];
$url = "https://api.flyfoxpay.com/api/callback/";//API位置
$post_value= array("key"=>$key, //商家KEY
       "id"=>$id, //商家ID
       "mail"=>$mail, //商家EMAIL
       //二選一，同時存在已null優先
       "null"=>$notify_url, //清除現有notify_url(請固定填入1)
       "notify_url"=>$notify_url, //callback網址(https://申請時的網址/路徑)
       ); 
$output = curl_post($url,$post_value);
$json=json_decode($output, true);


return $json;
}
/**
     * 取消訂單API
     */
function cancel_order($trade_no) {
$key=$this->flyfoxpay_config['key'];
$id=$this->flyfoxpay_config['id'];
$mail=$this->flyfoxpay_config['mail'];
$url = "https://api.flyfoxpay.com/api/cancel_order/";//API位置
$post_value= array("key"=>$key, //商戶KEY
       "id"=>$id, //商戶ID
       "mail"=>$mail, //商戶EMAIL
       "trade_no"=>$trade_no, //商戶訂單ID
       ); 
$output = curl_post($url,$post_value);
$json=json_decode($output, true);


return $json;
}
	/**
     * 取消提現申請API
     */
function cancel_withdraw($no) {
$key=$this->flyfoxpay_config['key'];
$id=$this->flyfoxpay_config['id'];
$mail=$this->flyfoxpay_config['mail'];
$url = "https://api.flyfoxpay.com/api/cancel_withdraw/";//API位置
$post_value= array("key"=>$key, //商戶KEY
       "id"=>$id, //商戶ID
       "mail"=>$mail, //商戶EMAIL
       "no"=>$no, //商戶訂單ID
       ); 
$output = curl_post($url,$post_value);
$json=json_decode($output, true);


return $json;
}
}
?>
