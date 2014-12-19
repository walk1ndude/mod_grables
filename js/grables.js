/*
var sections = [
	{ 
		"name" : "first",
		"value" : "1232.23",
		"level" : "1",
		"img" : "001.png",
		"children" : [
			{
				"name" : "first_1",
				"value" : "123",
				"level" : "1",
				"img" : "002.png",
				"children" : []
			},
			{
				"name" : "first_2",
				"value" : "1453",
				"level" : "1",
				"img" : "003.png",
				"children" : []
			},
			{
				"name" : "first_3",
				"value" : "1453",
				"level" : "1",
				"img" : "004.png",
				"children" : []
			}
		]
	},
	{ 
		"name" : "second",
		"value" : "122.23",
		"level" : "1",
		"img" : "002.png",
		"children" : [
			{
				"name" : "second_1",
				"value" : "123",
				"level" : "1",
				"img" : "004.png",
				"children" : []
			},
			{
				"name" : "second_2",
				"value" : "1453",
				"level" : "1",
				"img" : "006.png",
				"children" : []
			},
			{
				"name" : "second_3",
				"value" : "1453",
				"level" : "1",
				"img" : "012.png",
				"children" : []
			}
		]
	},
	{ 
		"name" : "third",
		"value" : "1223.3",
		"level" : "1",
		"img" : "012.png",
		"children" : [
			{
				"name" : "third_1",
				"value" : "123",
				"level" : "2",
				"img" : "013.png",
				"children" : []
			},
			{
				"name" : "third_2",
				"value" : "1453",
				"level" : "2",
				"img" : "010.png",
				"children" : []
			}
		]
	},
	{ 
		"name" : "fourth",
		"value" : "422.23",
		"level" : "1",
		"img" : "004.png",
		"children" : [
			{
				"name" : "fourth_1",
				"value" : "123",
				"level" : "1",
				"img" : "005.png",
				"children" : []
			},
			{
				"name" : "fourth_2",
				"value" : "1.453",
				"level" : "2",
				"img" : "007.png",
				"children" : []
			},
			{
				"name" : "fourth_3",
				"value" : "1453",
				"level" : "3",
				"img" : "008.png",
				"children" : []
			},
			{
				"name" : "fourth_4",
				"value" : "14.53",
				"level" : "1",
				"img" : "002.png",
				"children" : []
			}
		]
	},
];
*/
$j = jQuery.noConflict();

var maxElementsInRow;

function wrap(text) {
    text.each(function () {
        var text = d3.select(this),
            rect = this.parentNode.getBoundingClientRect(),
            words = text.text().split(/[ \f\n\r\t\v]+/).reverse(),
            word,
            line = [],
            lineNumber = 0,
            lineHeight = 1.1, // ems
            y = text.attr("y"),
            dy = parseFloat(text.attr("dy")),
            width = parseFloat(text.attr("width")),
            tspan = text.text(null)
            			.append("tspan")
            			.attr("x", 0)
            			.attr("y", y)
            			.attr("dy", dy + "em");

        while (word = words.pop()) {
            line.push(word);
            tspan.text(line.join(" "));

            //console.log(tspan.node().getComputedTextLength(), width, text.text());

            if (tspan.node().getComputedTextLength() > width && line.length > 1) {
                line.pop();
                tspan.text(line.join(" "));
                line = [word];
                tspan = text.append("tspan")
                			.attr("x", 0)
                			.attr("y", y)
                			.attr("dy", ++lineNumber * lineHeight + dy + "em")
                			.text(word);
            }
        }
    });
}

var __history = [];

function drawGrables(root, buttonStyle) {
	if (!root.length) {
		return;
	}
	
	root = root.slice(0, maxElementsInRow);
	
	var margins = {
		"left" : 40,
		"right" : 40,
		"top" : 40,
		"bottom" : 40
	};
	
	var gW = window.innerWidth / 2; //$j("#grables").parent().width();
	var gH = 600;
	
	$j("#grables").attr("width", function () { return gW + "px"; });
	$j("#grables").attr("height", function () { return gH + "px"; });
	
	d3.select("svg").remove();
	
	var actGW = gW - margins.left - margins.right;
	var actGH = gH - margins.top - margins.bottom;
	
	var color = d3.scale.category10();
	
	
	var grablesFull = d3.select("#grables")
					.append("svg:svg")
					.attr("class", "grables")
					.attr("overflow", "scroll")
					.attr("width", function (d) { return gW + "px"; })
					.attr("height", gH + "px");
					
	var grables = grablesFull.append("svg:svg")
						.attr("overflow", "visible")
						.attr("width", function (d) { return actGW + "px"; })
						.attr("height", function (d) { return actGH + "px"; })
						.attr("x", function (d) { return margins.left; })
						.attr("y", function (d) { return margins.top; });
						
    grablesFull.append("svg:image")
    	.attr("class", "back-key")
        .attr("xlink:href", function (d) { return "modules/mod_grables/pics/back.svg"; })
        .attr("width", function (d) { return margins.left + "px"; })
        .attr("height", function (d) { return margins.top + "px"; })
        .attr("preserveAspectRatio", "xMidYMid meet")
        .style("display", buttonStyle);
	
	
	var leftBack = 0;
	var rightBack = actGW - leftBack;
	
	var widthBack = rightBack - leftBack;
	var widthFront = widthBack;
	
	var thickness = {
		"back" : widthBack / maxElementsInRow,
		"front" : widthFront / maxElementsInRow,
	};
		
	var padding = {
		"back" : 5,
		"front" : 5
	};
	
	var ratioPaddingBack = 2;
	var ratioPaddingFront = 2;
	
	thickness["innerFront"] = thickness.front - 2 * ratioPaddingFront * padding.front;
	thickness["innerBack"] = thickness.back - 2 * ratioPaddingFront * padding.back; 
	
	
	var heightBack = gH / 2;
	var heightFront = thickness.front - padding.front * ratioPaddingFront;
	
	var terminator = 7 * actGH / 12;
	
	var xBack = leftBack;
	var i = 0;
	
	var offset = Math.floor((widthBack - Math.min(root.length, maxElementsInRow) * thickness.back) / 2); 
	
	var cells = grables.selectAll("g")
					  .data(root)
					  .enter();
	
	var cell = cells.append("svg:g")
		 .attr("class", "grables-cell")
		 .attr("overflow", "visible")
		 .attr("width", function (d) { return thickness.back + "px"; })
		 .attr("height", function (d) { return actGH + "px"; })
		 .attr("transform", function (d) {
			 var cellX = xBack + i * thickness.back + offset;
			 i ++;
			 return "translate(" + cellX + ", 0)";
		 });

		 
	var maxValue;
	
	$j(root).each(function (k, v) {
		var val = parseFloat(v.value);
		if (!maxValue) {
			maxValue = val;	
		}
		maxValue = maxValue < val ? val : maxValue;
	});
	
	var textValueHeight = 25;
	var textNameHeight = 25;
		
	var maxBackHeight = terminator - textValueHeight - padding.back;
		 
	cell.append("svg:rect")
		.attr("class", "grables-cell-back")
		.attr("y", function (d) { return (maxBackHeight * (maxValue - d.value) / maxValue) + "px"; })
		.attr("width", function (d) { return thickness.innerBack + "px"; })
		.attr("height", function (d) { return (maxBackHeight * d.value / maxValue) + "px"; })
		.attr("transform", function (d) { return "translate(" + ratioPaddingBack * padding.back + "," + textValueHeight + ")"; })
		.style("stroke", function (d) { return color(d.name); })
		.style("fill", function (d) { return color(d.name); });
		
		
	cell.append("svg:svg")
		.attr("class", "grables-cell-value")
		.attr("y", function (d) { return parseFloat($j($j(this).siblings(".grables-cell-back")[0]).attr("y")); })
		.attr("height", textValueHeight)
		.append("svg:text")
			.attr("class", "grables-cell-value-text")
		 	.attr("y", 10)
		 	.attr("dy", ".35em")
		 	.attr("text-anchor", "start")
		 	.attr("font-size", "16px")
		 	.text(function (d) { return d.value; })
		 	.call(wrap)
			.attr("transform", function (d) { return "translate(" + padding.back + ", 0)"; });
		 	
		 			  
	var cellFront = cell.append("svg:svg")
		.attr("y", function (d) { return terminator; })
		.attr("class", "grables-cell-front");
		
	cellFront.append("svg:path")
		.attr("d", function (d) {
			var cellTop = padding.front;
			var cellBottom = cellTop + heightFront - ratioPaddingFront * padding.front;
			var cellWidth = thickness.innerFront;
			
			var cellLeft = ratioPaddingFront * padding.front;
			var cellRight = cellLeft + cellWidth;
			
			return "M" + cellLeft + " " + cellTop +
				   " L " + cellRight + " " + cellTop +
				   " L " + cellRight + " " + cellBottom +
				   " L " + (cellLeft + 5 * cellWidth / 8) + " " + cellBottom +
				   " L " + (cellLeft + cellWidth / 2) + " " + (cellBottom + heightFront / 8) +
				   " L " + (cellLeft + 3 * cellWidth / 8) + " " + cellBottom +
				   " L " + cellLeft + " " + cellBottom +
				   "Z";
		})
		.style("stroke", "none")
		.style("fill", function (d) { return color(d.name); })
	
	
	cellFront.append("svg:image")
			.attr("xlink:href", function (d) { return "modules/mod_grables/pics/" + d.img; })
			.attr("x", function (d) { return ratioPaddingFront * padding.front; })
			.attr("width", function (d) { return (thickness.front - 2 * ratioPaddingFront * padding.front) + "px"; })
			.attr("height", function (d) { return heightFront + "px"; })
			.attr("preserveAspectRatio", "xMidYMid meet");
		
		  
	cell.append("svg:svg")
		.attr("class", "grables-cell-name")
		.attr("overflow", "visible")
		.attr("y", function (d) { 
			var y = terminator + padding.front + heightFront / 3;
			return (y + 25 * d.level + ratioPaddingFront * padding.front) + "px";
		})
		.attr("width", function (d) { return thickness.front + "px"; })
		.attr("height", function (d) { return textNameHeight * 10 + "px"; })
		.append("svg:text")
			.attr("class", "grables-cell-name-text")
			.attr("y", 10)
			.attr("width", function (d) { return $j(this.parentNode).attr("width"); })
			.attr("height", function (d) { return $j(this.parentNode).attr("height"); })
			.attr("dy", ".35em")
			.attr("text-anchor", "middle")
			.attr("font-size", "10px")
			.text(function (d) { return d.name; })
			.call(wrap)
			.attr("transform", function (d) { return "translate(" + (thickness.front / 2) + ", 0)"; })
			
		
	cell.append("svg:path")
		.attr("class", "grables-line-to-name")
		.attr("stroke-width", "2px")
		.attr("d", function (d) {
			var xC = Math.ceil(thickness.front / 2);
			var yC = terminator + heightFront / 3 + 4 * ratioPaddingFront * padding.front;
			
			var cellText = $j(this).siblings(".grables-cell-name")[0];
			var yCT = d.level === "1" ? yC : $j(cellText).attr("y");
			
			return "M" + xC + " " + yC + " L " + xC + " " + yCT;
		})
		.style("stroke", function (d) { return color(d.name); });

		  
	$j(".grables-cell").hover(
		function() {
			$j(this).children(":not(.grables-cell-value, .grables-cell-name)").css("opacity", "0.5");
			$j(this).children(".grables-cell-name").children(".grables-cell-name-rect").css("opacity", "0.1");
			
			$j($j(this).children(".grables-cell-value").children()).each(function (k, v) {
					$j(v).css("font-size", "18px");
			});
			
			$j(this).css("cursor", "pointer");
		},
		function() {
			$j(this).children(":not(.grables-cell-value, .grables-cell-name)").css("opacity", "1.0");
			$j(this).children(".grables-cell-name").children(".grables-cell-name-rect").css("opacity", "0.3");
			
			$j($j(this).children(".grables-cell-value").children()).each(function (k, v) {
					$j(v).css("font-size", "16px");
			});
			
			$j(this).css("cursor", "default");
		}
	);
	
	$j(".back-key").hover(
		function() {
			$j(this).css("cursor", "pointer");
		},
		function() {
			
			$j(this).css("cursor", "default");
		}
	);
	
	d3.selectAll(".grables-cell").on("click", function(d) {
		var elem;
		
		for (var i = 0; i != root.length; ++ i) {
			if (d.name === root[i].name) {
				elem = root[i];
			}	
		}
		
		if (elem.children.length) {
			__history.push(root);
			
			drawGrables(elem.children, "inline-block");
		}
	});
	
	$j(".back-key").on("click", function () {
		if (__history.length) {
			var root = __history.pop();
			drawGrables(root, __history.length ? "inline-block" : "none");
		}
	});
	
	$j(window).resize(function () {
		drawGrables(root, __history.length ? "inline-block" : "none");
	});
}

$j(document).ready(function($) {
	$j("#grablesTime").text("(по состоянию на " + params.grablesTime + ")");
	
	maxElementsInRow = parseFloat(params.maxElementsInRow);
	
	drawGrables(JSON.parse(params.sections), "none");	
});