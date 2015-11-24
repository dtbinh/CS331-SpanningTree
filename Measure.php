<?php
require_once "Prims.php";
require_once "Kruskal.php";

define('ALGO_PRIMS', 1);
define('ALGO_KRUSKAL', 2);
define('MIN_LOOP_COUNT', 5);
define('MIN_TIME_COST', 10);

// Get file name from command line parameters
if (count($argv) < 2) die("Please specify a test case file!\n");
$fileName = $argv[1];

// decode test cases from file
$testCases = file_get_contents($fileName);
$testCases = json_decode($testCases);

// Measure the cases
echo "AvgTime(ms)\tNodeCount\tEdgeCount\tAlgorithm\n";
for ($i = 0; $i < count($testCases); $i ++) {
  $nodeCount = $testCases[$i]->NodeCount;
  $edgeCount = $testCases[$i]->EdgeCount;
  $graph = &$testCases[$i]->Graph;

  list($avgTime, $loopCount) = algoMeasure($graph, ALGO_PRIMS);
  echo "$avgTime\t$nodeCount\t$edgeCount\tPrims\n";

  list($avgTime, $loopCount) = algoMeasure($graph, ALGO_KRUSKAL);
  echo "$avgTime\t$nodeCount\t$edgeCount\tKruskal\n";

  $graph = null;
  $testCases[$i] = null;
  gc_collect_cycles();
  // printf("%d\t%d\n", memory_get_usage()/1024/1024, memory_get_peak_usage()/1024/1024);
}
echo "\n";

// == MAIN END ==

function algoMeasure(&$graph, $algorithm) {
  $algo = null;
  $nodeCont = count($graph);
  switch ($algorithm) {
    case ALGO_PRIMS:
      $algo = new Prims();
      break;
    case ALGO_KRUSKAL:
      $algo = new Kruskal();
      break;
  }

  $loopCount = calculateLoopCount($graph, $algo);
  $timeStart = microTime(true);
  for ($i = 0; $i < $loopCount; $i ++) {
    $algo->createTree($graph);
  }
  $timeStop = microTime(true);
  $avgTime = ($timeStop - $timeStart) * 1000 / $loopCount;

  return array($avgTime, $loopCount);
}

function calculateLoopCount(&$graph, $algo) {
  $loopCount = 1;
  // Calculate a proper loop count
  $timeStart = microTime(true);
  $algo->createTree($graph);
  $timeStop = microTime(true);
  $timeCost = $timeStop - $timeStart;

  if ($timeCost >= 0.002) { // >=5ms
    $loopCount = intval(MIN_TIME_COST / $timeCost);
  } else { // too fast, need re-calculate with more loops
    $loopCount = 2000;
    $timeStart = microTime(true);
    for ($i = 0; $i < $loopCount; $i ++) {
      $algo->createTree($graph);
    }
    $timeStop = microTime(true);
    $timeCost = $timeStop - $timeStart;

    $loopCount = intval(MIN_TIME_COST / $timeCost * $loopCount);
  }

  if ($loopCount < 1) $loopCount = 1;

  return $loopCount;
}