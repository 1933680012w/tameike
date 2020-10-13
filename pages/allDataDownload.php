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
    <link href="../vendor/morrisjs/morris.css" rel="stylesheet">
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">
    <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css" />
    <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/flick/jquery-ui.css">
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
                    <h1 class="page-header">各データCSVダウンロード</h1>
                </div>
            </div>

            <form action="allDataCSVDownload.php" method="post" id="form" autocomplete="off">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-row">
                            <div class="form-group col-sm-7">
                                <label for="inputReservoir">対象ため池選択</label>
                                <select class="form-control" id="inputReservoir" name="place">
                                    <?php
                                    foreach ($allPlaces as $place) {
                                        echo ("<option value=" . $place["id"] . ">" . $place["viewName"] . "</option>");
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col-sm-7">
                                <label for="selectDownloadData">ダウンロードデータ</label>
                                <select class="form-control" id="selectDownloadData" name="downloadDataKind">
                                    <option value=1>過去データ</option>
                                    <option value=2>予測データ</option>
                                    <option value=3>予測雨量データ</option>
                                </select>
                            </div>

                            <div class="form-group col-sm-7">
                                <label for="inputReservoir">検索開始日付</label>
                                <input type="text" class="form-control" placeholder="日付を選択" id="picker" name="fromDate">
                                <br />
                            </div>

                            <div class="form-group col-sm-7">
                                <label for="inputReservoir">検索終了日付</label>
                                <input type="text" class="form-control" placeholder="日付を選択" id="picker2" name="toDate">
                                <br />
                            </div>

                            <div class="form-group col-sm-7">
                                <input class="btn btn-primary" type="submit" value="csvダウンロード">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
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
    <script type="text/javascript">
        $(function() {
            $("#picker2").datepicker({
                showButtonPanel: true
            });
        });
    </script>
</body>

</html>