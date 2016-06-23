<?php

define('API_URL', 'https://api.telegram.org/bot238737578:AAEXmM6gfeCsxTzttargwhYNe-UbAvDFcJE/');

function apiRequestWebhook($method, $parameters) {
  if (!is_string($method)) {
    error_log("Method name must be a string\n");
    return false;
  }

  if (!$parameters) {
    $parameters = array();
  } else if (!is_array($parameters)) {
    error_log("Parameters must be an array\n");
    return false;
  }

  $parameters["method"] = $method;

  header("Content-Type: application/json");
  echo json_encode($parameters);
  return true;
}

function exec_curl_request($handle) {
  $response = curl_exec($handle);

  if ($response === false) {
    $errno = curl_errno($handle);
    $error = curl_error($handle);
    error_log("Curl returned error $errno: $error\n");
    curl_close($handle);
    return false;
  }

  $http_code = intval(curl_getinfo($handle, CURLINFO_HTTP_CODE));
  curl_close($handle);

  if ($http_code >= 500) {
    // do not wat to DDOS server if something goes wrong
    sleep(10);
    return false;
  } else if ($http_code != 200) {
    $response = json_decode($response, true);
    error_log("Request has failed with error {$response['error_code']}: {$response['description']}\n");
    if ($http_code == 401) {
      throw new Exception('Invalid access token provided');
    }
    return false;
  } else {
    $response = json_decode($response, true);
    if (isset($response['description'])) {
      error_log("Request was successfull: {$response['description']}\n");
    }
    $response = $response['result'];
  }

  return $response;
}

function apiRequest($method, $parameters) {
  if (!is_string($method)) {
    error_log("Method name must be a string\n");
    return false;
  }

  if (!$parameters) {
    $parameters = array();
  } else if (!is_array($parameters)) {
    error_log("Parameters must be an array\n");
    return false;
  }

  foreach ($parameters as $key => &$val) {
    // encoding to JSON array parameters, for example reply_markup
    if (!is_numeric($val) && !is_string($val)) {
      $val = json_encode($val);
    }
  }
  $url = API_URL.$method.'?'.http_build_query($parameters);

  $handle = curl_init($url);
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($handle, CURLOPT_TIMEOUT, 60);

  return exec_curl_request($handle);
}

function apiRequestJson($method, $parameters) {
  if (!is_string($method)) {
    error_log("Method name must be a string\n");
    return false;
  }

  if (!$parameters) {
    $parameters = array();
  } else if (!is_array($parameters)) {
    error_log("Parameters must be an array\n");
    return false;
  }

  $parameters["method"] = $method;

  $handle = curl_init(API_URL);
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($handle, CURLOPT_TIMEOUT, 60);
  curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($parameters));
  curl_setopt($handle, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

  return exec_curl_request($handle);
}


function sendMessage($chat_id, $message, $quot = NULL) {
  // отправка сообщения с цитатой иль без
  if(is_null($quot)){
    apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => $message));
  }else{
    apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "reply_to_message_id" => $quot, "text" => $message));
  }

}

// смайлики
$emoji = array(
  'preload' => json_decode('"\uD83D\uDE03"'), // Улыбочка.
  'weather' => array(
    'clear' => json_decode('"\u2600"'), // Солнце.
    'clouds' => json_decode('"\u2601"'), // Облака.
    'rain' => json_decode('"\u2614"'), // Дождь.
    'snow' => json_decode('"\u2744"'), // Снег.
  ),
);

function processMessage($message) {
  // process incoming message
  $message_id = $message['message_id'];
  $chat_id = $message['chat']['id'];
  $first_name = $message['chat']['first_name'];
  if (isset($message['text'])) {
    // incoming text message
    $text = $message['text'];
    $answ = "";
    if (strpos($text, "/start") === 0) {
      $answ = 'Приятно познакомиться, '.$first_name;
      sendMessage($chat_id, $answ);
      // TODO move to funk
      apiRequestJson("sendMessage", array('chat_id' => $chat_id,
        "text" => 'Показать что умею?', 'reply_markup' => array(
          'keyboard' => array(array('Здорова, Бот! Ща разберусь', '\help')),
          'one_time_keyboard' => true,
          'resize_keyboard' => true)));
    } else if (strpos($text, "/help") === 0) {
      $answ = "Перечень доступных команд:";
      $answ .= "\n   \\help - эта справка";
      $answ .= "\n   \\temp - вывод метеосводки";
      $answ .= "\n   \\mekod - kod chata & polzovatel'a";
      $answ .= "\n    ";
      sendMessage($chat_id, $answ);
    } else if (strpos($text, "/mekod") === 0) {
      $answ = "chat_id = ";
      $answ .= strval($chat_id);
      $answ .= "\n";
      if (isset($message['from'])){
        $answ .= "from_id = ";
        $answ .= strval($message['from']['id']);
        $answ .= "\n";
      }
      if(isset($message['chat']['username'])){
        $answ .= "username = ";
        $answ .= strval($message['chat']['username']);
        $answ .= "";
      }
      sendMessage($chat_id, $answ);
    } else if (strpos($text, "/temp") === 0) {
      sendMessage($chat_id, "У природы нет плохой погоды (с) ".$emoji['snow']);
    } else if (strpos($text, "/stop") === 0) {
      // stop now
    } else {
      // обробока джанка
      $branch = mt_rand(0,3);
      switch ($branch) {
        case 0:
          $answ = "???";
          break;
        case 1:
          $answ = "выражайся яснее, или скажи /help";
          break;
        case 2:
          $answ = "Похоже на бред. ";
        default:
          $answ .= "Ничего не понял";
          break;
      }
      sendMessage($chat_id, $answ, $message_id);
    }
  // Обработка не текстовых сообщений  
  } else {
    $branch = mt_rand(0,3);
    switch ($branch) {
      case 0:
        $answ = "Это ты мне?";
        break;
      case 1:
        $answ = "А теперь тоже самое, только словами";
        break;
      case 2:
        $answ = "Не-ее, я чат-бот. ";
      default:
        $answ .= "Эти штуки я не понимаю";
        break;
    }
    sendMessage($chat_id,$answ);
  }
}


$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!$update) {
  // receive wrong update, must not happen
  exit;
}

if (isset($update["message"])) {
  processMessage($update["message"]);
}
