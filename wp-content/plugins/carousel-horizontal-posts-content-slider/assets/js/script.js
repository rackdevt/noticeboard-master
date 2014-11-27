jQuery(document).ready(function($) {
auto_s = (chpcsArgs.auto_scroll=="true") ? true : false;
cir = (chpcsArgs.circular=="true") ? true : false;
$("#wa_chpc_slider").carouFredSel({
	circular: cir,
	width: '100%',
	height: 'auto',
	infinite: false,
	auto 	: auto_s,
	prev	: {	
		button	: "#wa_chpc_slider_prev",
		key		: "left"
	},
	next	: { 
		button	: "#wa_chpc_slider_next",
		key		: "right"
	},
	pagination	: "#wa_chpc_slider_pag",
	scroll : {
items			: parseInt(chpcsArgs.no_of_items_to_scroll),
fx: chpcsArgs.fx,
easing : chpcsArgs.easing_effect,
duration: 500,					
pauseOnHover	: true
},
	swipe: {
onMouse: false,
onTouch: true
}
});
});