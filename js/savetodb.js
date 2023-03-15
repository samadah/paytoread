jQuery(document).ready(function(){
jQuery("#submit").click(function(){
    var name = jQuery("#site_code").val();
jQuery.ajax({
type: 'POST',
url: MyAjax.ajaxurl,
data: {"action": "post_word_count", "dname":name},
success: function(data){
alert(data);
}
});
});
});