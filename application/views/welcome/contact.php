<!-- Columns -->
<script type="text/javascript">
	var outerLayout, middleLayout, innerLayout_Center, innerLayout_South;
	
	$(document).ready(function () {
	
		middleLayout = $('div.ui-layout-center').layout({
			name: "middle", 
			center__paneSelector: ".middle-center",
			spacing_open: default_spacing_open,	// ALL panes
			spacing_closed: default_spacing_closed // ALL panes
		});
	});
</script>
    <div id="cols" class="box">
        <!-- Content -->
        <div id="content">
				<div id="container">
					<div id="container-content" class="pane ui-layout-center">
						<div id="" class="middle-center"><!-- Inner-Center Layout Container -->
						<div class="ui-widget-header summary-header" style="border: 0;">Liên hệ</div>
						<div id="help-content" class="ui-layout-content"></div>
						<!--  <div class="ui-widget-header summary-footer" style="border: 0;">&nbsp;</div> -->
					</div>
					<?/*
					<div class="middle-south"><!-- Inner-South Layout Container -->
						Inner-South
					</div>
					*/?>
				</div> <!-- /container-content -->
				<div class="pane ui-layout-west">
					<div id="tabs" class="ui-layout-content ui-widget ui-corner-all" style="background-color: #D7ECF4;">
					</div>
				</div> <!-- /ui-layout-west -->
				</div>
        <hr class="noscreen" />
        </div> <!-- /content -->
    </div> <!-- /cols -->