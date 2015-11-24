<?php
function quickSort(&$list, $indexLow, $indexHigh) {
  if ($indexLow < $indexHigh) {
    if (($indexHigh - $indexLow + 1) >= 16) selectPivot($list, $indexLow, $indexHigh);
    $pivotPosition = partition($list, $indexLow, $indexHigh);
    quickSort($list, $indexLow, $pivotPosition - 1);
    quickSort($list, $pivotPosition + 1, $indexHigh);
  }
}

function partition(&$list, $indexLow, $indexHigh) {
  $pivotValue = $list[$indexLow];

  $replacePosition = $indexLow;
  for ($i = $indexLow + 1;$i <= $indexHigh; $i ++) {
    if ($list[$i][2] < $pivotValue[2]) {
//    if ($list[$i] < $pivotValue) {
      swapValue($list, $i, $replacePosition + 1);
      $replacePosition ++;
    }
  }
  swapValue($list, $replacePosition, $indexLow);

  return $replacePosition;
}

function swapValue(&$list, $index1, $index2) {
    $temp = $list[$index1];
    $list[$index1] = $list[$index2];
    $list[$index2] = $temp;
}

function selectPivot(&$list, $indexLow, $indexHigh) {
  $pivotPosition = mt_rand($indexLow, $indexHigh);
  swapValue($list, $indexLow, $pivotPosition);
}
