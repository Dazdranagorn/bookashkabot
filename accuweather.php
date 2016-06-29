<?php
$accukey = 'GJkfyAOsEvEM9BrJLJejV0XZJZitcAXw';
$locationKey = array(
  'tomchak' => '580864', //томчак 
);

function cGet($message) {
  $out = '';
  if($handle = curl_init()){
    curl_setopt($handle, CURLOPT_URL, $message);
    curl_setopt($handle, CURLOPT_HEADER, false);
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
      //$response = json_decode($response, true);
      if ($http_code != 200) {
        error_log("Request has failed with error {$http_code}: {$response}\n");
        $out = 'error:';
        $out .= strval($http_code);
        $out .= "\n";
        $out .= $response;
      }else{
        $out = $response;
      }
    }
  }
  return $out;
}

function getLocation() {
  $answer = '';
  $out = cGet('http://dataservice.accuweather.com/locations/v1/cities/geoposition/search?apikey=GJkfyAOsEvEM9BrJLJejV0XZJZitcAXw&q=59.890234%2C30.324573&language=ru-ru&details=true&toplevel=false');
  if (strpos($out, "error:") === 0){
    $answer = $out;
  }else{
    $response = json_decode($out, true);
    //$answer = $response['Key'];
    $obj = (array)$response['SupplementalAdminAreas'];
    
    //$answer = $obj['EnglishName'];
    //$answer .= " ";
    //$answer .= $response['Key'];


    $an_array = array();
    $reflection = new ReflectionClass($response['SupplementalAdminAreas']);
    $properties = $reflection->getProperties();
    foreach ($properties as $property){
      $property->setAccessible(true);
      $an_array[$property->getName()] = $property->getValue($an_object);
      if (!$property->isPublic())
          $property->setAccessible(false);
    }
    $answer = $an_array['EnglishName'];

  }
 
  return $answer;
}

function getWeather() {
  $answer = '';
  $out = cGet('http://dataservice.accuweather.com/currentconditions/v1/580864?apikey=GJkfyAOsEvEM9BrJLJejV0XZJZitcAXw&language=ru-ru&details=true');
  //$out = cGet('http://dataservice.accuweather.com/currentconditions/v1/'.$locationKey['tomchak'].'?apikey='.$accukey.
  //'&language=ru-ru&details=true');
    if (strpos($out, "error:") === 0){
    $answer = $out;
  }else{
    //$answer = strval(strlen($out));
    //$response = json_decode($out, true);

    $response = json_decode(trim($out,"]["),true);
    $answer = $response['LocalObservationDateTime'];
  }

  return $answer;
}


// поиск 
/*
$answer .= "\n";
$answer .= "\n";
*/
/*
$out = cGet('http://dataservice.accuweather.com/currentconditions/v1/'.$locationKey['tomchak'].'?apikey='.$accukey.
  '&language=ru-ru&details=true');
$answer = $out['LocalObservationDateTime'];
$answer .= "\n";
$answer .= 'Сейчас '.$out['Temperature']['Metric']['Value'].' '.
          $out['Temperature']['Metric']['Unit'].', ветер ';
*//*
    $answer = $out['LocalObservationDateTime'];
    $answer .= "\n";
    $answer .= 'Сейчас '.$out['Temperature']['Metric']['Value'].' '.
              $out['Temperature']['Metric']['Unit'].', ветер ';
*/
/*          json_decode($answer['Wind']['Direction']['Localized']).' '.
          $answer['Wind']['Speed']['Metric']['Value'].' '.
          $answer['Wind']['Speed']['Metric']['Unit'].', облачность '.
          $answer['CloudCover'].'%';
          .json_decode($answer['WeatherText']).' '.*/
/*

[{"LocalObservationDateTime":"2016-06-24T14:10:00+03:00","EpochTime":1466766600,"WeatherText":"\u041c\u0430\u043b\u043e\u043e\u0431\u043b\u0430\u0447\u043d\u043e","WeatherIcon":3,"IsDayTime":true,"Temperature":{"Metric":{"Value":22.8,"Unit":"C","UnitType":17},"Imperial":{"Value":73,"Unit":"F","UnitType":18}},"RealFeelTemperature":{"Metric":{"Value":25.1,"Unit":"C","UnitType":17},"Imperial":{"Value":77,"Unit":"F","UnitType":18}},"RealFeelTemperatureShade":{"Metric":{"Value":21.2,"Unit":"C","UnitType":17},"Imperial":{"Value":70,"Unit":"F","UnitType":18}},"RelativeHumidity":56,"DewPoint":{"Metric":{"Value":13.9,"Unit":"C","UnitType":17},"Imperial":{"Value":57,"Unit":"F","UnitType":18}},"Wind":{"Direction":{"Degrees":293,"Localized":"\u0417\u0421\u0417","English":"WNW"},"Speed":{"Metric":{"Value":13,"Unit":"km\/h","UnitType":7},"Imperial":{"Value":8.1,"Unit":"mi\/h","UnitType":9}}},"WindGust":{"Speed":{"Metric":{"Value":13,"Unit":"km\/h","UnitType":7},"Imperial":{"Value":8.1,"Unit":"mi\/h","UnitType":9}}},"UVIndex":4,"UVIndexText":"\u0423\u043c\u0435\u0440\u0435\u043d.","Visibility":{"Metric":{"Value":16.1,"Unit":"km","UnitType":6},"Imperial":{"Value":10,"Unit":"mi","UnitType":2}},"ObstructionsToVisibility":"RW-","CloudCover":35,"Ceiling":{"Metric":{"Value":8352,"Unit":"m","UnitType":5},"Imperial":{"Value":27400,"Unit":"ft","UnitType":0}},"Pressure":{"Metric":{"Value":1020,"Unit":"mb","UnitType":14},"Imperial":{"Value":30.12,"Unit":"inHg","UnitType":12}},"PressureTendency":{"LocalizedText":"\u041f\u043e\u0441\u0442\u043e\u044f\u043d\u043d\u043e\u0435","Code":"S"},"Past24HourTemperatureDeparture":{"Metric":{"Value":2.8,"Unit":"C","UnitType":17},"Imperial":{"Value":5,"Unit":"F","UnitType":18}},"ApparentTemperature":{"Metric":{"Value":22.8,"Unit":"C","UnitType":17},"Imperial":{"Value":73,"Unit":"F","UnitType":18}},"WindChillTemperature":{"Metric":{"Value":22.8,"Unit":"C","UnitType":17},"Imperial":{"Value":73,"Unit":"F","UnitType":18}},"WetBulbTemperature":{"Metric":{"Value":17.1,"Unit":"C","UnitType":17},"Imperial":{"Value":63,"Unit":"F","UnitType":18}},"Precip1hr":{"Metric":{"Value":0,"Unit":"mm","UnitType":3},"Imperial":{"Value":0,"Unit":"in","UnitType":1}},"PrecipitationSummary":{"Precipitation":{"Metric":{"Value":0,"Unit":"mm","UnitType":3},"Imperial":{"Value":0,"Unit":"in","UnitType":1}},"PastHour":{"Metric":{"Value":0,"Unit":"mm","UnitType":3},"Imperial":{"Value":0,"Unit":"in","UnitType":1}},"Past3Hours":{"Metric":{"Value":0,"Unit":"mm","UnitType":3},"Imperial":{"Value":0,"Unit":"in","UnitType":1}},"Past6Hours":{"Metric":{"Value":0,"Unit":"mm","UnitType":3},"Imperial":{"Value":0,"Unit":"in","UnitType":1}},"Past9Hours":{"Metric":{"Value":0,"Unit":"mm","UnitType":3},"Imperial":{"Value":0,"Unit":"in","UnitType":1}},"Past12Hours":{"Metric":{"Value":1,"Unit":"mm","UnitType":3},"Imperial":{"Value":0.03,"Unit":"in","UnitType":1}},"Past18Hours":{"Metric":{"Value":2,"Unit":"mm","UnitType":3},"Imperial":{"Value":0.07,"Unit":"in","UnitType":1}},"Past24Hours":{"Metric":{"Value":2,"Unit":"mm","UnitType":3},"Imperial":{"Value":0.07,"Unit":"in","UnitType":1}}},"TemperatureSummary":{"Past6HourRange":{"Minimum":{"Metric":{"Value":18.9,"Unit":"C","UnitType":17},"Imperial":{"Value":66,"Unit":"F","UnitType":18}},"Maximum":{"Metric":{"Value":22.8,"Unit":"C","UnitType":17},"Imperial":{"Value":73,"Unit":"F","UnitType":18}}},"Past12HourRange":{"Minimum":{"Metric":{"Value":17.3,"Unit":"C","UnitType":17},"Imperial":{"Value":63,"Unit":"F","UnitType":18}},"Maximum":{"Metric":{"Value":22.8,"Unit":"C","UnitType":17},"Imperial":{"Value":73,"Unit":"F","UnitType":18}}},"Past24HourRange":{"Minimum":{"Metric":{"Value":15.4,"Unit":"C","UnitType":17},"Imperial":{"Value":60,"Unit":"F","UnitType":18}},"Maximum":{"Metric":{"Value":22.8,"Unit":"C","UnitType":17},"Imperial":{"Value":73,"Unit":"F","UnitType":18}}}},"MobileLink":"http:\/\/m.accuweather.com\/ru\/ru\/moskovskaya-zastava\/580864\/current-weather\/580864?lang=ru-ru","Link":"http:\/\/www.accuweather.com\/ru\/ru\/moskovskaya-zastava\/580864\/current-weather\/580864?lang=ru-ru"}]

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

?>
