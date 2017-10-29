<?

$names = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j'];
$from = 1;
$to = 23;
$needFilter = false;
$numSteps = 2;
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
echo $sql;

//SELECT a.idRoute, a.from,  b.idRoute, b.from, b.to, b.toTime FROM routes a INNER JOIN routes b ON a.to = b.from WHERE b.fromTime < a.toTime and a.from = '8' AND b.to = '12'
