<?
set_time_limit(0);

$db = new PDO('mysql:host=localhost;dbname=transport', 'root', '');

function insert($db, $count, $numPoints) {
    for ($i = 0; $i < $count; $i++) {
        $a = rand(0, $numPoints);
        $b = rand(0, $numPoints);
        $idRoute = $a . '-'. $b;
        $fromTime = rand(1, 40);
        $toTime = rand(1, 40) + $fromTime;

        $text = sprintf("INSERT INTO `transport`.`routes` (`idRoute`, `from`, `to`, `fromTime`, `toTime`) VALUES ('%s', '%s', '%s', '%s', '%s');", $idRoute, $a, $b, $fromTime, $toTime);
        $db->exec($text);
    }
}

function getQuery($numSteps, $from = false, $to = false) {
    $names = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j'];
    $needFilter = $from !== false;
    $sql = 'SELECT ';
    $firstName = $names[0];
    $secondName = $names[1];
    $lastName = $names[$numSteps - 1];
    for ($i = 0; $i < $numSteps; $i++) {
        $curName = $names[$i];
        $sql.= "$curName.id, $curName.idRoute, ";
    }
    $sql .= "$lastName.toTime FROM ";

    $join = "routes $firstName INNER JOIN routes $secondName ON $firstName.to = $secondName.from";
    for ($i = 1; $i < $numSteps - 1; $i++) {
        $curName = $names[$i];
        $nextName = $names[$i + 1];
        $join = '(' . $join . ") INNER JOIN routes $nextName ON $curName.to = $nextName.from";
    }
    $sql .= $join;
    if ($needFilter) {
        $where = " WHERE $firstName.from='$from' AND $lastName.to='$to' ";
    } else {
        $where = " WHERE 1=1 ";
    }
    for ($i = 0; $i < $numSteps - 1; $i++) {
        $curName = $names[$i];
        $nextName = $names[$i + 1];
        $where .= " AND $curName.toTime < $nextName.fromTime";
    }
    $sql.= $where;
    $sql .= " ORDER BY $lastName.toTime ASC LIMIT 1";
    return $sql;
}

function test($db, $name, $numSteps, $from = false, $to = false) {
    $sql = getQuery($numSteps, $from, $to);

    $startTime = time();
    $result = $db->query($sql);
    $endTime = time();

    return $endTime - $startTime;
}

function complexTest($db, $name, $numSteps, $numExperiments = 100) {
    $maxPoints = 500;
    $totalTime = 0;
    for ($i = 0; $i < $numExperiments; $i++) {
        $totalTime += test($db, $name, $numSteps,rand(0, $maxPoints), rand(0, $maxPoints));
    }

    echo 'Testing :' . $name . ' ';
    echo ($totalTime / $numExperiments) . ' sec' . PHP_EOL;
//    echo 'result: ' . var_export($result, true) . PHP_EOL;
}

//insert($db, 2200000, $maxPoints);
complexTest($db, 'Without transfer', 1);
complexTest($db, 'From a to B', 2);
complexTest($db, 'From a to B to c', 3);
complexTest($db, 'From a to B to c to d', 4);
complexTest($db, 'From a to B to c to d to e', 5);
complexTest($db, 'From a to B to c to d to e to f', 6);
complexTest($db, 'From a to B to c to d to e to f to g', 7);


//test($db, 'From a to B', 2, 140, rand(0, $maxPoints));
//test($db, 'From a to B to c', 3, rand(0, $maxPoints), rand(0, $maxPoints));
//test($db, 'From a to B to c to d', 4, rand(0, $maxPoints), rand(0, $maxPoints));
//test($db, 'From a to B to c to d to e', 5, rand(0, $maxPoints), rand(0, $maxPoints));
//test($db, 'From a to B to c to d to e to f', 6, rand(0, $maxPoints), rand(0, $maxPoints));
//test($db, 'From a to B to c to d to e to f to g', 7, rand(0, $maxPoints), rand(0, $maxPoints));
