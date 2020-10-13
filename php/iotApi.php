<?php
require './fn_connect.php';
require './fn_SQL.php';

if (isset($_POST['token'])) {

    $uploadfile = '';

    $settingDatas = db_selectSettingDatas();
    $settingDatas = $settingDatas[0];
    $allPlaces = db_selectAllPlaces();

    $pToken = $_POST['token'];
    $pWL = $_POST['W'];
    $pWT = $_POST['T'];
    $pRF = $_POST['R'];

    $pVAL1 = $_POST['val1'];
    $pVAL2 = $_POST['val2'];
    $pVAL3 = $_POST['val3'];
    $pVAL4 = $_POST['val4'];

    $pPlaceName = $_POST['name'];
    $placeId = "1";
    $diff = "0";
    $nowWL = 0;

    $pMDT;

    foreach ($allPlaces as $place) {
        if ($place["name"] == $pPlaceName) {
            $placeId = $place["id"];
            $diff = (int)$place["diff"];
        }
    }

    if (isset($_POST['MDT'])) {
        $pMDT = $_POST['MDT'];
    } else {
        $pMDT = date('Y-m-d H:i:s');
    }

    if ($pToken == $settingDatas["token"]) {

        $uploadfile = '../img/' . $placeId . 'newImage.jpg';

        if (file_exists($uploadfile)) {
            unlink($uploadfile);
        }

        $result = @move_uploaded_file($_FILES["FILES"]["tmp_name"], $uploadfile);

        header('Content-type: image/jpeg');

        $nowWL = (int)$pWL + $diff;

        if ($nowWL != 0) {
            $nowWL = $nowWL * 0.1;
        }

        $heavyRainFlg = false;
        $hourLaterPrecipitation = '';

        foreach (db_selectPredictPrecipitationHourLater('2') as $val) {
            if ((float) $val["precipitation"] >= 10) {
                $heavyRainFlg = true;
            }
            $hourLaterPrecipitation = $val["precipitation"];
        }

        if ($pWL != 0 && $pWT > -40 && $pWT < 70) {

            db_InsertIotData($pWL, $pWT, $pRF, $pVAL1, $pVAL2, $pVAL3, $pVAL4, $placeId, $pMDT);
        }

        if (((int) date("i") >= 0 && (int) date("i") < 5) || $heavyRainFlg) {
            $historyImgPath = '../img/camera/history/' . $placeId . '/' . (string) date('YmdH0000') . '.jpg';

            mkdir('../img/camera/history/' . $placeId . '/', 0775);
            copy($uploadfile, $historyImgPath);

            if ($settingDatas["lineChannel"] != "lineChannel") {
                if (((int) date("H") > 11 && (int) date("H") < 13) || $heavyRainFlg) {
                    $linebotImgPath = '/img/linebot/' . $placeId . '/' . (string) date('YmdHi00') . '.jpg';

                    mkdir('../img/linebot/' . $placeId . '/', 0775);
                    copy($uploadfile, ".." . $linebotImgPath);

                    $postData = array(
                        "msg" => "現在水位" . (string)$nowWL . "cm\n" . "現在水温" . $pWT . "度\n" . date("H", strtotime("+1 hour")) . "時の降雨予測" . $hourLaterPrecipitation . "mm/h",
                        "lineBotMode" => "1",
                        "token" =>  $settingDatas["token"],
                        "fileLink" => "https://" . $settingDatas["url"] . $linebotImgPath
                    );

                    $postContext = array(
                        'http' => array(
                            'method' => 'POST',
                            'header' => implode("\r\n", array('Content-Type: application/x-www-form-urlencoded')),
                            'content' => http_build_query($postData, "", "&"),
                        ),
                    );

                    $result = file_get_contents("http://" . $settingDatas["url"] . "/php/linebot.php", false, stream_context_create($postContext));
                }
            }
        }
    }
}
