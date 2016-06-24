<?php
$answer = '';

$accukey = 'GJkfyAOsEvEM9BrJLJejV0XZJZitcAXw';
$location = array(
  'tomchak' => '59.890234%2C30.324573', //томчак key 580864

);

function cGet($message) {
  $out = '';
  if($handle = curl_init()){
    curl_setopt($handle, CURLOPT_URL, $message);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($handle, CURLOPT_ENCODING , "gzip");
    $response = curl_exec($handle);
    if ($response === false) {
      $errno = curl_errno($handle);
      $error = curl_error($handle);
      error_log("Curl returned error $errno: $error\n");
      curl_close($handle);
    }else{
      $http_code = intval(curl_getinfo($handle, CURLINFO_HTTP_CODE));
      curl_close($handle);
      $response = json_decode($response, true);
      if ($http_code != 200) {
        error_log("Request has failed with error {$response['Code']}: {$response['Message']}\n");
      //}else{
        if (isset($response['Message'])) {
          error_log("Request was successfull: {$response['Message']}\n");
        }
        $out = $response;
      }
    }
  }
  return $out;
}

$getAddr = cGet('http://dataservice.accuweather.com/locations/v1/cities/geoposition/search?apikey='.$accukey.
	'&q='.$location['tomchak'].'&language=ru-ru&details=true&toplevel=false');

//$answer = $getAddr['Key'];


$answer = cGet('http://dataservice.accuweather.com/currentconditions/v1/'.$getAddr['Key'].'?apikey='.$accukey.
  '&language=ru-ru&details=false');



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
