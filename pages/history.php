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
                    <h1 class="page-header">履歴 時刻[<?php echo date("Y/m/d H:i:s"); ?></h1>
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
                                <input type="text" class="form-control" placeholder="日付を選択" id="picker" name="date">
                                <br />
                                <input class="btn btn-primary" type="submit" value="グラフ表示">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <?php if (!isset($_POST['date'])) {
            } else { ?>
                <div class="row">
                    <div class="col-lg-12">
                        <?php
                        $placeId = 1;
                        $placeName = '';
                        $graphDataArr = array();
                        $searchDate = $_POST['date'];
                        $searchPlaceId = $_POST['place'];
                        $water_level_step = 5;
                        $precipitation_max = 1;
                        $precipitation_step = 0.1;
                        $water_temperature_step = 5;

                        foreach($allPlaces as $place){
                            if($place["id"] == $searchPlaceId){
                                $placeName = $place["viewName"];
                            }
                        }
                        
                        $history = array($searchDate, $searchPlaceId);

                        $water_level_data = db_selectHistoryWaterLevel($history);
                        foreach ($water_level_data as $value) {
                            if ($precipitation_max < $value['precipitation']) {
                                $precipitation_max = $value['precipitation'];
                            }
                        }
                        ?>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <i class="fa fa-bar-chart-o fa-fw"></i> <?php echo ($placeName); ?>水位
                                    </div>
                                    <div class="panel-body">
                                        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.3.0/Chart.bundle.min.js"></script>
                                        <div class="container" style="width:100%">
                                            <canvas id="canvas"></canvas>
                                        </div>
                                        <script>
                                            var barChartData = {
                                                labels: [
                                                    <?php
                                                    $loop_count = 0;

                                                    foreach ($water_level_data as $value) {
                                                        if ($loop_count == 0) {
                                                            echo ("'");
                                                            echo (date("m/d H時", strtotime($value['measurementDate'])));
                                                            echo ("'");
                                                        } else {
                                                            echo (',');
                                                            echo ("'");
                                                            echo (date("m/d H時", strtotime($value['measurementDate'])));
                                                            echo ("'");
                                                        }
                                                        $loop_count++;
                                                    }
                                                    ?>
                                                ],
                                                datasets: [{
                                                        type: 'line',
                                                        label: '水位',
                                                        data: [
                                                            <?php
                                                            $loop_count = 0;
                                                            foreach ($water_level_data as $value) {
                                                                if ($loop_count == 0) {
                                                                    echo ("'");
                                                                    echo ($value['waterLevel']);
                                                                    echo ("'");
                                                                } else {
                                                                    echo (',');
                                                                    echo ("'");
                                                                    echo ($value['waterLevel']);
                                                                    echo ("'");
                                                                }
                                                                $loop_count++;
                                                            }
                                                            ?>
                                                        ],
                                                        borderColor: "rgba(63, 191, 127, 0.6)",
                                                        pointBackgroundColor: "rgba(63, 191, 127, 0.6)",
                                                        fill: false,
                                                        yAxisID: "y-axis-1",
                                                    },
                                                    {
                                                        type: 'bar',
                                                        label: '降水量',
                                                        data: [
                                                            <?php
                                                            $loop_count = 0;
                                                            foreach ($water_level_data as $value) {
                                                                if ($loop_count == 0) {
                                                                    echo ("'");
                                                                    echo ($value['precipitation']);
                                                                    echo ("'");
                                                                } else {
                                                                    echo (',');
                                                                    echo ("'");
                                                                    echo ($value['precipitation']);
                                                                    echo ("'");
                                                                }
                                                                $loop_count++;
                                                            }
                                                            ?>
                                                        ],
                                                        borderColor: "rgba(54,164,235,0.8)",
                                                        backgroundColor: "rgba(54,164,235,0.5)",
                                                        yAxisID: "y-axis-2",
                                                    },
                                                ],
                                            };
                                        </script>

                                        <script>
                                            <?php
                                            $water_level_max = 0;
                                            $water_level_min = 30;

                                            foreach ($water_level_data as $value) {

                                                if ($water_level_max < $value['waterLevel']) {
                                                    $water_level_max = $value['waterLevel'];
                                                }

                                                if ($water_level_min > $value['waterLevel']) {
                                                    $water_level_min = $value['waterLevel'];
                                                }
                                            }
                                            $water_level_max = round($water_level_max, 4);
                                            $water_level_min = round($water_level_min, 4);
                                            $graph_water_level_max = $water_level_max + 0.1;
                                            $graph_water_level_min = $water_level_min - 0.1;
                                            $graph_precipitation_max = $precipitation_max + 2.0;
                                            $water_level_step = $graph_water_level_max / 10;
                                            $precipitation_step = $graph_precipitation_max / 10;
                                            ?>
                                            var complexChartOption = {
                                                tooltips: {
                                                    callbacks: {
                                                        label: function(tooltipItem, data) {
                                                            if (tooltipItem.datasetIndex == 0 || tooltipItem.datasetIndex == 1) {
                                                                return (data.datasets[tooltipItem.datasetIndex].label) + ": " + tooltipItem.yLabel + " m";
                                                            } else {
                                                                return (data.datasets[tooltipItem.datasetIndex].label) + ": " + tooltipItem.yLabel + " mm/h";
                                                            }
                                                        }
                                                    }
                                                },
                                                responsive: true,
                                                scales: {
                                                    yAxes: [{
                                                        id: "y-axis-1",
                                                        type: "linear",
                                                        position: "left",
                                                        ticks: {
                                                            max: <?php echo ($graph_water_level_max) ?>,
                                                            min: <?php echo ($graph_water_level_min) ?>,
                                                            stepSize: <?php echo ($water_level_step) ?>,
                                                            callback: function(label, index, labels) {
                                                                return label.toString() + ' m';
                                                            }
                                                        },
                                                    }, {
                                                        id: "y-axis-2",
                                                        type: "linear",
                                                        position: "right",
                                                        ticks: {
                                                            max: <?php echo ($graph_precipitation_max) ?>,
                                                            min: 0,
                                                            stepSize: <?php echo ($precipitation_step) ?>,
                                                            callback: function(label, index, labels) {
                                                                return parseFloat(label.toString()).toFixed(2).toString() + ' mm/h';
                                                            }
                                                        },
                                                        gridLines: {
                                                            drawOnChartArea: false,
                                                        },
                                                    }],
                                                }
                                            };
                                        </script>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php

                        $water_temperature_max = 0;
                        $water_temperature_min = 50;

                        foreach ($water_level_data as $value) {
                            if ($water_temperature_max < $value['waterTemperature']) {
                                $water_temperature_max = $value['waterTemperature'];
                            }
                            if ($water_temperature_min > $value['waterTemperature']) {
                                $water_temperature_min = $value['waterTemperature'];
                            }
                        }

                        $water_temperature_max = round($water_temperature_max, 4);
                        $water_temperature_min = round($water_temperature_min, 4);
                        $water_temperature_max = $water_temperature_max + 1;
                        $water_temperature_min = $water_temperature_min - 1;
                        $water_temperature_step = $water_temperature_max / 10;

                        ?>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <i class="fa fa-bar-chart-o fa-fw"></i> <?php echo ($placeName); ?>水温
                                    </div>
                                    <div class="panel-body">
                                        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.3.0/Chart.bundle.min.js"></script>

                                        <div class="container" style="width:100%">
                                            <canvas id="canvas1"></canvas>
                                        </div>

                                        <script>
                                            var barChartData1 = {
                                                labels: [
                                                    <?php
                                                    $loop_count = 0;

                                                    foreach ($water_level_data as $value) {
                                                        if ($loop_count == 0) {
                                                            echo ("'");
                                                            echo (date("m/d H時", strtotime($value['measurementDate'])));
                                                            echo ("'");
                                                        } else {
                                                            echo (',');
                                                            echo ("'");
                                                            echo (date("m/d H時", strtotime($value['measurementDate'])));
                                                            echo ("'");
                                                        }
                                                        $loop_count++;
                                                    }
                                                    ?>
                                                ],
                                                datasets: [{
                                                    type: 'line',
                                                    label: '水温',
                                                    data: [
                                                        <?php
                                                        $loop_count = 0;
                                                        foreach ($water_level_data as $value) {
                                                            if ($loop_count == 0) {
                                                                echo ("'");
                                                                echo ($value['waterTemperature']);
                                                                echo ("'");
                                                            } else {
                                                                echo (',');
                                                                echo ("'");
                                                                echo ($value['waterTemperature']);
                                                                echo ("'");
                                                            }
                                                            $loop_count++;
                                                        }
                                                        ?>
                                                    ],
                                                    borderColor: "rgba(54,164,235,0.8)",
                                                    backgroundColor: "rgba(54,164,235,0.5)",
                                                    fill: false,
                                                    yAxisID: "y-axis-1",
                                                }, ],
                                            };
                                        </script>

                                        <script>
                                            var complexChartOptionTemperature = {
                                                tooltips: {
                                                    callbacks: {
                                                        label: function(tooltipItem, data) {
                                                            if (tooltipItem.datasetIndex == 0 || tooltipItem.datasetIndex == 1) {
                                                                return (data.datasets[tooltipItem.datasetIndex].label) + ": " + tooltipItem.yLabel + " 度";
                                                            } else {
                                                                return (data.datasets[tooltipItem.datasetIndex].label) + ": " + tooltipItem.yLabel + " 度";
                                                            }
                                                        }
                                                    }
                                                },
                                                responsive: true,
                                                scales: {
                                                    yAxes: [{
                                                        id: "y-axis-1",
                                                        type: "linear",
                                                        position: "left",
                                                        ticks: {
                                                            max: <?php echo ($water_temperature_max) ?>,
                                                            min: <?php echo ($water_temperature_min) ?>,
                                                            stepSize: <?php echo ($water_temperature_step) ?>,
                                                            callback: function(label, index, labels) {
                                                                return label.toString() + ' 度';
                                                            }
                                                        },
                                                        gridLines: {
                                                            drawOnChartArea: false,
                                                        },
                                                    }],
                                                }
                                            };
                                        </script>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <form action="historyCSVDownload.php" method="post" id="form" autocomplete="off">
                                    <div class="form-row">
                                        <div class="form-group col-sm-6">
                                            <input id="date" name="date" value="<?php echo ($searchDate); ?>" type="hidden" />
                                            <input id="place" name="place" value="<?php echo ($searchPlaceId); ?>" type="hidden" />
                                            <input class="btn btn-primary" type="submit" value="CSVダウンロード">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <script>
        window.onload = function() {
            ctx = document.getElementById("canvas").getContext("2d");
            window.myBar = new Chart(ctx, {
                type: 'bar',
                data: barChartData,
                options: complexChartOption
            });

            ctx1 = document.getElementById("canvas1").getContext("2d");
            window.myBar = new Chart(ctx1, {
                type: 'line',
                data: barChartData1,
                options: complexChartOptionTemperature
            });
        };
    </script>

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