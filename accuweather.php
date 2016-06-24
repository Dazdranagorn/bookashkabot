<?php
$answer = '';

$accukey = 'GJkfyAOsEvEM9BrJLJejV0XZJZitcAXw';
$location = array(
  'tomchak' => '59.890234%2C30.324573', //томчак key 580864

);

function cGet($message) {
  if($curl = curl_init()){
    curl_setopt($curl, CURLOPT_URL, $message);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl, CURLOPT_ENCODING , "gzip");
    $out = curl_exec($curl);
    curl_close($curl);
  }
  return $out;
}

$getAddr = cGet('http://dataservice.accuweather.com/locations/v1/cities/geoposition/search?apikey='.$accukey.
	'&q='.$location['tomchak'].'&language=ru-ru&details=true&toplevel=false');

$answer = $getAddr['Key'];

/*
$answer = cGet('http://dataservice.accuweather.com/currentconditions/v1/'.$getAddr['Key'].'?apikey='.$accukey.
  '&language=ru-ru&details=false');
*/


/*
  if( $curl = curl_init() ) {
    curl_setopt($curl, CURLOPT_URL, 'http://mysite.ru/receiver.php');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, "a=4&b=7");
    $out = curl_exec($curl);
    echo $out;
    curl_close($curl);
  }
*/
return $answer;
?>
