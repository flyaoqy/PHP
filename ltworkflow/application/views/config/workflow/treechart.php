<!DOCTYPE html>
<html>
<head>
<title>PHP工作流引擎</title>
<base href="<?php echo base_url(); ?>" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- jquery -->
<script src="common/js/jquery-1.8.2.js" type="text/javascript"></script>
<!-- jquery UI -->
<link href="common/css/jquery-ui-1.9.0.custom.css" rel="stylesheet" />		
<script src="common/js/jquery-ui-1.9.0.custom.js" type="text/javascript"></script>

<script src="common/plugins/js/go.js" type="text/javascript"></script>
<script src="common/js/wfedit.js" type="text/javascript"></script>
<script id="code">
  function init() {
    var $go = go.GraphObject.make;  // for conciseness in defining templates

    myDiagram =
      $go(go.Diagram, "myDiagram", // must be the ID or reference to div
        {
          initialContentAlignment: go.Spot.Center,
          // make sure users can only create trees
          //validCycle: go.Diagram.CycleDestinationTree,
          // users can select only one part at a time
          maxSelectionCount: 1,
          layout:
            $go(go.TreeLayout,
              {
                // properties for most of the tree:
                angle: 90,
                layerSpacing: 35
              }),
          // enable undo & redo
          "undoManager.isEnabled": true
        });
//    var levelColors = ["#AC193D/#BF1E4B", "#2672EC/#2E8DEF", "#8C0095/#A700AE", "#5133AB/#643EBF",
//                       "#008299/#00A0B1", "#D24726/#DC572E", "#008A00/#00A600", "#094AB2/#0A5BC4"];
//

    // when a node is double-clicked, add a child to it
    function nodeDoubleClick(e, obj) {
      var clicked = obj.part;
      if (clicked !== null) {
        var thisemp = clicked.data;
		console.log(thisemp);
		//node_edit($("#wfuid").val(),thisemp.id,thisemp.name);
      }
    }
	 // when a node is link-clicked, add a child to it
    function linkDoubleClick(e, obj) {
      var clicked = obj.part;
      if (clicked !== null) {
        var thisemp = clicked.data;
		console.log(thisemp);
        //link_edit($("#wfuid").val(),thisemp.from,thisemp.to);
      }
    }


    // define the Node template
    myDiagram.nodeTemplate =
      $go(go.Node, "Auto",
        { doubleClick: nodeDoubleClick },
        // for sorting, have the Node.text be the data.name
        new go.Binding("text", "name"),
        // bind the Part.layerName to control the Node's layer depending on whether it isSelected
        new go.Binding("layerName", "isSelected", function(sel) { return sel ? "Foreground" : ""; }).ofObject(),
        // define the node's outer shape
        $go(go.Shape, "Rectangle",
          {
            name: "SHAPE", 
            //fill: "#AC193D", 
            stroke: null,
            // set the port properties:
            portId: "", fromLinkable: true, toLinkable: true, cursor: "pointer"
          },
          	new go.Binding("fill", "status", function(v) {
              		if(v=="start"){
              			return "#008A00";
                  	}else if(v=="end"){
              			return "#AC193D";
                  	}else{
                  		return "#008299";
                    }
              	})
          ),
        
        $go(go.Panel, "Horizontal",
          // define the panel where the text will appear
          $go(go.Panel, "Table",
            {
              maxSize: new go.Size(150, 999),
              margin: new go.Margin(6, 10, 0, 3),
              defaultAlignment: go.Spot.Center
            },
            $go(go.RowColumnDefinition, { column: 2, width: 4 }),
            $go(go.TextBlock,
              {
                row: 0, column: 0, columnSpan: 5,
                font: "12px  Segoe UI,sans-serif",stroke: "white",
                editable: false, isMultiline: false,
                minSize: new go.Size(10, 16)
              },
              new go.Binding("text", "name").makeTwoWay()),
            $go(go.TextBlock,
              { row: 1, column: 0,
            	font: "12px  Segoe UI,sans-serif", stroke: "white" 
               },
              new go.Binding("text", "id", function(v) {return "ID: " + v;}))
          )  // end Table Panel
        ) // end Horizontal Panel
      );  // end Node

    // define the Link template
    myDiagram.linkTemplate =
      $go(go.Link, go.Link.Orthogonal,
        { corner: 5, relinkableFrom: true, relinkableTo: true },
		{ doubleClick: linkDoubleClick },
        $go(go.Shape, { strokeWidth: 4, stroke: "#00a4a4" }),
        $go(go.Shape, { toArrow: "standard", stroke:"#00a4a4",fill:"#00a4a4"})
		
		);  // the link shape

    // read in the JSON-format data from the "mySavedModel" element
    load();
  }
  function load() {
    myDiagram.model = go.Model.fromJson(document.getElementById("mySavedModel").value);
  }
</script>
</head>
<body onload="init()">
<div id="sample">
  <p>
    <?php echo @$base['wf_name']?>
  </p>
  <div id="myDiagram" style="border: solid 1px black; height: 600px"></div>




  
  <div style="display:none">
  	<input type="hidden" id="wfuid" name="wfuid" value="<?php echo $wfuid?>">
    <textarea id="mySavedModel" style="width:100%;height:250px"><?php echo $treeModel?></textarea>
  </div>
</div>
<div id="dialog_div"></div>
<div id="dialog_node"></div>
</body>
</html>
