<?php

$counties = explode(' ', '澎湖縣 宜蘭縣 花蓮縣 臺東縣 桃園市 屏東縣 嘉義縣 連江縣 金門縣 高雄市 雲林縣 新竹縣 臺中市 臺南市 新竹市 彰化縣 新北市 基隆市 苗栗縣');
$token = '0AAC913D58C6B352';

$output = fopen('php://output', 'w');

fputs($output, '縣市,海岸線,左下X,左下Y,右上X,右上Y' . "\n");

foreach ($counties as $c) {
    $url = 'http://ecolife2.epa.gov.tw/Coastal/_WS/SeaLine.asmx/GetCitySeaLineNames';
    $curl = curl_init($url);
    curl_Setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type:application/json;charset=UTF-8',

    ));
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array(
        'Token' => $token,
        'City' => $c,
    )));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $obj = json_decode(curl_exec($curl));
    foreach ($obj->d->RecList as $rl) {
        $url = 'http://ecolife2.epa.gov.tw/Coastal/_WS/SeaLine.asmx/GetSeaLineBox';
        $curl = curl_init($url);
        curl_Setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json;charset=UTF-8',
        ));
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array(
            'City' => $c,
            'SeaLineName' => $rl->ItemValue,
            'Token' => $token,
        )));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $sea_obj = json_decode(curl_exec($curl));
        $r = $sea_obj->d->Rec;
        fputcsv($output, array(
            $c, trim($rl->ItemValue),
            $r->ldLng, $r->ldLat, $r->rtLng, $r->rtLat,
        ));
    }
}
fclose($output);
