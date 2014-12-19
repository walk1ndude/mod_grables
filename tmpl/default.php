<?php defined('_JEXEC') or die('Restricted access'); // no direct access 

JHtml::script('jquery-2.1.1.min.js', 'modules/mod_grables/js/');

JHtml::script('d3.min.js', 'modules/mod_grables/js/');

JHtml::script('grables.js', 'modules/mod_grables/js/');

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
