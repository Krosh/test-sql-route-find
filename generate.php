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
    $tableName = 'new_table';
    $sql = 'SELECT ';
    $firstName = $names[0];
    $secondName = $names[1];
    $lastName = $names[$numSteps - 1];
    for ($i = 0; $i < $numSteps; $i++) {
        $curName = $names[$i];
        $sql.= "$curName.id, $curName.idRoute, ";
    }
    $sql .= "$lastName.toTime FROM ";

    $join = "$tableName $firstName INNER JOIN $tableName $secondName ON $firstName.to = $secondName.from";
    for ($i = 1; $i < $numSteps - 1; $i++) {
        $curName = $names[$i];
        $nextName = $names[$i + 1];
        $join = '(' . $join . ") INNER JOIN $tableName $nextName ON $curName.to = $nextName.from";
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
echo "START: " . date("H:i:s") . PHP_EOL;
complexTest($db, 'Without transfer', 1);
complexTest($db, 'From a to B', 2);
complexTest($db, 'From a to B to c', 3);
complexTest($db, 'From a to B to c to d', 4);
complexTest($db, 'From a to B to c to d to e', 5);
complexTest($db, 'From a to B to c to d to e to f', 6);
complexTest($db, 'From a to B to c to d to e to f to g', 7);
echo "END: " . date("H:i:s") . PHP_EOL;


//test($db, 'From a to B', 2, 140, rand(0, $maxPoints));
//test($db, 'From a to B to c', 3, rand(0, $maxPoints), rand(0, $maxPoints));
//test($db, 'From a to B to c to d', 4, rand(0, $maxPoints), rand(0, $maxPoints));
//test($db, 'From a to B to c to d to e', 5, rand(0, $maxPoints), rand(0, $maxPoints));
//test($db, 'From a to B to c to d to e to f', 6, rand(0, $maxPoints), rand(0, $maxPoints));
//test($db, 'From a to B to c to d to e to f to g', 7, rand(0, $maxPoints), rand(0, $maxPoints));

/*
Testing :Without transfer 0.02 sec
Testing :From a to B 0.42 sec
Testing :From a to B to c 1.68 sec
Testing :From a to B to c to d 10.32 sec
Testing :From a to B to c to d to e 31.85 sec
Testing :From a to B to c to d to e to f 53.57 sec
Testing :From a to B to c to d to e to f to g 75.62 sec*/

//SET max_heap_table_size = 1024*1024*1024;

/*CREATE TABLE `transport`.`new_table` (
`id` INT NOT NULL AUTO_INCREMENT,
  `idRoute` VARCHAR(45) NULL,
  `from` VARCHAR(45) NULL,
  `to` VARCHAR(45) NULL,
  `fromTime` INT NULL,
  `toTime` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `index_from` (`from` ASC),
  INDEX `index_to` (`to` ASC),
  INDEX `index_from_time` (`fromTime` ASC),
  INDEX `index_to_time` (`toTime` ASC))
ENGINE = MEMORY;*/

//INSERT INTO transport.new_table SELECT * FROM transport.routes;


/*
Testing :Without transfer 0 sec
Testing :From a to B 0 sec
Testing :From a to B to c 0.35 sec
Testing :From a to B to c to d 4.55 sec
Testing :From a to B to c to d to e 7.7 sec
Testing :From a to B to c to d to e to f 16.05 sec
Testing :From a to B to c to d to e to f to g 13.15 sec
 */