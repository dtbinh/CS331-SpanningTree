<?php
require_once "Prims.php";
require_once "Kruskal.php";

// Get file name from command line parameters
if (count($argv) < 2) die("Please specify a test case file!\n");
$fileName = $argv[1];

$caseNum = -1; // -1: all
if (count($argv) >= 3) $caseNum = intval($argv[2]);

$debug = false;
if (count($argv) >= 4) $debug = (intval($argv[3]) > 0);

// decode test cases from file
$testCases = file_get_contents($fileName);
$testCases = json_decode($testCases);

// Compare the results
for ($i = 0; $i < count($testCases); $i ++) {
  if ($caseNum < 0 || ($caseNum >= 0 && $caseNum == $i)) {
    $nodeCount = $testCases[$i]->NodeCount;
    $edgeCount = $testCases[$i]->EdgeCount;
    $graph = &$testCases[$i]->Graph;

    $prims = new Prims();
    $kruskal = new Kruskal();

    echo "Node:$nodeCount Edge:$edgeCount ... ";
    list($primsST, $primsCost) = $prims->createTree($graph);
    list($kruskalST, $kruskalCost) = $kruskal->createTree($graph);

    if (compareResult($primsST, $primsCost, $kruskalST, $kruskalCost)) {
      printf("Passed Cost=(%d, %d)\n", $primsCost, $kruskalCost);
    } else {
      printf("Failed Cost=(%d, %d)!\n", $primsCost, $kruskalCost);
    }
    if ($debug) {
      echo "Prims ST = "; debugST($primsST); echo "\n";
      echo "Kruskal ST = "; debugST($kruskalST); echo "\n";
    }
    $edgeHeap = null;
    $graph = null;
  }
  echo "\n";
  $testCases[$i] = null;
  gc_collect_cycles();
}

function debugST(&$st) {
  if ($st == null) {
    echo "No spanning tree";
    return;
  }
  foreach ($st as list($node1, $node2)) {
    echo "($node1,$node2) ";
  }
}

function compareResult(&$st1, $cost1, &$st2, $cost2) {
  // Check min cost
  if ($cost1 != $cost2) return false;
  if ($st1 == null || $st2 == null) {
    if ($cost1 == $cost2)
      return true;
    else
      return false;
  }
  // Check edge count
  if (count($st1) != count($st2)) return false;

  // compare the spanning tree
  // NOTE: the min spanning tree should be unique, if each edge is assigned a distinct weight
  $allMatch = true;
  foreach ($st1 as $edge1) {
    $match = false;
    foreach ($st2 as $edge2) {
      if ($edge1[0] == $edge2[0] && $edge1[1] == $edge2[1]) {
        $match = true;
        break;
      }
      if ($edge1[0] == $edge2[1] && $edge1[1] == $edge2[0]) {
        $match = true;
        break;
      }
    }
    if (!$match) {
      printf("(%d,%d) not found\t", $edge1[0], $edge1[1]);
      $allMatch = false;
      break;
    }
  }

  return $allMatch;
}