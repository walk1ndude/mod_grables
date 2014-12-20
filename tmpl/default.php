<?php defined('_JEXEC') or die('Restricted access'); // no direct access 

JHtml::script('jquery-2.1.1.min.js', 'modules/mod_grables/js/');

JHtml::script('d3.min.js', 'modules/mod_grables/js/');

JHtml::script('grables.js', 'modules/mod_grables/js/');

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

require_once('modules/mod_grables/Classes/PHPExcel/IOFactory.php');

$objPHPExcel = PHPExcel_IOFactory::load("modules/mod_grables/test.xlsx");

// все данные в первом листе
$worksheet = $objPHPExcel->getActiveSheet();

$columnWithValue = -1;	

$currentCellIndex = [
	0 => 0
];

$children[0] = [];

$lastChildPos = [
	"i" => 0,
	"j" => -1
];

$prevChildPos = [
	"i" => -1,
	"j" => -1	
];

foreach ($worksheet->getRowIterator() as $row) {
	$cellIterator = $row->getCellIterator();
	$cellIterator->setIterateOnlyExistingCells(true);
	
	$i = 0;
	
	$gotNewCell = false;
	
	foreach ($cellIterator as $cell) {
		if (!is_null($cell)) {
			$cellVal = $cell->getCalculatedValue();
			
			if ($columnWithValue < 0 && $cellVal === "Исполнено бюджет субъекта РФ") {
				$columnWithValue = $i;
			}
			
			switch ($i) {
				case 0 :
					// Если в яичейке не значение формата число.число. ... .число - не рассматривать столбец вообще					
					if (!preg_match("/^\d+(\.\d+)*$/m", $cellVal)) {
						continue;
					}
					
					$curCellIndex = $currentCellIndex;
					
					$curCellIndex[$lastChildPos[i]] += 1;
				
					$currentLevel = implode(".", $curCellIndex);
							
					if ($cellVal === $currentLevel) {					
						// Если значение яичейки отличается от последнего числа текущего индекса на 1,
						// просто добавить в последний уровень дочерних элементов
						$gotNewCell = true;
						
						$currentCellIndex[$lastChildPos[i]] += 1; 
						
						$lastChildPos[j] += 1;
						
						$children[$lastChildPos[i]][$lastChildPos[j]] = [];
						
						$lastAdded = &$children[$lastChildPos[i]][$lastChildPos[j]];
					}
					else {
						$nextCellIndex = $currentCellIndex;
						$nextCellIndex[] = 1;
						
						$implCurCellIndex = implode(".", $nextCellIndex);
						
						if ($cellVal === $implCurCellIndex) {
							// Если начинается новый подуровень
							$currentCellIndex[] = 1;
							
							$lastChildPos[i] += 1;
							
							$prevChildPos[i] += 1;
							$prevChildPos[j] = $lastChildPos[j];
							
							$lastChildPos[j] = 0;
							
							$children[$lastChildPos[i]][$lastChildPos[j]] = [];
						
							$lastAdded = &$children[$lastChildPos[i]][$lastChildPos[j]];
						}
						else {
							// Если произошел выход из подуровней, выполнить свертку дочерних элементов
							
							$nextCellIndex = implode(".", $currentCellIndex);
							
							do {
								$nextCellIndex = preg_replace("/\.\d+$/", "", $nextCellIndex);
								
								array_pop($currentCellIndex);
									
								$children[$prevChildPos[i]][$prevChildPos[j]]["children"] = array_pop($children);
								
								$lastChildPos = $prevChildPos;
								
								$prevChildPos[i] = $prevChildPos[i] - 1;
								$prevChildPos[j] = $prevChildPos[i] > -1 ? count($children[$prevChildPos[i]]) - 1 : -1;
								
								$curCellIndex = $currentCellIndex;
								$curCellIndex[$lastChildPos[i]] += 1;
								
								$implCurCellIndex = implode(".", $curCellIndex); 
								
							// Продолжать пока не достигли самого верхнего уровня или
							// не установили соотвествие
							} while ($cellVal !== $implCurCellIndex && $lastChildPos[i] > 0);
							
							$lastChildPos[j] += 1;
							$currentCellIndex[$lastChildPos[i]] += 1;
						}
							
						if ($cellVal === $implCurCellIndex) {
							$gotNewCell = true;
							
							$children[$lastChildPos[i]][$lastChildPos[j]] = [];
							$lastAdded = &$children[$lastChildPos[i]][$lastChildPos[j]];
						}
					}
	
					break;
					
				case 1 :	
					// Заполняем последний добавленный в иерархию элемент значениями из полей
					if ($gotNewCell) { 				
						$lastAdded["name"] = $cellVal;
						$lastAdded["children"] = [];
					}
					break;
					
				case $columnWithValue : 
					// Заполняем последний добавленный в иерархию элемент значение показателя
					if ($gotNewCell) {
						$lastAdded["value"] = floatval($cellVal); 
					}
					break;
			}
		}
		
		$i += 1;
	}
}

// Выполнить свертку для последних элементов
$nextCellIndex = implode(".", $currentCellIndex);

do {
	$nextCellIndex = preg_replace("/\.\d+$/", "", $nextCellIndex);
	
	array_pop($currentCellIndex);
		
	$children[$prevChildPos[i]][$prevChildPos[j]]["children"] = array_pop($children);
	
	$lastChildPos = $prevChildPos;
	
	$prevChildPos[i] = $prevChildPos[i] - 1;
	$prevChildPos[j] = $prevChildPos[i] > -1 ? count($children[$prevChildPos[i]]) - 1 : -1;
} while ($lastChildPos[i] > 0);

$paramsToJS = array(
	"sections" => $children[0],
	"maxElementsInRow" => $params->get("maxElementsInRow", "14"),
	"grablesTime" => str_replace("-", ".", $params->get("grablesTime", "01-12-2014"))
);
?>

<script>
var params = <?php echo json_encode($paramsToJS); ?>; 
</script>

<div id="grablesTime">
</div>

<div id="grables">			
</div>
