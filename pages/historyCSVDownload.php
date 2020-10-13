<?php
require '../php/fn_connect.php';
require '../php/fn_SQL.php';
header('Content-Disposition: attachment; filename=data.csv');
header('Content-Type: text/csv');

$searchDate = $_POST['date'];
$searchPlaceId = $_POST['place'];

$history = array($searchDate, $searchPlaceId);

$water_level_data = db_selectHistoryWaterLevel($history);

$fp = fopen('php://output', 'wb');

fputcsv($fp, array('日付', '水位(m)', '水温(℃)', '降水量(mm/h)'));

foreach ($water_level_data as $value) {
    fputcsv($fp, array($value['measurementDate'], $value['waterLevel'], $value['waterTemperature'], $value['precipitation']));
}

exit;