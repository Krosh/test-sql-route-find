<?php
/**
 * Created by PhpStorm.
 * User: Павел
 * Date: 12.10.2017
 * Time: 19:45
 */

class Dijkstra extends AbstractSearcher {

    public function index() {
        // TODO: Implement index() method.
    }

    public function search($from, $to) {
        // массив кратчайших путей к каждому узлу
        $distances = array();
        // массив "предшественников" для каждого узла
        $parents = array();
        // очередь всех неоптимизированных узлов
        $queue = new SplPriorityQueue();

        foreach ($this->graph as $v => $fromArray) {
            $distances[$v] = INF; // устанавливаем изначальные расстояния как бесконечность
            $parents[$v] = null; // никаких узлов позади нет
        }

        // начальная дистанция на стартовом узле - 0
        $distances[$from] = 0;
        $queue->insert($from, 0);
//        $wasVisited = [];

        $n = 0;
        while (!$queue->isEmpty()) {
            // извлечем минимальную цену
            $n++;
            $currentNode = $queue->extract();
            if ($currentNode == $to) {
                break;
            }
            if (!empty($this->graph[$currentNode])/* && !isset($wasVisited[$currentNode])*/) {
//                $wasVisited[$currentNode] = true;
                // пройдемся по всем соседним узлам
                foreach ($this->graph[$currentNode] as $v => $cost) {
                    // установим новую длину пути для соседнего узла
                    $currentCost = $distances[$currentNode] + $cost;
                    // если он оказался короче
                    if ($currentCost < $distances[$v]) {
                        $queue->insert($v, -$currentCost);
                        $distances[$v] = $currentCost; // update minimum length to vertex установим как минимальное расстояние до этого узла
                        $parents[$v] = $currentNode;  // добавим соседа как предшествующий этому узла
                    }
                }
            }
        }

        // теперь мы можем найти минимальный путь
        // используя обратный проход
        $path = new SplStack(); // кратчайший путь как стек
        $currentNode = $to;
        $dist = 0;
        // проход от целевого узла до стартового
        while (isset($parents[$currentNode]) && $parents[$currentNode]) {
            $path->push($currentNode);
            $dist += $this->graph[$parents[$currentNode]][$currentNode]; // добавим дистанцию для предшествующих
            $currentNode = $parents[$currentNode];
        }

        // стек будет пустой, если нет пути назад
        if ($path->isEmpty()) {
            echo "Нет пути из $from в $to";
        }
        else {
            // добавим стартовый узел и покажем весь путь 
            // в обратном (LIFO) порядке
            $path->push($from);
            echo "$dist:";
            $sep = '';
            foreach ($path as $v) {
                echo $sep, $v;
                $sep = '->';
            }
            echo PHP_EOL;
        }
    }
}