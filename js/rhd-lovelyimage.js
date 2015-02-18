(function($){
	$(".rhd_lovelyimage_container").each(function(){
		var $box = $(this),
			$cap = $box.children("figcaption"),
			hex = $cap.data("bgColor"),
			opacityBase = $cap.data("opacityBase"),
			opacityHover = $cap.data("opacityHover");

		if ( opacityBase === undefined || opacityBase === "" )
			opacityBase = 0.5;
		if ( opacityHover === undefined || opacityHover === "" )
			opacityHover = 0.8;

		var rgb = hexToRgb(hex);
		var rgba_base = 'rgba(' + rgb.r + ',' + rgb.g + ',' + rgb.b + ',' + opacityBase + ')';
		var rgba_hover = 'rgba(' + rgb.r + ',' + rgb.g + ',' + rgb.b + ',' + opacityHover + ')';

		// Set initial values, overriding default CSS
		$cap.css("backgroundColor", rgba_base);

		$box.hover(
			function(){
				$cap.stop().animate({
					backgroundColor: rgba_hover
				});
			},
			function(){
				$cap.stop().animate({
					backgroundColor: rgba_base
				});
			}
		);
	});
})(jQuery);


/* ==========================================================================
	Functions
   ========================================================================== */

function hexToRgb(hex) {
    // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
    var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
    hex = hex.replace(shorthandRegex, function(m, r, g, b) {
        return r + r + g + g + b + b;
    });

    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}