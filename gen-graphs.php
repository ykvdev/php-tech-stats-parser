<?php

require_once __DIR__ . '/vendor/autoload.php';

$stats = json_decode(file_get_contents(__DIR__ . '/results/' . date('Y') . '-stats.json'), true);

$yearStats = [];
foreach($stats as $monthNumber => $stat) {
    foreach($stat as $category => $techsStats) {
        if($category == 'total_vacancy_number') {
            $yearStats['Кол-во вакансий']['Кол-во вакансий'][$monthNumber] = $techsStats;
            continue;
        }

        foreach($techsStats as $k => $v) {
            $yearStats[$category][$k][$monthNumber] = $v;
        }

        // Gen bar charts for current month
        if($monthNumber == date('m')) {
            echo "Generate \"{$category}\" bar chart for current month\n";
            genBarGraph($category, $techsStats);
        }
    }
}

foreach($yearStats as $category => $techsStats) {
    echo "Generate \"{$category}\" spline chart for curr year\n";
    genSplineGraph($category, $techsStats);
}

function genBarGraph($category, $techsStats) {
    arsort($techsStats);

    /* Create and populate the pData object */
    $MyData = new pData();
    $MyData->setAxisName(0,"Кол-во упоминаний");
    $MyData->addPoints(array_keys($techsStats),"abscissa");
    $MyData->setAbscissa("abscissa");
    $MyData->setAxisDisplay(0,AXIS_FORMAT_METRIC,1);

    $MyData->addPoints(array_values($techsStats));

    /* Create the pChart object */
    $myPicture = new pImage(600,500,$MyData);
//    $myPicture->drawGradientArea(0,0,600,500,DIRECTION_VERTICAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>100));
//    $myPicture->drawGradientArea(0,0,600,500,DIRECTION_HORIZONTAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>20));
    $myPicture->setFontProperties(array("FontName"=>__DIR__ . '/Candara.ttf',"FontSize"=>10));

    $myPicture->drawText(20,30,$category,array("FontSize"=>13,"Align"=>TEXT_ALIGN_BOTTOMLEFT));

    /* Draw the chart scale */
    $myPicture->setGraphArea(150,50,580,480);
    $myPicture->drawScale(array("CycleBackground"=>TRUE,"DrawSubTicks"=>false,"GridR"=>0,"GridG"=>0,"GridB"=>0,"GridAlpha"=>10,"Pos"=>SCALE_POS_TOPBOTTOM,"Mode"=>SCALE_MODE_START0));

    /* Turn on shadow computing */
    $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));

    /* Draw the chart */
    $myPicture->drawBarChart();

    /* Render the picture (choose the best way) */
    $myPicture->autoOutput(__DIR__ . '/results/graphs/' . date('Y') . '-curr-' . \Behat\Transliterator\Transliterator::transliterate($category) . '.png');
}

function genSplineGraph($category, $techsStats) {
    $font = __DIR__ . '/Candara.ttf';

    $MyData = new pData();
    if($category != 'Кол-во вакансий') {
        $MyData->setAxisName(0,"Кол-во упоминаний");
    }
    $MyData->addPoints(array("Янв","Фев","Март","Апр","Май","Июнь","Июль","Авг","Сент","Окт","Ноя","Дек"),"Labels");
    $MyData->setAbscissa("Labels");

    $i = 1;
    foreach($techsStats as $techName => $monthsStats) {
        $MyData->addPoints($monthsStats, $techName);

        if($i == 5) {
            break;
        }

        $i++;
    }

    /* Create the pChart object */
    $myPicture = new pImage(710,260,$MyData);
//    $myPicture->drawGradientArea(0,0,710,260,DIRECTION_VERTICAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>100));
//    $myPicture->drawGradientArea(0,0,710,260,DIRECTION_HORIZONTAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>20));

    /* Write the chart title */
    $myPicture->setFontProperties(array("FontName"=>$font));
    $myPicture->drawText(30,30,$category,array("FontSize"=>13,"Align"=>TEXT_ALIGN_BOTTOMLEFT));

    /* Set the default font */
    $myPicture->setFontProperties(array("FontName"=>$font,"FontSize"=>10));

    /* Define the chart area */
    $myPicture->setGraphArea(75,50,670,220);

    /* Draw the scale */
    $scaleSettings = array("XMargin"=>10,"YMargin"=>10,"Floating"=>TRUE,"GridR"=>200,"GridG"=>200,"GridB"=>200,"DrawSubTicks"=>false,"CycleBackground"=>TRUE,"Mode"=>SCALE_MODE_START0);
    $myPicture->drawScale($scaleSettings);

    /* Draw the line chart */
    $myPicture->drawSplineChart();
    $myPicture->drawPlotChart(array("PlotBorder"=>TRUE,"BorderSize"=>1,"Surrounding"=>-60,"BorderAlpha"=>80));

    /* Write the chart legend */
    if($category != 'Кол-во вакансий') {
        $myPicture->drawLegend(350, 20, array("Style" => LEGEND_NOBORDER, "Mode" => LEGEND_HORIZONTAL, "Align" => TEXT_ALIGN_BOTTOMRIGHT));
    }

    /* Render the picture (choose the best way) */
    $myPicture->autoOutput(__DIR__ . '/results/graphs/' . date('Y') . '-year-' . \Behat\Transliterator\Transliterator::transliterate($category) . '.png');
}