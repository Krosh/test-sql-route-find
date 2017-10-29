<?
set_time_limit(0);
require ('searcher/AbstractSearcher.php');
require ('searcher/Dijkstra.php');

$fileName = "/input/2.txt";
require ('importer.php');

$searcher = new Dijkstra($graph);

$startTime = time();
for ($i = 0; $i < 100; $i++) {
//    $result = $searcher->search(1, 450);
    $result = $searcher->search(rand(1,450), rand(1,450));
}
$endTime = time();
var_dump(($endTime - $startTime)/ 100 );
