						<script type="text/javascript">
						<!--
						$(document).ready(function () {
							$("#aside-01-tree3").dynatree({
								checkbox: true,
								// Override class name for checkbox icon:
								classNames: {checkbox: "dynatree-radio"},
								checkbox: false, // Show checkboxes
								selectMode: 1,
								children: treeData,
								onSelect: function(select, node) {
									// Get a list of all selected nodes, and convert to a key array:
									var selKeys = $.map(node.tree.getSelectedNodes(), function(node){
										return node.data.key;
									});
									$("#echoSelection3").text(selKeys.join(", "));

									// Get a list of all selected TOP nodes
									var selRootNodes = node.tree.getSelectedNodes(true);
									// ... and convert to a key array:
									var selRootKeys = $.map(selRootNodes, function(node){
										return node.data.key;
									});
									$("#echoSelectionRootKeys3").text(selRootKeys.join(", "));
									$("#echoSelectionRoots3").text(selRootNodes.join(", "));
								},
								onDblClick: function(node, event) {
									node.toggleSelect();
								},
								onKeydown: function(node, event) {
									if( event.which == 32 ) {
										node.toggleSelect();
										return false;
									}
								},
								// The following options are only required, if we have more than one tree on one page:
								//initId: "treeData",
								cookieId: "dynatree-Cb3",
								idPrefix: "dynatree-Cb3-"
							});
							$( "#tabs" ).tabs({ disabled: [1] });
							tabs_scrollable();
						});
						//-->
						</script>
						<div id="tabs">
							<ul class="tabs-ul">
								<li><a href="#tabs-1">Danh sách xe</a></li>
								<li><a href="#tabs-2">Tinh chỉnh</a></li>
							</ul>
							<div id="tabs-1">
								<div id="aside-01-tree3" ></div>
							</div>
							<div id="tabs-2">
								<div id="tabs-2-content">
								<p>Morbi tincidunt, Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus. Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.</p>
								</div>
							</div>
						</div>