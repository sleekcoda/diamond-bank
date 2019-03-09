jQuery(document).ready(function($){
    var cont = jQuery('.report_content');
    jQuery('#years a').on('click',function(e){
        e.preventDefault();
        //var container_offset = 
        jQuery('#years li').removeClass('active');
        jQuery(this).parent().addClass('active');
        
        var div_height = jQuery(this.hash+'.report_content').height();
        var div_index = jQuery(this.hash+'.report_content').index();
        var scroll_to = div_index * div_height;
        
        var pos = jQuery('#report_container')[0].scrollHeight;
        if (this.hash !== "") {
          e.preventDefault();
          var hash = this.hash;
          jQuery('#report_container').animate({
            scrollTop: scroll_to
          }, 800, function(){
               //window.location.hash = hash;
               //position=null;
          });
        }        
    });
    
    
    var list = jQuery('.report_content');
    
    jQuery('#report_container').on('scroll',function(e){
        jQuery.each( list, function( key, value ) {
            var pos =  list[key].offsetHeight * key;
          if( jQuery('#report_container').scrollTop() >= pos){
                jQuery('#years li').removeClass('active');
                jQuery('#years li').eq(key).addClass('active');
				console.log(key);
            }
        });
        
    }); 
    
});