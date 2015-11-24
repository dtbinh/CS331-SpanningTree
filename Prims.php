<?php
require_once "SpanningTreeBase.php";

class Prims {
  const ST_INDICATE = -1;

  public function createTree(&$graph) {
    $st = array();
    $nodeCount = count($graph);
    // Construct near array
    $near = array();
    $near[0] = self::ST_INDICATE;
    for ($i = 1; $i < $nodeCount; $i ++) {
      $near[$i] = 0;
    }

    $minCost = 0;
    for ($i = 0; $i < $nodeCount - 1; $i ++) {
      list($v, $cost) = $this->findMinNear($graph, $near);
      if ($v == null) break; // a broken graph
      $minCost += $cost;
      $st[] = array($near[$v], $v);
      $this->updateNear($graph, $near, $v);
    }

    if (count($st) != $nodeCount - 1)
      return array(null, 0);
    else
      return array($st, $minCost);
  }

  private function findMinNear(&$graph, &$near) {
    $minCost = NOT_DIRECT_CONNECT;
    $node = 0;
    foreach ($near as $node1 => $node2) {
      if ($node2 == self::ST_INDICATE) continue; // Already in S.T.
      $cost = $this->getCost($graph, $node1, $node2);
      if ($cost != NOT_DIRECT_CONNECT) {
        if ($minCost == NOT_DIRECT_CONNECT || $cost < $minCost) {
          $minCost = $cost;
          $node = $node1;
        }
      }
    }

    if ($minCost == NOT_DIRECT_CONNECT)
      return array(null, 0); // no path found
    else
      return array($node, $minCost);
  }

  private function updateNear(&$graph, &$near, $v) {
    $near[$v] = self::ST_INDICATE; // add to S.T.
    foreach ($near as $node1 => $node2) {
      if ($node2 == self::ST_INDICATE) continue; // already in S.T.
      $oldCost = $this->getCost($graph, $node1, $node2);
      $newCost = $this->getCost($graph, $node1, $v);
      if ($oldCost == NOT_DIRECT_CONNECT) {
        if ($newCost != NOT_DIRECT_CONNECT) {
          $near[$node1] = $v;
        }
      } else {
        if ($newCost != NOT_DIRECT_CONNECT && $oldCost > $newCost)
          $near[$node1] = $v;
      }
    }
  }

  private function getCost(&$graph, $node1, $node2) {
    return $graph[$node1][$node2];
  }
}
