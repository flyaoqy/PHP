<!DOCTYPE html>
<html>
<head>
<title>PHP工作流引擎</title>
<base href="<?php echo base_url(); ?>" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- jquery -->
<script src="common/js/jquery-1.8.2.js" type="text/javascript"></script>
<script src="common/js/jquery.form.js" type="text/javascript" ></script>

<!-- jquery UI -->
<link href="common/css/jquery-ui-1.9.0.custom.css" rel="stylesheet" />		
<script src="common/js/jquery-ui-1.9.0.custom.js" type="text/javascript"></script>

<!--layer弹出框样式 -->
<script type="text/javascript" src="common/plugins/layer/layer.js"></script>

<script src="common/plugins/js/go.js" type="text/javascript"></script>
<script src="common/js/wfedit.js" type="text/javascript"></script>
<script id="code">
  function init() {
    var $go = go.GraphObject.make;  // for conciseness in defining templates

    myDiagram =
      $go(go.Diagram, "myDiagram",  // must name or refer to the DIV HTML element
        {
          initialContentAlignment: go.Spot.Center,
          allowDrop: true,  // must be true to accept drops from the Palette
          "LinkDrawn": showLinkLabel,  // this DiagramEvent listener is defined below
          "LinkRelinked": showLinkLabel,
          "animationManager.duration": 800, // slightly longer than default (600ms) animation
          "undoManager.isEnabled": true  // enable undo & redo
        });

    // when the document is modified, add a "*" to the title and enable the "Save" button
    myDiagram.addDiagramListener("Modified", function(e) {
      var button = document.getElementById("SaveButton");
      if (button) button.disabled = !myDiagram.isModified;
      var idx = document.title.indexOf("*");
      if (myDiagram.isModified) {
        if (idx < 0) document.title += "*";
      } else {
        if (idx >= 0) document.title = document.title.substr(0, idx);
      }
    });

    // helper definitions for node templates

    function nodeStyle() {
      return [
        // The Node.location comes from the "loc" property of the node data,
        // converted by the Point.parse static method.
        // If the Node.location is changed, it updates the "loc" property of the node data,
        // converting back using the Point.stringify static method.
        new go.Binding("location", "loc", go.Point.parse).makeTwoWay(go.Point.stringify),
        {
          // the Node.location is at the center of each node
          locationSpot: go.Spot.Center,
          //isShadowed: true,
          //shadowColor: "#888",
          // handle mouse enter/leave events to show/hide the ports
          mouseEnter: function (e, obj) { showPorts(obj.part, true); },
          mouseLeave: function (e, obj) { showPorts(obj.part, false); }
        }
      ];
    }

    // when a node is double-clicked, add a child to it
    function nodeDoubleClick(e, obj) {
      var clicked = obj.part;
      if (clicked !== null) {
        var thisemp = clicked.data;
		//console.log(thisemp);
		node_edit($("#wfuid").val(),thisemp.id,thisemp.text);
      }
    }
	 // when a node is link-clicked, add a child to it
    function linkDoubleClick(e, obj) {
      var clicked = obj.part;
      if (clicked !== null) {
        var thisemp = clicked.data;
		//console.log(thisemp);
		link_edit($("#wfuid").val(),thisemp.from,thisemp.to,thisemp.condition_type,thisemp.condition_value);
      }
    }

    // Define a function for creating a "port" that is normally transparent.
    // The "name" is used as the GraphObject.portId, the "spot" is used to control how links connect
    // and where the port is positioned on the node, and the boolean "output" and "input" arguments
    // control whether the user can draw links from or to the port.
    function makePort(name, spot, output, input) {
      // the port is basically just a small circle that has a white stroke when it is made visible
      return $go(go.Shape, "Circle",
               {
                  fill: "transparent",
                  stroke: null,  // this is changed to "white" in the showPorts function
                  desiredSize: new go.Size(8, 8),
                  alignment: spot, alignmentFocus: spot,  // align the port on the main Shape
                  portId: name,  // declare this object to be a "port"
                  fromSpot: spot, toSpot: spot,  // declare where links may connect at this port
                  fromLinkable: output, toLinkable: input,  // declare whether the user may draw links to/from here
                  cursor: "pointer"  // show a different cursor to indicate potential link point
               });
    }

    // define the Node templates for regular nodes

    var lightText = 'whitesmoke';

    myDiagram.nodeTemplateMap.add("",  // the default category
      $go(go.Node, "Spot", nodeStyle(),
    	{ doubleClick: nodeDoubleClick },
        // the main object is a Panel that surrounds a TextBlock with a rectangular Shape
        $go(go.Panel, "Auto",
          $go(go.Shape, "Rectangle",
            { fill: "#00A9C9", stroke: null },
            new go.Binding("figure", "figure")),
          $go(go.TextBlock,
            {
              font: "bold 11pt Helvetica, Arial, sans-serif",
              stroke: lightText,
              margin: 8,
              maxSize: new go.Size(160, NaN),
              wrap: go.TextBlock.WrapFit,
              editable: false
            },
            new go.Binding("text").makeTwoWay())
        ),
        // four named ports, one on each side:
        makePort("T", go.Spot.Top, false, true),
        makePort("L", go.Spot.Left, true, true),
        makePort("R", go.Spot.Right, true, true),
        makePort("B", go.Spot.Bottom, true, false)
      ));

      myDiagram.nodeTemplateMap.add("split",  // the default category
          $go(go.Node, "Spot", nodeStyle(),
              { doubleClick: nodeDoubleClick },
              // the main object is a Panel that surrounds a TextBlock with a rectangular Shape
              $go(go.Panel, "Auto",
                  $go(go.Shape, "Rectangle",
                      { fill: "#008A00", stroke: null },
                      new go.Binding("figure", "figure")),
                  $go(go.TextBlock,
                      {
                          font: "bold 11pt Helvetica, Arial, sans-serif",
                          stroke: lightText,
                          margin: 8,
                          maxSize: new go.Size(160, NaN),
                          wrap: go.TextBlock.WrapFit,
                          editable: false
                      },
                      new go.Binding("text").makeTwoWay())
              ),
              // four named ports, one on each side:
              makePort("T", go.Spot.Top, false, true),
              makePort("L", go.Spot.Left, true, true),
              makePort("R", go.Spot.Right, true, true),
              makePort("B", go.Spot.Bottom, true, false)
          ));
      myDiagram.nodeTemplateMap.add("join",  // the default category
          $go(go.Node, "Spot", nodeStyle(),
              { doubleClick: nodeDoubleClick },
              // the main object is a Panel that surrounds a TextBlock with a rectangular Shape
              $go(go.Panel, "Auto",
                  $go(go.Shape, "Rectangle",
                      { fill: "#AC193D", stroke: null },
                      new go.Binding("figure", "figure")),
                  $go(go.TextBlock,
                      {
                          font: "bold 11pt Helvetica, Arial, sans-serif",
                          stroke: lightText,
                          margin: 8,
                          maxSize: new go.Size(160, NaN),
                          wrap: go.TextBlock.WrapFit,
                          editable: false
                      },
                      new go.Binding("text").makeTwoWay())
              ),
              // four named ports, one on each side:
              makePort("T", go.Spot.Top, false, true),
              makePort("L", go.Spot.Left, true, true),
              makePort("R", go.Spot.Right, true, true),
              makePort("B", go.Spot.Bottom, true, false)
          ));

    myDiagram.nodeTemplateMap.add("Start",
      $go(go.Node, "Spot", nodeStyle(),
        $go(go.Panel, "Auto",
          $go(go.Shape, "Circle",
            { minSize: new go.Size(40, 40), fill: "#79C900", stroke: null }),
          $go(go.TextBlock, "Start",
            { font: "bold 11pt Helvetica, Arial, sans-serif", stroke: lightText },
            new go.Binding("text"))
        ),
        // three named ports, one on each side except the top, all output only:
        makePort("L", go.Spot.Left, true, false),
        makePort("R", go.Spot.Right, true, false),
        makePort("B", go.Spot.Bottom, true, false)
      ));

    myDiagram.nodeTemplateMap.add("End",
      $go(go.Node, "Spot", nodeStyle(),
        $go(go.Panel, "Auto",
          $go(go.Shape, "Circle",
            { minSize: new go.Size(40, 40), fill: "#DC3C00", stroke: null }),
          $go(go.TextBlock, "End",
            { font: "bold 11pt Helvetica, Arial, sans-serif", stroke: lightText },
            new go.Binding("text"))
        ),
        // three named ports, one on each side except the bottom, all input only:
        makePort("T", go.Spot.Top, false, true),
        makePort("L", go.Spot.Left, false, true),
        makePort("R", go.Spot.Right, false, true)
      ));

    myDiagram.nodeTemplateMap.add("Comment",
      $go(go.Node, "Auto", nodeStyle(),
        $go(go.Shape, "File",
          { fill: "#EFFAB4", stroke: null }),
        $go(go.TextBlock,
          {
            margin: 5,
            maxSize: new go.Size(200, NaN),
            wrap: go.TextBlock.WrapFit,
            textAlign: "center",
            editable: true,
            font: "bold 12pt Helvetica, Arial, sans-serif",
            stroke: '#454545'
          },
          new go.Binding("text").makeTwoWay())
        // no ports, because no links are allowed to connect with a comment
      ));


    // replace the default Link template in the linkTemplateMap
    myDiagram.linkTemplate =
      $go(go.Link,  // the whole link panel
        {
          routing: go.Link.AvoidsNodes,
          curve: go.Link.JumpOver,
          corner: 5, toShortLength: 4,
          relinkableFrom: true,
          relinkableTo: true,
          reshapable: true,
          resegmentable: true,
          // mouse-overs subtly highlight links:
          mouseEnter: function(e, link) { link.findObject("HIGHLIGHT").stroke = "rgba(30,144,255,0.2)"; },
          mouseLeave: function(e, link) { link.findObject("HIGHLIGHT").stroke = "transparent"; }
        },
        { doubleClick: linkDoubleClick },
        new go.Binding("points").makeTwoWay(),
        $go(go.Shape,  // the highlight shape, normally transparent
          { isPanelMain: true, strokeWidth: 8, stroke: "transparent", name: "HIGHLIGHT" }),
        $go(go.Shape,  // the link path shape
          { isPanelMain: true, stroke: "gray", strokeWidth: 2 }),
        $go(go.Shape,  // the arrowhead
          { toArrow: "standard", stroke: null, fill: "gray"}),
        $go(go.Panel, "Auto",  // the link label, normally not visible
          { visible: false, name: "LABEL", segmentIndex: 2, segmentFraction: 0.5},
          new go.Binding("visible", "visible").makeTwoWay(),
          $go(go.Shape, "RoundedRectangle",  // the label shape
            { fill: "#F8F8F8", stroke: null }),
          $go(go.TextBlock, "Yes",  // the label
            {
              textAlign: "center",
              font: "10pt helvetica, arial, sans-serif",
              stroke: "#333333",
              editable: true
            },
            new go.Binding("text", "text").makeTwoWay())
        )
      );

    // Make link labels visible if coming out of a "conditional" node.
    // This listener is called by the "LinkDrawn" and "LinkRelinked" DiagramEvents.
    function showLinkLabel(e) {
      var label = e.subject.findObject("LABEL");
      if (label !== null) label.visible = (e.subject.fromNode.data.figure === "Diamond");
    }

    // temporary links used by LinkingTool and RelinkingTool are also orthogonal:
    myDiagram.toolManager.linkingTool.temporaryLink.routing = go.Link.Orthogonal;
    myDiagram.toolManager.relinkingTool.temporaryLink.routing = go.Link.Orthogonal;

    load();  // load an initial diagram from some JSON text

    // initialize the Palette that is on the left side of the page
    myPalette =
      $go(go.Palette, "myPalette",  // must name or refer to the DIV HTML element
        {
          "animationManager.duration": 800, // slightly longer than default (600ms) animation
          nodeTemplateMap: myDiagram.nodeTemplateMap,  // share the templates used by myDiagram
          model: new go.GraphLinksModel([  // specify the contents of the Palette
            { category: "Start", text: "开始" },
            { text: "审批节点" },
            { category: "split", text: "拆分节点" },
            { category: "join", text: "合并节点" },
            { text: "条件", figure: "Diamond" },
            { category: "End", text: "结束" },
            { category: "Comment", text: "注释" }

          ])
        });

  }

  // Make all ports on a node visible when the mouse is over the node
  function showPorts(node, show) {
    var diagram = node.diagram;
    if (!diagram || diagram.isReadOnly || !diagram.allowLink) return;
    node.ports.each(function(port) {
        port.stroke = (show ? "white" : null);
      });
  }


  // Show the diagram's model in JSON format that the user may edit
  function save() {
    document.getElementById("mySavedModel").value = myDiagram.model.toJson();
    myDiagram.isModified = false;
    $('#theForm').ajaxSubmit({
		dataType:'json',
		success : function(json){
			if(json.status == 'success'){
				layer.alert('保存成功',{icon: 1});
			}else{
				show_message(json.msg);
			}
		}
	});

    
  }
  function load() {
    myDiagram.model = go.Model.fromJson(document.getElementById("mySavedModel").value);
  }
</script>
</head>
<body onload="init()">
<div id="sample">
  <div style="width:100%; white-space:nowrap;">
    <span style="display: inline-block; vertical-align: top; padding: 5px; width:100px">
      <div id="myPalette" style="border: solid 1px gray; height: 720px"></div>
    </span>
	
    <span style="display: inline-block; vertical-align: top; padding: 5px; width:90%">
	     <div style="margin-bottom: 10px;">
	     <?php if(@$wfuid != ""){?>
	      <button id="SaveButton" onclick="save()">保存流程图</button>
	      <?php }?>
	     </div>
	 
      <div id="myDiagram" style="border: solid 1px gray; height: 720px"></div>
    </span>
  </div>
  <form id="theForm" name="theForm" method="post" action="<?= site_url('config/workflow/wfedit/flow_chart_save') ?>">
  <div style="display:none">
  	<input type="hidden" id="wfuid" name="wfuid" value="<?php echo $wfuid?>">
   <textarea id="mySavedModel" name="mySavedModel" style="width:100%;height:300px"><?php echo $linksModel?></textarea>
  
  </div>
  </form>
 
</div>
<div id="dialog_div"></div>
<div id="dialog_node"></div>
</body>
</html>
