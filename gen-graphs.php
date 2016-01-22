<?php

require_once __DIR__ . '/vendor/autoload.php';

$stats = json_decode(file_get_contents(__DIR__ . '/results/' . date('Y') . '-stats.json'), true);

foreach($stats as $monthNumber => $stat) {

    // Gen bar charts for current month
    if($monthNumber == date('m')) {
        foreach($stat as $category => $techsStats) {
            if($category == 'total_vacancy_number') {
                continue;
            }

            echo "Generate \"{$category}\" bar chart for current month\n";
            genBarGraph($category, $techsStats);
        }
    }
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
    $myPicture->drawGradientArea(0,0,600,500,DIRECTION_VERTICAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>100));
    $myPicture->drawGradientArea(0,0,600,500,DIRECTION_HORIZONTAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>20));
    $myPicture->setFontProperties(array("FontName"=>__DIR__ . '/Candara.ttf',"FontSize"=>10));

    $myPicture->drawText(80,30,$category,array("FontSize"=>13,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));

    /* Draw the chart scale */
    $myPicture->setGraphArea(150,50,580,480);
    $myPicture->drawScale(array("CycleBackground"=>TRUE,"DrawSubTicks"=>false,"GridR"=>0,"GridG"=>0,"GridB"=>0,"GridAlpha"=>10,"Pos"=>SCALE_POS_TOPBOTTOM)); //

    /* Turn on shadow computing */
    $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));

    /* Draw the chart */
    $myPicture->drawBarChart();

    /* Render the picture (choose the best way) */
    $myPicture->autoOutput(__DIR__ . '/results/' . date('Y') . '-curr-' . \Behat\Transliterator\Transliterator::transliterate($category) . '.png');
}