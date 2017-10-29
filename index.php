<?
set_time_limit(0);
require ('searcher/AbstractSearcher.php');
require ('searcher/Dijkstra.php');

$fileName = "/input/2.txt";
require ('importer.php');

$searcher = new Dijkstra($graph);

$startTime = time();
$result = $searcher->search(1, 450);
$endTime = time();
echo PHP_EOL;
var_dump(($endTime - $startTime) );
