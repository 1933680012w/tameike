<?php

require '../php/fn_connect.php';
require '../php/fn_SQL.php';

$placeId = 1;
$placeName = '';
$diff = 0;

if (isset($_GET['id'])) {
    $placeId = $_GET['id'];
}

$tameikeName = "";
$cameraLastGetDate = db_selectCameraLastGetDate($placeId);
$allPlaces = db_selectAllPlaces();

foreach ($allPlaces as $place) {
    if ($place["id"] == $placeId) {
        $placeName = $place["viewName"];
        $diff = (int)$place["diff"];
    }
}

function max_no_get($no)
{
    $no = ceil($no);
    $abs = abs($no);
    $keta = 0;
    $multi = 1;

    while (1 <= $abs) {
        $abs /= 10;
        $keta++;
    }

    for ($i = 1; $i <= $keta - 1; $i++) {
        $multi = $multi * 10;
    }

    return ceil($no / $multi) * ($multi);
}

$waterLevelValMin = 0;
$waterLevelValMax = 0;

$graphWaterLevelMax = 0;
$graphWaterLevelMin = 0;
$graphWaterLevelStep = 5;

$precipitation_max = 30;
$precipitation_step = 1;

$forecastArr = array();
$forecastViewArr = array();
$recentryWaterLevel = array();
$graphDataArr = array();
$loop_count = 0;

$graphStartDate = new DateTime();
$graphStartDate = $graphStartDate->modify('-23 hour');
$graphStartDate = new DateTime($graphStartDate->format('Y-m-d H:0:0'));
$graphStartDate->setTimeZone(new DateTimeZone('Asia/Tokyo'));

$recentryWLStartDate = new DateTime();
$recentryWLStartDate = $recentryWLStartDate->modify('-3 hour');
$recentryWLStartDate = new DateTime($recentryWLStartDate->format('Y-m-d H:0:0'));
$recentryWLStartDate->setTimeZone(new DateTimeZone('Asia/Tokyo'));

$recentryWaterLevel = db_selectRecentryWaterLevel($recentryWLStartDate->format('Y-m-d H:i:s'), $placeId);

$water_level_data = db_selectWaterLevel($graphStartDate->format('Y-m-d H:i:s'), $placeId);
$water_level_lastData = round(end($water_level_data)['waterLevel'], 2);
$pre_1 = round(end($water_level_data)['pre_1'], 2);
$pre_2 = round(end($water_level_data)['pre_2'], 2);
$pre_3 = round(end($water_level_data)['pre_3'], 2);
$pre_4 = round(end($water_level_data)['pre_4'], 2);
$pre_5 = round(end($water_level_data)['pre_5'], 2);
$pre_6 = round(end($water_level_data)['pre_6'], 2);
$pre_7 = round(end($water_level_data)['pre_7'], 2);
$pre_8 = round(end($water_level_data)['pre_8'], 2);
$pre_9 = round(end($water_level_data)['pre_9'], 2);
$pre_10 = round(end($water_level_data)['pre_10'], 2);
$pre_11 = round(end($water_level_data)['pre_11'], 2);
$pre_12 = round(end($water_level_data)['pre_12'], 2);
$pre_13 = round(end($water_level_data)['pre_13'], 2);
$pre_14 = round(end($water_level_data)['pre_14'], 2);
$pre_15 = round(end($water_level_data)['pre_15'], 2);
$pre_16 = round(end($water_level_data)['pre_16'], 2);
$pre_17 = round(end($water_level_data)['pre_17'], 2);
$pre_18 = round(end($water_level_data)['pre_18'], 2);
$pre_19 = round(end($water_level_data)['pre_19'], 2);
$pre_20 = round(end($water_level_data)['pre_20'], 2);
$pre_21 = round(end($water_level_data)['pre_21'], 2);
$pre_22 = round(end($water_level_data)['pre_22'], 2);
$pre_23 = round(end($water_level_data)['pre_23'], 2);
$pre_24 = round(end($water_level_data)['pre_24'], 2);

$water_level_prediction = array(
    $pre_1, $pre_2, $pre_3, $pre_4, $pre_5, $pre_6, $pre_7, $pre_8, $pre_9, $pre_10,
    $pre_11, $pre_12, $pre_13, $pre_14, $pre_15, $pre_16, $pre_17, $pre_18, $pre_19, $pre_20,
    $pre_21, $pre_22, $pre_23, $pre_24
);

$forecastArr = db_selectPredictPrecipitations(end($water_level_data)['measurementDate'], $placeId);

$loop_count = 0;

while (true) {
    if ($loop_count > 100) {
        break;
    } else {
        $insertdate = DateTime::createFromFormat('Y/m/d H:i:s', date_format($graphStartDate, 'Y/m/d H:i:s'));
        $insertdate->setTimeZone(new DateTimeZone('Asia/Tokyo'));
        array_push($graphDataArr, array(
            'DateTime' => $insertdate,
            'water_level' => 'null',
            'water_level_future' => 'null',
            'precipitation_past' => 'null',
            'precipitation_future' => 'null'
        ));
    }
    $loop_count++;
    $graphStartDate = $graphStartDate->modify('+1 hour');
}

$count = 0;

foreach ($water_level_data as &$value) {
    $value['waterLevel'] = $value['waterLevel'] + ((int)$diff / 1000);
}
foreach ($water_level_prediction as &$value) {
    if ($value != null) {
        $value = $value + ((int)$diff / 1000);
    }
    else{
        $value = "無";
    }
}

foreach ($graphDataArr as $value) {
    if ($water_level_data[0]['measurementDate'] == $value['DateTime']->format('Y-m-d H:i:s')) {
        break;
    } else {
        $count++;
    }
}

foreach ($water_level_data as $value) {
    foreach ($graphDataArr as &$value2) {
        if ($value['measurementDate'] == $value2['DateTime']->format('Y-m-d H:i:s')) {
            $value2['water_level'] = round($value['waterLevel'], 2);
            $value2['water_level_future'] = 'null';
            $value2['precipitation_past'] = $value['precipitation'];
            $value2['precipitation_future'] = 'null';
            $count++;
            break;
        }
    }
}

foreach ($water_level_prediction as $value) {
    $graphDataArr[$count]['water_level_future'] = $value;
    foreach ($forecastArr as $forecastData) {
        if ($graphDataArr[$count]['DateTime']->format('Y-m-d H:i:s') == $forecastData['measurementDate']) {

            $graphDataArr[$count]['precipitation_future'] = $forecastData['precipitation'];

            array_push($forecastViewArr, array($graphDataArr[$count]['DateTime']->format('m月d日H時'), $forecastData['precipitation']));
            break;
        }
    }
    $count++;
}

if ($precipitation_max > 1) {
    $precipitation_max = max_no_get($precipitation_max);
    $precipitation_step = $precipitation_max / 10;
}

$minMaxSearchLoop = 0;

foreach ($graphDataArr as $value) {
    if ($minMaxSearchLoop == 0) {
        $waterLevelValMax = $value['water_level'];
        $waterLevelValMin = $value['water_level'];
        $minMaxSearchLoop++;
    }

    if ($value['water_level'] != "null") {
        if ($waterLevelValMax < $value['water_level']) {
            $waterLevelValMax = $value['water_level'];
        }
        if ($waterLevelValMin > $value['water_level']) {
            $waterLevelValMin = $value['water_level'];
        }
    }

    if ($value['water_level_future'] != "null") {
        if ($waterLevelValMax < $value['water_level_future']) {
            $waterLevelValMax = $value['water_level_future'];
        }
        if ($waterLevelValMin > $value['water_level_future']) {
            $waterLevelValMin = $value['water_level_future'];
        }
    }
}

$graphWaterLevelMax = round($waterLevelValMax, 2) + 0.5;
$graphWaterLevelMin = round($waterLevelValMin, 2) - 0.5;

$water_level_step = ($graphWaterLevelMax - $graphWaterLevelMin) / 10;

if ($recentryWaterLevel != null) {
    foreach ($recentryWaterLevel as &$val) {
        $val['waterLevel'] = $val['waterLevel'] + ((int)$diff / 1000);;
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
                    <h1 class="page-header"><?php echo ($placeName); ?>水位
                        [N:<?php echo (round(end($water_level_data)['waterLevel'], 2)); ?>m]
                        [1:<?php echo (str_pad(round($water_level_prediction[0], 4), 4, 0, STR_PAD_RIGHT)); ?>m]
                        [2:<?php echo (str_pad(round($water_level_prediction[1], 4), 4, 0, STR_PAD_RIGHT)); ?>m]
                    </h1>
                </div>
            </div>

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
                                        foreach ($graphDataArr as $value) {
                                            if ($loop_count == $count) {
                                                break;
                                            } elseif ($loop_count == 0) {
                                                echo ("'");
                                                echo ($value['DateTime']->format('m/d H時'));
                                                echo ("'");
                                            } else {
                                                echo (',');
                                                echo ("'");
                                                echo ($value['DateTime']->format('m/d H時'));
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
                                                foreach ($graphDataArr as $value) {
                                                    if ($loop_count == $count) {
                                                        break;
                                                    } elseif ($loop_count == 0) {
                                                        echo ("'");
                                                        echo ($value['water_level']);
                                                        echo ("'");
                                                    } else {
                                                        echo (',');
                                                        echo ("'");
                                                        echo ($value['water_level']);
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
                                            type: 'line',
                                            label: '水位予測',
                                            data: [
                                                <?php
                                                $loop_count = 0;
                                                foreach ($graphDataArr as $value) {
                                                    if ($loop_count == $count) {
                                                        break;
                                                    } elseif ($loop_count == 0) {
                                                        echo ("'");
                                                        echo ($value['water_level_future']);
                                                        echo ("'");
                                                    } else {
                                                        echo (',');
                                                        echo ("'");
                                                        echo ($value['water_level_future']);
                                                        echo ("'");
                                                    }
                                                    $loop_count++;
                                                }
                                                ?>

                                            ],
                                            borderColor: "rgba(255,157,28,0.9)",
                                            pointBackgroundColor: "rgba(255,157,28,0.9)",
                                            fill: false,
                                            yAxisID: "y-axis-1",
                                        },
                                        {
                                            type: 'bar',
                                            label: '降水量',
                                            data: [
                                                <?php
                                                $loop_count = 0;
                                                foreach ($graphDataArr as $value) {
                                                    if ($loop_count == $count) {
                                                        break;
                                                    } elseif ($loop_count == 0) {
                                                        echo ("'");
                                                        echo ($value['precipitation_past']);
                                                        echo ("'");
                                                    } else {
                                                        echo (',');
                                                        echo ("'");
                                                        echo ($value['precipitation_past']);
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
                                        {
                                            type: 'bar',
                                            label: '降水量予測',
                                            data: [
                                                <?php
                                                $loop_count = 0;
                                                foreach ($graphDataArr as $value) {
                                                    if ($loop_count == $count) {
                                                        break;
                                                    } elseif ($loop_count == 0) {
                                                        echo ("'");
                                                        echo ($value['precipitation_future']);
                                                        echo ("'");
                                                    } else {
                                                        echo (',');
                                                        echo ("'");
                                                        echo ($value['precipitation_future']);
                                                        echo ("'");
                                                    }
                                                    $loop_count++;
                                                }
                                                ?>
                                            ],
                                            borderColor: "rgba(248, 113, 248, 0.4)",
                                            backgroundColor: "rgba(248, 113, 248, 0.4)",
                                            yAxisID: "y-axis-2",

                                        },
                                    ],
                                };
                            </script>

                            <script>
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
                                                max: <?php echo ($graphWaterLevelMax) ?>,
                                                min: <?php echo ($graphWaterLevelMin) ?>,
                                                stepSize: <?php echo ($water_level_step) ?>,
                                                callback: function(label, index, labels) {
                                                    return parseFloat(label.toString()).toFixed(2).toString() + ' m';
                                                }
                                            },
                                        }, {
                                            id: "y-axis-2",
                                            type: "linear",
                                            position: "right",
                                            ticks: {
                                                max: <?php echo ($precipitation_max) ?>,
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

            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            現在<?php echo ($placeName); ?>画像 [<?php echo ($cameraLastGetDate[0]['measurementDate']); ?>]
                        </div>
                        <div class="panel-body">
                            <img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="max-width: 100%;" src="../img/<?php echo ($placeId); ?>newImage.jpg?<?php echo (date('ymdHis')) ?>" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i> <?php echo ($placeName); ?>直近3時間10分毎水位
                        </div>
                        <div class="panel-body">
                            <div class="container" style="width:100%">
                                <canvas id="canvas2"></canvas>
                            </div>
                            <script>
                            </script>
                            <script>
                                var barChartData2 = {
                                    labels: [
                                        <?php
                                        $loop_count = 0;
                                        foreach ($recentryWaterLevel as $value) {
                                            if ($loop_count == $count) {
                                                break;
                                            } elseif ($loop_count == 0) {
                                                echo ("'");
                                                echo (date_format(date_create($value['measurementDate']), 'H:i'));
                                                echo ("'");
                                            } else {
                                                echo (',');
                                                echo ("'");
                                                echo (date_format(date_create($value['measurementDate']), 'H:i'));
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
                                                foreach ($recentryWaterLevel as $value) {
                                                    if ($loop_count == $count) {
                                                        break;
                                                    } elseif ($loop_count == 0) {
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
                                                foreach ($recentryWaterLevel as $value) {
                                                    if ($loop_count == $count) {
                                                        break;
                                                    } elseif ($loop_count == 0) {
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
                                var complexChartOption2 = {
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
                                                max: <?php echo ($graphWaterLevelMax) ?>,
                                                min: <?php echo ($graphWaterLevelMin) ?>,
                                                stepSize: <?php echo ($water_level_step) ?>,
                                                callback: function(label, index, labels) {
                                                    return parseFloat(label.toString()).toFixed(2).toString() + ' m';
                                                }
                                            },
                                        }, {
                                            id: "y-axis-2",
                                            type: "linear",
                                            position: "right",
                                            ticks: {
                                                max: <?php echo ($precipitation_max) ?>,
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

            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <?php
                            print_r(end($water_level_data)['measurementDate']);
                            ?>
                        </div>
                        <div class="panel-body">
                            <?php
                            echo "現在の水位：";
                            print_r(round(end($water_level_data)['waterLevel'], 2));
                            echo "m";
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            最大値　最小値
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <?php
                                            echo ('<td>');
                                            echo "グラフ中の最大値：";
                                            echo ('</td><td>');
                                            echo ($waterLevelValMax);
                                            echo "m";
                                            echo ('</td></tr>');
                                            echo ('<tr><td>');
                                            echo "グラフ中の最小値：";
                                            echo ('</td><td>');
                                            echo ($waterLevelValMin);
                                            echo "m";
                                            echo ('</td></tr>');
                                            ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            水位予測
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <?php
                                        $count = 1;
                                        foreach ($water_level_prediction as $value) {
                                        ?>
                                            <tr>
                                            <?php
                                            echo ('<td>');
                                            echo ($count);
                                            echo ('時間後の予測水位：</td>');
                                            echo ('<td>');
                                            echo (str_pad(round($value, 4), 4, 0, STR_PAD_RIGHT));
                                            echo "m";
                                            echo ('</td></tr>');
                                            $count++;
                                        }
                                            ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            降水量予測
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <?php
                                        $count = 1;
                                        foreach ($forecastViewArr as $value) {
                                        ?>
                                            <tr>
                                            <?php
                                            echo ('<td>');
                                            echo ($value[0]);
                                            echo ('</td>');
                                            echo ('<td>');
                                            echo ($value[1]);
                                            echo "mm/h";
                                            echo ('</td></tr>');
                                            $count++;
                                        }
                                            ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            ctx = document.getElementById("canvas").getContext("2d");
            ctx.canvas.height = 220;
            window.myBar = new Chart(ctx, {
                type: 'bar',
                data: barChartData,
                options: complexChartOption
            });
        };

        ctx2 = document.getElementById("canvas2").getContext("2d");
        ctx2.canvas.height = 220;
        window.myBar = new Chart(ctx2, {
            type: 'bar',
            data: barChartData2,
            options: complexChartOption2
        });
    </script>

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="../vendor/metisMenu/metisMenu.min.js"></script>
    <script src="../vendor/raphael/raphael.min.js"></script>
    <script src="../vendor/morrisjs/morris.min.js"></script>
    <script src="../dist/js/sb-admin-2.js"></script>

</body>

</html>