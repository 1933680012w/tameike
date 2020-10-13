<?php
require '../php/fn_connect.php';
require '../php/fn_SQL.php';

$fileName = "";

$searchPlaceId = $_POST['place'];
$searchDownloadDataKind = $_POST['downloadDataKind'];
$searchFromDate = $_POST['fromDate'];
$searchToDate = $_POST['toDate'];
$dateSearch = false;

$allPlaces = db_selectAllPlaces();

foreach ($allPlaces as $place) {
    if ($place["id"] == $searchPlaceId) {
        $fileName = $place["viewName"];
        break;
    }
}

list($FY, $FM, $FD) = explode('/', $searchFromDate);
list($TY, $TM, $TD) = explode('/', $searchToDate);

if (checkdate($FM, $FD, $FY) === true && checkdate($TM, $TD, $TY) === true) {
    $dateSearch = true;
}

$searchPlaceId = (int)$searchPlaceId;
$searchDownloadDataKind = (int)$searchDownloadDataKind;

switch ($searchDownloadDataKind) {
    case 1:
        $fileName = $fileName . '過去データ';
        break;
    case 2:
        $fileName = $fileName . '予測データ';
        break;
    case 3:
        $fileName = $fileName . '予測雨量';
        break;
}

$csvDownloadData = db_selectAllDataCSVDownload($searchPlaceId, $searchDownloadDataKind, $dateSearch, $searchFromDate, $searchToDate);

header('Content-Disposition: attachment; filename=' . $fileName . '.csv');
header('Content-Type: text/csv');

$fp = fopen('php://output', 'wb');

switch ($searchDownloadDataKind) {
    case 1:
        fputcsv($fp, array('計測時刻', '水位(mm)', '降水量(mm/h)', '水温(℃)'));
        array_push($downloadDataArray, array('measurementDate' => $row['measurementDate'], 'waterLevel' => $row['waterLevel'], 'precipitation' => $row['precipitation'], 'waterTemperature' => $row['waterTemperature']));

        break;
    case 2:
        fputcsv($fp, array(
            '計測時刻', '水位(mm)', '降水量(mm/h)', '水温(℃)',
            '予測水位1', '予測水位2', '予測水位3', '予測水位4', '予測水位5', '予測水位6', '予測水位7', '予測水位8', '予測水位9', '予測水位10', '予測水位11', '予測水位12', '予測水位13', '予測水位14', '予測水位15', '予測水位16', '予測水位17', '予測水位18', '予測水位19', '予測水位20', '予測水位21', '予測水位22', '予測水位23', '予測水位24'
        ));
        break;
    case 3:
        fputcsv($fp, array('予測時刻', '降水量(mm/h)'));
        break;
}

foreach ($csvDownloadData as $row) {
    fputcsv($fp, $row);
}

exit;
