<?php

function json_safe_encode($val)
{
    return json_encode($val, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
}

require '../php/fn_connect.php';
require '../php/fn_SQL.php';

date_default_timezone_set('Asia/Tokyo');

$mapPlaces = array();
$mapPlaces = db_selectMapPlaces();



foreach ($mapPlaces as $value => $val) {
    $placeWaterLevels = db_selectLastWaterLevel($val["id"]);

    if (count($placeWaterLevels) > 0) {
        $placeWaterLevels[0]["waterLevel"] = round($placeWaterLevels[0]["waterLevel"] + ((float)$val["diff"] / 1000), 2);
        if ($placeWaterLevels[0]["pre_1"] != null) {
            $placeWaterLevels[0]["pre_1"] = round($placeWaterLevels[0]["pre_1"] + ((float)$val["diff"] / 1000), 2);
        }
        else
        {
            $placeWaterLevels[0]["pre_1"] = "無";
        }
        if ($placeWaterLevels[0]["pre_2"] != null) {
            $placeWaterLevels[0]["pre_2"] = round($placeWaterLevels[0]["pre_2"] + ((float)$val["diff"] / 1000), 2);
        }
        else{
            $placeWaterLevels[0]["pre_2"] = "無";
        }
        $mapPlaces[$value] += $placeWaterLevels[0];
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>ため池監視システム</title>

    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../vendor/metisMenu/metisMenu.min.css" rel="stylesheet">
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">
    <link href="../vendor/morrisjs/morris.css" rel="stylesheet">
    <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css" />
    <script src="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js"></script>
    <script src="http://code.jquery.com/jquery-latest.js"></script>

</head>

<body>
    <div id="wrapper">
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">ため池監視システム</a>
            </div>
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li>
                            <a href="map.php"><i class="fa fa-dashboard fa-map"></i>地図</a>
                        </li>
                        <li>
                            <a href="history.php"><i class="fa fa-dashboard fa-history"></i>履歴</a>
                        </li>
                        <li>
                            <a href="imgHistory.php"><i class="fa fa-photo"></i>画像履歴</a>
                        </li>
                        <li>
                            <a href="allDataDownload.php"><i class="fa fa-dashboard fa-cloud-download"></i>データダウンロード</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">map 現在時刻[<?php echo date("Y/m/d H:i:s"); ?>]</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div id="map">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="../vendor/metisMenu/metisMenu.min.js"></script>
    <script src="../vendor/raphael/raphael.min.js"></script>
    <script src="../vendor/morrisjs/morris.min.js"></script>
    <script src="../dist/js/sb-admin-2.js"></script>
    <script id="tameikeMap" type="text/javascript" src="../js/mapPlaces.js" data-param='<?php echo json_safe_encode($mapPlaces); ?>'></script>
</body>

</html>