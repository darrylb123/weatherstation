<?php

require __DIR__ . '/vendor/autoload.php';


use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\Exceptions\ConnectingToBrokerFailedException;
use PhpMqtt\Client\Exceptions\DataTransferException;
use PhpMqtt\Client\Exceptions\UnexpectedAcknowledgementException;

$server = "192.168.12.110";
$port = 1883;
$client_id = "weather-data-publisher";
$mqttRoot = "pws/sensors/";

// OVERLY VERBOSE HELPER STUFF
function RoundIt($ee){
  return round($ee, 2);
}
function toKM( $a) {
  return  RoundIt( floatval($a)*1.60934);
}
function toC( $a) {
  return RoundIt(  (floatval($a)-32) * (5/9) );
}
function toMM( $a) {
    return RoundIt( floatval($a)*25.4);
}
  
function toHPA( $a) {
  return RoundIt((floatval($a)*33.8639));
}
// FOUND THIS ON THE NET IN SOME PASTEBIN
function wind_cardinal( $degree ) { 
  switch( $degree ) {
      case ( $degree >= 348.75 && $degree <= 360 ):
          $cardinal = "N";
      break;
      case ( $degree >= 0 && $degree <= 11.249 ):
          $cardinal = "N";
      break;
      case ( $degree >= 11.25 && $degree <= 33.749 ):
          $cardinal = "N NO";
      break;
      case ( $degree >= 33.75 && $degree <= 56.249 ):
          $cardinal = "NO";
      break;
      case ( $degree >= 56.25 && $degree <= 78.749 ):
          $cardinal = "O NO";
      break;
      case ( $degree >= 78.75 && $degree <= 101.249 ):
          $cardinal = "O";
      break;
      case ( $degree >= 101.25 && $degree <= 123.749 ):
          $cardinal = "O ZO";
      break;
      case ( $degree >= 123.75 && $degree <= 146.249 ):
          $cardinal = "ZO";
      break;
      case ( $degree >= 146.25 && $degree <= 168.749 ):
          $cardinal = "N";
      break;
      case ( $degree >= 168.75 && $degree <= 191.249 ):
          $cardinal = "Z";
      break;
      case ( $degree >= 191.25 && $degree <= 213.749 ):
          $cardinal = "Z ZW";
      break;
      case ( $degree >= 213.75 && $degree <= 236.249 ):
          $cardinal = "ZW";
      break;
      case ( $degree >= 236.25 && $degree <= 258.749 ):
          $cardinal = "W ZW";
      break;
      case ( $degree >= 258.75 && $degree <= 281.249 ):
          $cardinal = "W";
      break;
      case ( $degree >= 281.25 && $degree <= 303.749 ):
          $cardinal = "W NW";
      break;
      case ( $degree >= 303.75 && $degree <= 326.249 ):
          $cardinal = "NW";
      break;
      case ( $degree >= 326.25 && $degree <= 348.749 ):
          $cardinal = "N NW";
      break;
      default:
          $cardinal = null;
  }
 return $cardinal;
}

// SHUFF IT TO MQTT
$mqtt = new MqttClient($server, $port, $client_id);
$mqtt->connect();

$mqtt->publish($mqttRoot .'baromin', toHPA($_GET["baromin"]), 0);
$mqtt->publish($mqttRoot .'temp', toC($_GET["tempf"]), 0);
$mqtt->publish($mqttRoot .'dewpt', toC($_GET["dewptf"]), 0);
$mqtt->publish($mqttRoot .'humidity', $_GET["humidity"], 0);
$mqtt->publish($mqttRoot .'windspeedkph', toKM($_GET["windspeedmph"]), 0);
$mqtt->publish($mqttRoot .'windgustkph', toKM($_GET["windgustmph"]), 0);
$mqtt->publish($mqttRoot .'winddir',wind_cardinal( $_GET["winddir"]), 0);
$mqtt->publish($mqttRoot .'rainmm', toMM($_GET["rainin"]), 0);
$mqtt->publish($mqttRoot .'dailyrainmm', toMM($_GET["dailyrainin"]), 0);
$mqtt->publish($mqttRoot .'indoortemp', toC($_GET["indoortempf"]), 0);
$mqtt->publish($mqttRoot .'indoorhumidity', $_GET["indoorhumidity"], 0);

$mqtt->disconnect();

// POST TO WU .. OPTIONAL, I SHOULD JUST NOT BECAUSE THEY PULLED A FREE SERVICE AND STILL LEECH DATA OF OF MY INVESTMENT WITHOUT ANY REAL RETURN.
$xml = file_get_contents("http://rtupdate.wunderground.com/weatherstation/updateweatherstation.php?".$_SERVER['QUERY_STRING']);

?>
success
