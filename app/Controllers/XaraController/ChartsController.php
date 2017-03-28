<?php

use Khill\Lavacharts\Lavacharts;

$lava = new Lavacharts; // See note below for Laravel

$temps = Lava::DataTable();

$temps->addStringColumn('Type')
      ->addNumberColumn('Value')
      ->addRow(['CPU', rand(0,100)])
      ->addRow(['Case', rand(0,100)])
      ->addRow(['Graphics', rand(0,100)]);

Lava::GaugeChart('Temps', $temps, [
    'width'      => 400,
    'greenFrom'  => 0,
    'greenTo'    => 69,
    'yellowFrom' => 70,
    'yellowTo'   => 89,
    'redFrom'    => 90,
    'redTo'      => 100,
    'majorTicks' => [
        'Safe',
        'Critical'
    ]
]);