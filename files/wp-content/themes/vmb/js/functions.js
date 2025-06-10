(function($){
	var baseURL="//"+window.location.hostname;
	var pathURL=baseURL+"/wp-content/themes/vmb";
	var currentURL=window.location;
	
	// Load
		
	
	// Interactive
		jQuery.fn.fullLink=function(link,elem){jQuery(this).each(function(){var thisElem=jQuery(this);if(jQuery("a",thisElem).length>0){thisElem.css("cursor","pointer").click(function(e){e.preventDefault();if(link){var thisAttr=jQuery("a:eq("+link+")",this).attr("target");if(thisAttr=="_blank"){window.open(jQuery("a:eq("+link+")",this).attr("href"));}else{window.location=jQuery("a:eq("+link+")",this).attr("href");}}else if(elem){var thisAttr=jQuery("."+elem,this).attr("target");if(thisAttr=="_blank"){window.open(jQuery("."+elem,this).attr("href"));}else{window.location=jQuery("."+elem,this).attr("href");}}else{var thisAttr=jQuery("a",this).attr("target");if(thisAttr=="_blank"){window.open(jQuery("a",this).attr("href"));}else{window.location=jQuery("a",this).attr("href");}}});}});}
		jQuery.fn.randomise=function(selector){var $elems=selector?jQuery(this).find(selector):jQuery(this).children(),$parents=$elems.parent();$parents.each(function(){jQuery(this).children(selector).sort(function(){return Math.round(Math.random())-0.5;}).detach().appendTo(this);});return this;};
})(this.jQuery);