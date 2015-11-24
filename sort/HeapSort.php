<?php
function heapSort(&$list, $indexLow, $indexHigh) {
  for ($i = floor(($indexHigh - 1) / 2); $i >= $indexLow; $i --) {
    maxHeapify($list, $i, $indexHigh);
  }

  for ($i = $indexHigh; $i >= $indexLow + 1; $i --) {
    $temp = $list[$i];
    $list[$i] = $list[$indexLow];
    $list[$indexLow] = $temp;
    maxHeapify($list, 0, $i - 1);
  }
}

function maxHeapify(&$list, $i, $n) {
  $l = $i * 2 + 1;
  $r = $l + 1;
  $largest = $i;
  if ($l <= $n && $list[$l][2] > $list[$largest][2]) {
    $largest = $l;
  }

  if ($r <= $n && $list[$r][2] > $list[$largest][2]) {
    $largest = $r;
  }

  if ($largest != $i) {
    $temp = $list[$i];
    $list[$i] = $list[$largest];
    $list[$largest] = $temp;
    maxHeapify($list, $largest, $n);
  }
}