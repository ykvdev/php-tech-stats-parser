<?php

require_once __DIR__ . '/vendor/autoload.php';

$font = __DIR__ . '/Candara.ttf';

$MyData = new pData();
$MyData->setAxisName(0,"Кол-во упоминаний");
$MyData->addPoints(array("Янв","Фев","Март","Апр","Май","Июнь","Июль","Авг","Сент","Окт","Ноя","Дек"),"Labels");
$MyData->setAbscissa("Labels");

$MyData->addPoints(array(2,7,5,18,19,22,25,56,86,48,100,120), "Yii");
$MyData->addPoints(array(25,76,55,108,219,222,325,656,686,948,2100,6120), "Laravel");

/* Create the pChart object */
$myPicture = new pImage(710,260,$MyData);
$myPicture->drawGradientArea(0,0,710,260,DIRECTION_VERTICAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>100));
$myPicture->drawGradientArea(0,0,710,260,DIRECTION_HORIZONTAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>20));

/* Write the chart title */
$myPicture->setFontProperties(array("FontName"=>$font));
$myPicture->drawText(85,30,"PHP frameworks",array("FontSize"=>13,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));

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
$myPicture->drawLegend(580,20,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));

/* Render the picture (choose the best way) */
$myPicture->autoOutput("example.drawSplineChart.simple.png");