<?php
date_default_timezone_set('Asia/Jakarta');
require_once("sdata-modules.php");
/**
* @Author: MR.RIUS
* @Last Modified time: 2018-08-17 15:13:34
*/


##############################################################################################################
$config['deviceCode'] 		= '862387037856646';
$config['tk'] 			= 'ACAJd4hjqSxNUajJqB4UfPuvytA9c7PZQRxxdHRodw';
$config['token'] 		= 'ec1ektYz6HLi8NqNLYsoyidrZEDoAFqYZjmaoHReDN_Yy8tBr5oODZ_t8TdgATMnDiN0VIizWSvTmRE';
$config['uuid'] 		= '5bbf9e3b82fc4a138d4039e411c0a54b';
$config['sign'] 		= 'd80b7e2babfac2fdf6b3cfaf25fe4b81';
$config['android_id'] 		= '7714a6d2ecb583ba';
##############################################################################################################


for ($x=0; $x <1; $x++) { 
	$url 	= array(); 
	for ($cid=0; $cid <20; $cid++) { 
		for ($page=0; $page <10; $page++) { 
			$url[] = array(
				'url' 	=> 'http://api.baca.co.id/content/getList?cid='.$cid.'&page='.$page,
				'note' 	=> 'optional', 
			);
		}
		$ambilBerita = $sdata->sdata($url); unset($url);unset($header);
		foreach ($ambilBerita as $key => $value) {
			$jdata = json_decode($value[respons],true);
			foreach ($jdata[data][data] as $key => $dataArtikel) {
				$artikel[] = $dataArtikel[id];
			}
		}
		$artikel = array_unique($artikel);
		echo "[+] Mengambil data artikel (CID : ".$cid.") ==> ".count(array_unique($artikel))."\r\n";
	}
	while (TRUE) {
		$timeIn30Minutes = time() + 30*60;
		$rnd 	= array_rand($artikel); 
		$id 	= $artikel[$rnd];
		$url[] = array(
			'url' 	=> 'http://api.beritaqu.net/timing/read',
			'note' 	=> $rnd, 
		);
		$header[] = array(
			'post' => 'OSVersion=8.0.0&android_channel=google&android_id='.$config['android_id'].'&content_id='.$id.'&content_type=1&deviceCode='.$config['deviceCode'].'&device_brand=samsung&device_ip=114.124.239.'.rand(0,255).'&device_version=SM-A730F&dtu=001&lat=&lon=&network=wifi&pack_channel=google&time='.$timeIn30Minutes.'&tk='.$config['tk'].'&token='.$config['token'].'&uuid='.$config['uuid'].'&version=10047&versionName=1.4.7&sign='.$config['sign'], 
		);
		$respons = $sdata->sdata($url , $header); 
		unset($url);unset($header);
		foreach ($respons as $key => $value) {
			$rjson = json_decode($value[respons],true);
			echo "[+][".$id." (Live : ".count($artikel).")] Message : ".$rjson['message']." | Poin : ".$rjson['data']['amount']." | Read Second : ".$rjson['data']['current_read_second']."\r\n";
			if($rjson[code] == '-20003' || $rjson['data']['current_read_second'] == '330' || $rjson['data']['amount'] == 0){
				unset($artikel[$value[data][note]]);
			}
		}
		if(count($artikel) == 0){
			sleep(30);
			break;
		}
		sleep(5);
	}
	$x++;
}
