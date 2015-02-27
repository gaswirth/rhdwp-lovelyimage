jQuery(document).ready(function($){

	// Widget: Image Style
	$(".rhd_lovelyimage_preview").each(function(){
		var $this = $(this);

		if ( $this.attr("src") === undefined || $this.attr("src") === "" )
			$this.hide();
		else {
			$this.show();
		}
	});
});