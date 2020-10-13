<?php

require '../php/fn_connect.php';
require '../php/fn_SQL.php';

date_default_timezone_set('Asia/Tokyo');
$allPlaces = db_selectAllPlaces();

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
    <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/flick/jquery-ui.css">
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
                    <h1 class="page-header">画像履歴 時刻[<?php echo date("Y/m/d H:i:s"); ?></h1>
                </div>
            </div>
            <form action="" method="post" id="form" autocomplete="off">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-row">
                            <div class="form-group col-sm-7">
                                <label for="inputReservoir">ため池</label>
                                <select class="form-control" id="inputReservoir" name="place">
                                    <?php
                                    foreach ($allPlaces as $place) {
                                        echo ("<option value=" . $place["id"] . ">" . $place["viewName"] . "</option>");
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col-sm-7">
                                <label for="inputReservoir">日付</label>
                                <input type="text" class="form-control" placeholder="日付を選択" id="picker" name="date" value = "<?php if (isset($_POST['date'])){echo($_POST['date']);} ?>">
                                <br />
                                <input class="btn btn-primary" type="submit" value="画像検索">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <?php if (!isset($_POST['date'])) {
            } else {
                $placeId = 1;
                $placeName = '';
                $searchPlaceId = $_POST['place'];
                $searchDate = $_POST['date'];
                $viewDate = $_POST['date'];
                $searchDate = str_replace("/", "", $searchDate);
                foreach ($allPlaces as $place) {
                    if ($place["id"] == $searchPlaceId) {
                        $placeName = $place["viewName"];
                    }
                }
            ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <i class="fa fa-photo fa-fw"></i> <?php echo ($placeName); ?>画像
                            </div>

                            <?php
                            for ($i = 0; $i <= 23; $i++) {
                                $hour = sprintf('%02d', $i);
                                $filePath = '../img/camera/history/' . $placeId . '/' . $searchDate . $hour . '0000.jpg';

                                if (file_exists($filePath)) {
                                    echo ('<div class="panel-body">');
                                    echo($viewDate . " " . $hour . "時<br />");
                                    echo ('<img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="max-width: 100%;" src="' . $filePath . '" />');
                                    echo ('</div>');
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>


    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="../vendor/metisMenu/metisMenu.min.js"></script>
    <script src="../vendor/raphael/raphael.min.js"></script>
    <script src="../vendor/morrisjs/morris.min.js"></script>
    <script src="../dist/js/sb-admin-2.js"></script>

    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-ja.min.js"></script>

    <script type="text/javascript">
        $(function() {
            $("#picker").datepicker({
                showButtonPanel: true
            });
        });
    </script>

</body>

</html>