<?php

$dest=$_REQUEST['dest'];
$url = 'http://maps.google.com/maps/geo?q='.urlencode($dest).'&output=json';
//$url = 'http://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($dest).'&sensor=false';
// make the HTTP request
echo $url;

$data = curl($url);
// parse the json response
echo $data;
$jsondata = json_decode($data,true);
// if we get a placemark array and the status was good, 
if(is_array($jsondata)) 
{
	echo "test1 ";
	if ($jsondata ['Status']['code']==200)
		{
		echo "Here ";
		      $lat = $jsondata ['Placemark'][0]['Point']['coordinates'][1];
		      $lng = $jsondata ['Placemark'][0]['Point']['coordinates'][0];
		}
}
echo $lat.", ".$lng;


function curl($url){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;
        }


?>