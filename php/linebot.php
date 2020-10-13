<?php

require './fn_connect.php';
require './fn_SQL.php';

if (isset($_POST['token']) && isset($_POST['lineBotMode'])) {

    $settingDatas = db_selectSettingDatas();
    $settingDatas = $settingDatas[0];

    $pToken = $_POST['token'];
    $postMsgData = $_POST['msg'];
    $fileLink = $_POST['fileLink'];

    if ($pToken == $settingDatas["token"] && $_POST['lineBotMode'] == '1') {

        $access_token = $settingDatas["accesstoken"];

        $url = 'https://api.line.me/v2/bot/message/push';

        $raw = file_get_contents('php://input');
        $receive = json_decode($raw, true);
        $event = $receive['events'][0];
        $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);

        $imgMessage = array('type' => 'image', 'originalContentUrl' => $fileLink, 'previewImageUrl' => $fileLink);
        $message = array('type' => 'text', 'text' => $postMsgData);
        $body = json_encode(array(
            'to' => $settingDatas["channel"],
            'messages' => array($imgMessage, $message)
        ));

        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $body
        );

        $curl = curl_init();
        curl_setopt_array($curl, $options);
        curl_exec($curl);
        curl_close($curl);
    }
} else {
    header("charset=UTF-8");
    $file = './linebotGID.txt';
    $inputData = file_get_contents('php://input');
    $jsonObj = json_decode($inputData, true);
    $GID = "";

    foreach ($jsonObj as $val) {
        foreach ($val[0] as $val2) {
            if (array_key_exists('groupId', $val2)) {
                $GID = $val2['groupId'];
                break;
            }
        }
    }
    file_put_contents($file, $GID);
}
