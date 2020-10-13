<?php

function db_selectAllPlaces()
{
    $allPlaces = array();
    $Execute_SQL = "";
    $Execute_SQL = "SELECT * FROM places";

    $DB_URL = db_URL();
    $DB_PATH = db_Path();
    $DB_UserID = db_UserID();
    $DB_UserPass = db_UserPass();

    $mysqli = new mysqli($DB_URL, $DB_UserID, $DB_UserPass, $DB_PATH) or die("MySQLへの接続に失敗しました。");

    if ($mysqli->connect_error) {
        echo $mysqli->connect_error;
        exit();
    } else {
        $mysqli->set_charset("utf8");
    }

    $result = $mysqli->query($Execute_SQL) or die("クエリの送信に失敗しました。<br />SQL:" . mysqli_error($mysqli));

    while ($row = mysqli_fetch_assoc($result)) {
        array_push($allPlaces, array('id' => $row['id'], 'name' => $row['name'], 'viewName' => $row['viewName'], 'diff' => $row['diff']));
    }

    mysqli_free_result($result);

    $mysqli->close();
    return ($allPlaces);
}

function db_selectMapPlaces()
{
    $mapPlaces = array();
    $Execute_SQL = "SELECT * FROM places";
    $DB_URL = db_URL();
    $DB_PATH = db_Path();
    $DB_UserID = db_UserID();
    $DB_UserPass = db_UserPass();

    $mysqli = new mysqli($DB_URL, $DB_UserID, $DB_UserPass, $DB_PATH) or die("MySQLへの接続に失敗しました。");

    if ($mysqli->connect_error) {
        echo $mysqli->connect_error;
        exit();
    } else {
        $mysqli->set_charset("utf8");
    }

    $result = $mysqli->query($Execute_SQL) or die("クエリの送信に失敗しました。<br />SQL:" . mysqli_error($mysqli));

    while ($row = mysqli_fetch_assoc($result)) {
        array_push($mapPlaces, array('id' => $row['id'], 'name' => $row['viewName'], 'overflowLevel' => $row['overflowLevel'], 'diff' => $row['diff'], 'lat' => $row['lat'], 'lon' => $row['lon']));
    }

    mysqli_free_result($result);

    $mysqli->close();
    return ($mapPlaces);
}

function db_selectLastWaterLevel($placeId)
{
    $waterLevelData = array();
    $Execute_SQL = '';

    $Execute_SQL = "SELECT * FROM waterLevelPredictions where place_id = " . $placeId . " order by measurementDate desc limit 1";

    $DB_URL = db_URL();
    $DB_PATH = db_Path();
    $DB_UserID = db_UserID();
    $DB_UserPass = db_UserPass();

    $mysqli = new mysqli($DB_URL, $DB_UserID, $DB_UserPass, $DB_PATH) or die("MySQLへの接続に失敗しました。");

    if ($mysqli->connect_error) {
        echo $mysqli->connect_error;
        exit();
    } else {
        $mysqli->set_charset("utf8");
    }

    $result = $mysqli->query($Execute_SQL) or die("クエリの送信に失敗しました。<br />SQL:" . mysqli_error($mysqli));

    while ($row = mysqli_fetch_assoc($result)) {

        $row['waterLevel'] = $row['waterLevel'] / 1000;
        $row['pre_1'] = $row['pre_1'] / 1000;
        $row['pre_2'] = $row['pre_2'] / 1000;

        array_push($waterLevelData, array('measurementDate' => $row['measurementDate'], 'waterLevel' => round($row['waterLevel'], 1), 'pre_1' => round($row['pre_1'], 1), 'pre_2' => round($row['pre_2'], 1)));
    }

    mysqli_free_result($result);

    $mysqli->close();
    return ($waterLevelData);
}

function db_selectCameraLastGetDate($placeId)
{
    $waterLevelData = array();
    $Execute_SQL = "SELECT * FROM `waterLevels` where place_id = " . $placeId . " ORDER BY id DESC LIMIT 1";

    $DB_URL = db_URL();
    $DB_PATH = db_Path();
    $DB_UserID = db_UserID();
    $DB_UserPass = db_UserPass();

    $mysqli = new mysqli($DB_URL, $DB_UserID, $DB_UserPass, $DB_PATH) or die("MySQLへの接続に失敗しました。");

    if ($mysqli->connect_error) {
        echo $mysqli->connect_error;
        exit();
    } else {
        $mysqli->set_charset("utf8");
    }

    $result = $mysqli->query($Execute_SQL) or die("クエリの送信に失敗しました。<br />SQL:" . mysqli_error($mysqli));

    while ($row = mysqli_fetch_assoc($result)) {
        array_push($waterLevelData, array('measurementDate' => $row['measurementDate'], 'waterLevel' => $row['waterLevel'], 'precipitation' => $row['precipitation']));
    }

    mysqli_free_result($result);

    $mysqli->close();
    return ($waterLevelData);
}

function db_selectRecentryWaterLevel($searchDate, $placeId)
{
    $waterLevelData = array();
    $Execute_SQL = "";
    $Execute_SQL = "SELECT * FROM waterLevels where place_id = " . $placeId . " AND measurementDate >= '" . $searchDate . "' order by measurementDate asc";

    $DB_URL = db_URL();
    $DB_PATH = db_Path();
    $DB_UserID = db_UserID();
    $DB_UserPass = db_UserPass();

    $mysqli = new mysqli($DB_URL, $DB_UserID, $DB_UserPass, $DB_PATH) or die("MySQLへの接続に失敗しました。");

    if ($mysqli->connect_error) {
        echo $mysqli->connect_error;
        exit();
    } else {
        $mysqli->set_charset("utf8");
    }

    $result = $mysqli->query($Execute_SQL) or die("クエリの送信に失敗しました。<br />SQL:" . mysqli_error($mysqli));

    while ($row = mysqli_fetch_assoc($result)) {
        $row['waterLevel'] = $row['waterLevel'] / 1000;
        array_push($waterLevelData, array('measurementDate' => $row['measurementDate'], 'waterLevel' => $row['waterLevel'], 'precipitation' => $row['precipitation']));
    }

    mysqli_free_result($result);

    $mysqli->close();
    return ($waterLevelData);
}

function db_selectWaterLevel($searchDate, $placeId)
{
    $waterLevelData = array();
    $Execute_SQL = "";
    $Execute_SQL = "SELECT * FROM waterLevelPredictions where place_id = " . $placeId . " AND measurementDate >= '" . $searchDate . "' order by measurementDate asc";

    $DB_URL = db_URL();
    $DB_PATH = db_Path();
    $DB_UserID = db_UserID();
    $DB_UserPass = db_UserPass();

    $mysqli = new mysqli($DB_URL, $DB_UserID, $DB_UserPass, $DB_PATH) or die("MySQLへの接続に失敗しました。");

    if ($mysqli->connect_error) {
        echo $mysqli->connect_error;
        exit();
    } else {
        $mysqli->set_charset("utf8");
    }

    $result = $mysqli->query($Execute_SQL) or die("クエリの送信に失敗しました。<br />SQL:" . mysqli_error($mysqli));

    while ($row = mysqli_fetch_assoc($result)) {
        $row['waterLevel'] = $row['waterLevel'] / 1000;
        for ($i = 1; $i <= 24; $i++) {
            $rowName = 'pre_' . (string)$i;
            $row[$rowName] = $row[$rowName] / 1000;
        }

        array_push($waterLevelData, array(
            'measurementDate' => $row['measurementDate'], 'waterLevel' => $row['waterLevel'], 'precipitation' => $row['precipitation'],
            'pre_1' => $row['pre_1'], 'pre_2' => $row['pre_2'], 'pre_3' => $row['pre_3'], 'pre_4' => $row['pre_4'], 'pre_5' => $row['pre_5'], 'pre_6' => $row['pre_6'], 'pre_7' => $row['pre_7'], 'pre_8' => $row['pre_8'], 'pre_9' => $row['pre_9'], 'pre_10' => $row['pre_10'],
            'pre_11' => $row['pre_11'], 'pre_12' => $row['pre_12'], 'pre_13' => $row['pre_13'], 'pre_14' => $row['pre_14'], 'pre_15' => $row['pre_15'], 'pre_16' => $row['pre_16'], 'pre_17' => $row['pre_17'], 'pre_18' => $row['pre_18'], 'pre_19' => $row['pre_19'], 'pre_20' => $row['pre_20'],
            'pre_21' => $row['pre_21'], 'pre_22' => $row['pre_22'], 'pre_23' => $row['pre_23'], 'pre_24' => $row['pre_24']
        ));
    }

    mysqli_free_result($result);

    $mysqli->close();
    return ($waterLevelData);
}

function db_selectPredictPrecipitations($searchDate, $placeID)
{
    $waterLevelData = array();
    $Execute_SQL = "SELECT * FROM predictPrecipitations where place_id = " . $placeID . " and predictTime > '" . $searchDate . "' order by predictTime asc";
    $DB_URL = db_URL();
    $DB_PATH = db_Path();
    $DB_UserID = db_UserID();
    $DB_UserPass = db_UserPass();

    $mysqli = new mysqli($DB_URL, $DB_UserID, $DB_UserPass, $DB_PATH) or die("MySQLへの接続に失敗しました。");

    if ($mysqli->connect_error) {
        echo $mysqli->connect_error;
        exit();
    } else {
        $mysqli->set_charset("utf8");
    }

    $result = $mysqli->query($Execute_SQL) or die("クエリの送信に失敗しました。<br />SQL:" . mysqli_error($mysqli));

    while ($row = mysqli_fetch_assoc($result)) {
        array_push($waterLevelData, array('measurementDate' => $row['predictTime'], 'precipitation' => $row['precipitation']));
    }

    mysqli_free_result($result);

    $mysqli->close();
    return ($waterLevelData);
}

function db_selectHistoryWaterLevel($history)
{
    $waterLevelData = array();
    $Execute_SQL = '';
    $Execute_SQL = "SELECT * FROM waterLevels where place_id = " . $history[1] . " AND measurementDate between '" . $history[0] . " 00:00:00' AND '" . $history[0] . " 23:59:59'";

    $DB_URL = db_URL();
    $DB_PATH = db_Path();
    $DB_UserID = db_UserID();
    $DB_UserPass = db_UserPass();

    $mysqli = new mysqli($DB_URL, $DB_UserID, $DB_UserPass, $DB_PATH) or die("MySQLへの接続に失敗しました。");

    if ($mysqli->connect_error) {
        echo $mysqli->connect_error;
        exit();
    } else {
        $mysqli->set_charset("utf8");
    }

    $result = $mysqli->query($Execute_SQL) or die("クエリの送信に失敗しました。<br />SQL:" . mysqli_error($mysqli));

    while ($row = mysqli_fetch_assoc($result)) {
        $row['waterLevel'] = $row['waterLevel'] / 1000;
        array_push($waterLevelData, array('measurementDate' => $row['measurementDate'], 'waterLevel' => round($row['waterLevel'], 2), 'precipitation' => $row['precipitation'], 'waterTemperature' => round($row['waterTemperature'], 2)));
    }

    mysqli_free_result($result);

    $mysqli->close();
    return ($waterLevelData);
}

function db_selectAllDataCSVDownload($searchPlaceId, $searchDownloadData, $dateSearch, $searchFromDate, $searchToDate)
{
    $downloadDataArray = array();

    $Execute_SQL = "";

    $dateSearchSQL = "";

    if ($dateSearch) {
        $dateSearchSQL = " AND measurementDate >= '" . $searchFromDate . " 00:00:00' and measurementDate <= '" . $searchToDate . " 23:59:59'";
    }


    switch ($searchDownloadData) {
        case 1:
            $Execute_SQL = "SELECT * FROM waterLevels where place_id = " . $searchPlaceId .  $dateSearchSQL . " order by measurementDate asc";;
            break;
        case 2:
            $Execute_SQL = "SELECT * FROM waterLevelPredictions where place_id = " . $searchPlaceId .  $dateSearchSQL . " order by measurementDate asc";;
            break;
        case 3:
            if ($dateSearch) {
                $dateSearchSQL = " AND predictTime >= '" . $searchFromDate . " 00:00:00' and predictTime <= '" . $searchToDate . " 23:59:59'";
            }
            $Execute_SQL = "SELECT * FROM predictPrecipitations where place_id = " . $searchPlaceId .  $dateSearchSQL . " order by predictTime asc";;
            break;
    }

    $DB_URL = db_URL();
    $DB_PATH = db_Path();
    $DB_UserID = db_UserID();
    $DB_UserPass = db_UserPass();

    $mysqli = new mysqli($DB_URL, $DB_UserID, $DB_UserPass, $DB_PATH) or die("MySQLへの接続に失敗しました。");

    if ($mysqli->connect_error) {
        echo $mysqli->connect_error;
        exit();
    } else {
        $mysqli->set_charset("utf8");
    }

    $result = $mysqli->query($Execute_SQL) or die("クエリの送信に失敗しました。<br />SQL:" . mysqli_error($mysqli));

    switch ($searchDownloadData) {
        case 1:
            while ($row = mysqli_fetch_assoc($result)) {
                array_push($downloadDataArray, array('measurementDate' => $row['measurementDate'], 'waterLevel' => $row['waterLevel'], 'precipitation' => $row['precipitation'], 'waterTemperature' => $row['waterTemperature']));
            }
            break;
        case 2:
            while ($row = mysqli_fetch_assoc($result)) {
                array_push($downloadDataArray, array(
                    'measurementDate' => $row['measurementDate'], 'waterLevel' => round($row['waterLevel'], 2), 'precipitation' => $row['precipitation'], 'waterTemperature' => round($row['waterTemperature'], 2),
                    'pre_1' => $row['pre_1'], 'pre_2' => $row['pre_2'], 'pre_3' => $row['pre_3'], 'pre_4' => $row['pre_4'], 'pre_5' => $row['pre_5'], 'pre_6' => $row['pre_6'], 'pre_7' => $row['pre_7'], 'pre_8' => $row['pre_8'], 'pre_9' => $row['pre_9'], 'pre_10' => $row['pre_10'],
                    'pre_11' => $row['pre_11'], 'pre_12' => $row['pre_12'], 'pre_13' => $row['pre_13'], 'pre_14' => $row['pre_14'], 'pre_15' => $row['pre_15'], 'pre_16' => $row['pre_16'], 'pre_17' => $row['pre_17'], 'pre_18' => $row['pre_18'], 'pre_19' => $row['pre_19'], 'pre_20' => $row['pre_20'],
                    'pre_21' => $row['pre_21'], 'pre_22' => $row['pre_22'], 'pre_23' => $row['pre_23'], 'pre_24' => $row['pre_24']
                ));
            }
            break;
        case 3:
            while ($row = mysqli_fetch_assoc($result)) {
                array_push($downloadDataArray, array('predictTime' => $row['predictTime'], 'precipitation' => $row['precipitation']));
            }
            break;
    }

    mysqli_free_result($result);

    $mysqli->close();
    return ($downloadDataArray);
}

function db_insertIotData($WL, $WT, $RF, $VAL1, $VAL2, $VAL3, $VAL4, $PlaceID, $MDT)
{
    $Execute_SQL = "INSERT INTO waterLevels (`waterLevel`,`waterTemperature`,`precipitation`,`val1`,`val2`,`val3`,`val4`,`place_id`,`measurementDate`,`created`,`modified`) " .
        "values ('" . $WL . "','" . $WT . "','" . $RF . "','" . $VAL1 . "','" . $VAL2 . "','" . $VAL3 . "','" . $VAL4 . "'," . $PlaceID . ",'" . $MDT . "','" . date('Y-m-d H:i:s') . "','" . date('Y-m-d H:i:s') . "')";

    $DB_URL = db_URL();
    $DB_PATH = db_Path();
    $DB_UserID = db_UserID();
    $DB_UserPass = db_UserPass();

    $mysqli = new mysqli($DB_URL, $DB_UserID, $DB_UserPass, $DB_PATH) or die("MySQLへの接続に失敗しました。");

    if ($mysqli->connect_error) {
        echo $mysqli->connect_error;
        exit();
    } else {
        $mysqli->set_charset("utf8");
    }

    $result = $mysqli->query($Execute_SQL) or die("クエリの送信に失敗しました。<br />SQL:" . mysqli_error($mysqli));

    $mysqli->close();
}

function db_selectPredictPrecipitationHourLater($placeID)
{
    $nowTime = date('Y-m-d H:00:00');
    $hourLaterTime = date("Y-m-d H:00:00", strtotime("+2 hour"));
    $waterLevelData = array();

    $Execute_SQL = "SELECT * FROM predictPrecipitations where place_id = " . $placeID . " and predictTime >= '" . $nowTime . "' and predictTime < '" . $hourLaterTime . "' order by predictTime asc";

    $DB_URL = db_URL();
    $DB_PATH = db_Path();
    $DB_UserID = db_UserID();
    $DB_UserPass = db_UserPass();

    $mysqli = new mysqli($DB_URL, $DB_UserID, $DB_UserPass, $DB_PATH) or die("MySQLへの接続に失敗しました。");

    if ($mysqli->connect_error) {
        echo $mysqli->connect_error;
        exit();
    } else {
        $mysqli->set_charset("utf8");
    }

    $result = $mysqli->query($Execute_SQL) or die("クエリの送信に失敗しました。<br />SQL:" . mysqli_error($mysqli));

    while ($row = mysqli_fetch_assoc($result)) {
        array_push($waterLevelData, array('measurementDate' => $row['predictTime'], 'precipitation' => $row['precipitation']));
    }

    mysqli_free_result($result);

    $mysqli->close();
    return ($waterLevelData);
}

function db_selectSettingDatas()
{
    $settingDatas = array();
    $Execute_SQL = "";
    $Execute_SQL = "SELECT * FROM settings where id = 1 LIMIT 1";

    $DB_URL = db_URL();
    $DB_PATH = db_Path();
    $DB_UserID = db_UserID();
    $DB_UserPass = db_UserPass();

    $mysqli = new mysqli($DB_URL, $DB_UserID, $DB_UserPass, $DB_PATH) or die("MySQLへの接続に失敗しました。");

    if ($mysqli->connect_error) {
        echo $mysqli->connect_error;
        exit();
    } else {
        $mysqli->set_charset("utf8");
    }

    $result = $mysqli->query($Execute_SQL) or die("クエリの送信に失敗しました。<br />SQL:" . mysqli_error($mysqli));

    while ($row = mysqli_fetch_assoc($result)) {
        array_push($settingDatas, array('url' => $row['url'], 'channel' => $row['lineChannel'], 'accesstoken' => $row['lineAccesstoken'], 'token' => $row['token']));
        break;
    }

    mysqli_free_result($result);

    $mysqli->close();
    return ($settingDatas);
}
