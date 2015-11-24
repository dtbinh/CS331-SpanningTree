<?php
require_once "SpanningTreeBase.php";

class GraphGenerator {
  public function generate($nodeCount, $edgeCount) {
    $graph = $this->createGraph($nodeCount);
    $costs = $this->createCosts($edgeCount);

    for ($i = 0; $i < $edgeCount; $i ++) {
      do {
        list($node1, $node2) = $this->getNodes($nodeCount);
      } while ($this->isEdgeExist($graph, $node1, $node2));

      $cost = $costs[$i];
      $this->addEdge($graph, $node1, $node2, $cost);
    }

    return $graph;
  }

  private function getNodes($nodeCount) {
    $node1 = mt_rand(0, $nodeCount - 1);
    do {
      $node2 = mt_rand(0, $nodeCount - 1);
    } while ($node1 == $node2);

    return array($node1, $node2);
  }

  private function isEdgeExist($graph, $node1, $node2) {
    return ($graph[$node1][$node2] != NOT_DIRECT_CONNECT);
  }

  private function addEdge(&$graph, $node1, $node2, $cost) {
    if ($node1 == $node2) return;

    $graph[$node1][$node2] = $cost;
    $graph[$node2][$node1] = $cost;
  }

  private function createGraph($nodeCount) {
    $graph = array();
    for ($i = 0; $i < $nodeCount; $i ++) {
      for ($j = 0; $j < $nodeCount; $j ++) {
        $graph[$i][$j] = ($i == $j)? 0: NOT_DIRECT_CONNECT;
      }
    }

    return $graph;
  }

  private function createCosts($edgeCount) {
    $costs = array();
    for ($i = 0; $i < $edgeCount; $i ++) {
      $costs[] = $i + 1;
    }

    for ($i = 0; $i < $edgeCount - 1; $i ++) {
      $ri = mt_rand($i + 1, $edgeCount - 1);
      $temp = $costs[$i];
      $costs[$i] = $costs[$ri];
      $costs[$ri] = $temp;
    }
    return $costs;
  }
}

// Generate test cases
$generator = new GraphGenerator();
$testCaseList = array();

if (count($argv) < 2) die("Please specify node count!\n");

$nodeCount = intval($argv[1]);
$e = floor($nodeCount * ($nodeCount - 1) / 2);
for ($edgeFactor = 2; $edgeFactor <= 10; $edgeFactor += 2) {
  $edgeCount = floor($edgeFactor * $e / 10);
  $graph = $generator->generate($nodeCount, $edgeCount);
  $testCaseList[] = array(
    "NodeCount" => $nodeCount,
    "EdgeCount" => $edgeCount,
    "Graph" => $graph
  );
}

echo json_encode($testCaseList);

