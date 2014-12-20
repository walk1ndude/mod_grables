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

$grables = [];
$grablesCell;

$currentCell = [1];

foreach ($worksheet->getRowIterator() as $row) {
	$cellIterator = $row->getCellIterator();
	$cellIterator->setIterateOnlyExistingCells(true);
	
	$i = 0;
	$cellVal;
	
	$grablesCell = [];
	$newCell = [];
	$cellToAdd = false;
	
	foreach ($cellIterator as $cell) {
		if (!is_null($cell)) {
			$cellVal = $cell->getCalculatedValue();
			
			if ($columnWithValue < 0 && $cellVal === "Исполнено бюджет субъекта РФ") {
				$columnWithValue = $i;
			}
			
			switch ($i) {
				case 0 :
					if ($cellVal === implode(".", $currentCell)) {
						$cellToAdd = true;
						$currentCell[count($currentCell) - 1] += 1; 
					}
					break;
					
				case 1 :	
					if ($cellToAdd) {
						$newCell["name"] = $cellVal;
						$newCell["level"] = "1";
						$newCell["children"] = [];
					}
					break;
					
				case $columnWithValue : 
					if ($cellToAdd) {
						$newCell["value"] = floatval($cellVal); 
					}
					break;
			}
		}
		
		$i += 1;
	}
	
	if ($cellToAdd) {
		$grables[] = $newCell;
	}
}

echo print_r($grables);

function escapeJsonString($value) {
    # list from www.json.org: (\b backspace, \f formfeed)    
    $escapers =     array("\\",  "/",   "\"",  "\n",  "\r",  "\t", "\x08", "\x0c");
    $replacements = array("", "", '"', "", "", "",  "",  "");
    $result = str_replace($escapers, $replacements, $value);
    return $result;
};

$paramsToJS = array(
	"sections" => escapeJsonString($params->get("grablesJSON", '{}')),
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
