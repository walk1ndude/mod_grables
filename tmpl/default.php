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

echo $lastChildPos, EOL;


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
					if ($cellVal === "") {
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
						/*continue;
						$nextCellIndex = $currentCellIndex;
						$nextCellIndex[] = 1;
						
						$nextLevel = implode(".", $nextCellIndex);
						
						if ($cellVal === $nextLevel) {
							// Если начинается новый подуровень
							$currentCellIndex[] = 1;
							
							$lastChildPos[i] += 1;
							
							$prevChildPos[i] += 1;
							$prevChildPos[j] = $lastChildPos[j];
							
							$lastChildPos[j] = 0;
						}
						else {
							// Если произошел выход из подуровней, собрать дочерние элементы
							/*
							
							$nextCellIndex = implode(".", $currentCellIndex);
							
							do {
								$before = $nextCellIndex;
								
								$nextCellIndex = preg_replace("/\.\d+$/", "", implode(".", $currentCellIndex));
								
								echo $nextCellIndex, " ", $before, EOL;
								
								array_pop($currentCellIndex);
								
								$prevChildPos = count($children) - 2;
								$prevChildPosPos = count($children[$prevChildPos]) - 1;
								
								$children[$prevChildPos][$prevChildPosPos]["children"] = array_pop($children);
								
							// Продолжать пока не достигли самого верхнего уровня или
							// не установили соотвествие
							
							} while ($before === $nextCellIndex);
							
							$gotNewCell = true;
						
							$currentCellIndex[count($currentCellIndex) - 1] += 1; 
							
							$lastChild = &$children[count($children) - 1];	
							$lastChildCount = max(count($lastChild) - 1, 0);
							
							$lastChild[$lastChildCount] = [];
							
							$lastAdded = &$lastChild[$lastChildPos];
							*/	
						}
	
					break;
					
				case 1 :	
					// Заполняем последний добавленный в иерархию объект значениями из полей
					if ($gotNewCell) { 				
						$lastAdded["name"] = $cellVal;
						$lastAdded["level"] = "1";
						$lastAdded["children"] = [];
					}
					break;
					
				case $columnWithValue : 
					if ($gotNewCell) {
						$lastAdded["value"] = floatval($cellVal); 
					}
					break;
			}
		}
		
		$i += 1;
	}
}

echo print_r($children[0]);

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
