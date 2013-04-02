$(document).ready(function () {

	middleLayout = $('div.ui-layout-center').layout({
		name: "middle",
		/*north__paneSelector: ".middle-north",*/
		center__paneSelector: ".middle-center",
		/*south__paneSelector: ".middle-south",*/
		/*north__size: 35,
		north__maxSize: 35,*/
		/*south__size: "50%",*/
		/*south__initClosed: true,*/
		spacing_open: default_spacing_open,	// ALL panes
		spacing_closed: default_spacing_closed // ALL panes
	});
	
	$("#tabs-options-div").tabs({ disabled: [1,2] });
	tabs_options_scrollable();
});