<?php
require_once "SpanningTreeBase.php";
require_once "sort/HeapSort.php";
require_once "sort/QuickSort.php";

class Kruskal {
  private $groups = null;
  private $height;

  public function createTree(&$graph) {
    $nodeCount = count($graph);
    $this->init($nodeCount);
    $st = array(); // Spanning tree
    $minCost = 0; // Minimum cost
    $edgeCount = $nodeCount - 1; // number of edges should be the number of vertices - 1
    $edgeHeap = &$this->createHeap($graph);

    $ei = 0;
    $heapCount = count($edgeHeap);
    while (count($st) < $edgeCount && $ei < $heapCount) {
      list($node1, $node2, $cost) = $edgeHeap[$ei ++];
      $group1 = $this->findGroup($node1);
      $group2 = $this->findGroup($node2);
      if ($group1 != $group2) {
        $st[] = array($node1, $node2);
        $minCost += $cost;
        $this->mergeGroup($group1, $group2);
      }
    }

    if (count($st) != $edgeCount)
      return array(null, 0); // unconnected graph
    else
      return array($st, $minCost);
  }

  protected function init($nodeCount) {
    $this->groups = array();
    $this->height = array();
    for ($i = 0; $i < $nodeCount; $i ++) {
      $this->groups[$i] = $i;
      $this->height[$i] = 1;
    }
  }

  protected function findGroup($node) {
    $i = $node;
    while ($i != $this->groups[$i]) {
      $i = $this->groups[$i];
    }
    return $i;
  }

  protected function mergeGroup($group1, $group2) {
    $height1 = $this->height[$group1];
    $height2 = $this->height[$group2];
    if ($height1 == $height2) {
      $this->groups[$group2] = $group1;
      $this->height[$group1] ++;
    } else {
      if ($height1 > $height2) {
        $this->groups[$group2] = $group1;
      } else {
        $this->groups[$group1] = $group2;
      }
    }
  }

  protected function &createHeap(&$graph) {
    $list = array();
    $nodeCount = count($graph);
    for ($node1 = 1; $node1 < $nodeCount; $node1 ++) {
      for ($node2 = 0; $node2 < $node1; $node2 ++) {
        $cost = $graph[$node1][$node2];
        if ($cost == NOT_DIRECT_CONNECT) continue;
        $list[] = array($node1, $node2, $cost);
      }
    }

    heapSort($list, 0, count($list) - 1);
    return $list;
  }

}