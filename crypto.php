<?php

 /**
  * @author Anthony Tournier <hello@anthonytournier.fr>
  * Call this file with two arguments : php crypto.php EUR bitcoin
  */

$endpoint = "https://api.coinmarketcap.com/v1/ticker/";
$convertArray = ["AUD", "BRL", "CAD", "CHF", "CLP", "CNY", "CZK", "DKK", "EUR", "GBP", "HKD", "HUF", "IDR", "ILS", "INR", "JPY", "KRW", "MXN", "MYR", "NOK", "NZD", "PHP", "PKR", "PLN", "RUB", "SEK", "SGD", "THB", "TRY", "TWD", "ZAR"];

$convert = "EUR";
if (isset($argv[1]) && in_array($argv[1], $convertArray)) {
    $convert = $argv[1];
}

try {
    $ch = curl_init($endpoint.$argv[2]."/?convert=".$convert);    
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch,CURLOPT_TIMEOUT,5000);
    $data = curl_exec($ch);
    $curl_errno = curl_errno($ch);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($curl_errno > 0) {
        echo '<txt><span foreground="#ff0000">No internet connection</span></txt>';
        die();
    } else {
        $currency = json_decode($data);
    }
    

    $currency = $currency[0];
    $price = $currency->{'price_'.strtolower($convert)};
    if ($price < 10) {
        $round = 5;
    } elseif($price >= 10 && $price < 1000) {
        $round = 4;
    } else {
        $round = 2;
    }

    if ($currency->percent_change_1h >= 0) {
        $variation = '<span foreground="#24ad18">+'.$currency->percent_change_1h.'%</span>';
    } else {
        $variation = '<span foreground="#ff0000">'.$currency->percent_change_1h.'%</span>';
    }

    echo sprintf("<txt><small><b>%s</b> : %s %s (%s)</small></txt>",
        $currency->symbol,
        round($price, $round),
        $convert,
        $variation
    );

    echo sprintf("<tool>Last hour change : %s
Last day change : %s
Last week change : %s
24h volume: %s</tool>",
      $currency->percent_change_1h,
      $currency->percent_change_24h,
      $currency->percent_change_7d,
      $currency->{'24h_volume_'.strtolower($convert)}
    );
} catch (Exception $e) {
    echo '<txt><b>ERROR</b></txt>';
}