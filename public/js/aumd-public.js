(function( $ ) {
	'use strict';


var AJAXIFY_UM = {
    common: {
        /**
         * Init common scripts
         */
        init: function(){

              var $umform = $(".um-search form ");
              var $gform = $("#gform_1");

          
            if(  $("#aumd-ajaxify-wrapper").length > 0 &&  $umform.length > 0 ||  $gform.length > 0 ){
                  var $form = null;

                  
                  if( $umform.length > 0 ){
                    $form = $umform;
                  }else{
                    $form = $gform;
                  }


                	$(".um-search-submit a.um-do-search", $form).click(function(e){
                			e.preventDefault();

                				var search_param = $form.find('.um-search-filter input').serialize();
                 				
                 				AJAXIFY_UM.common.search_users( search_param );
                 				
                			
                			return false;

                	});

                  $form.append('<div class="um-search-filter"><input name="members_page" type="hidden" value="1" /></div>');
                	
                	$(".um-search-submit a.um-button.um-alt",$form).click(function(e){
               		      e.preventDefault();

             	        	$(':input',$form)
                				  .not(':button, :submit, :reset, :hidden, .medium')
                				  .val('')
                				  .removeAttr('checked')
                				  .removeAttr('selected');

                				$('select',$form).select2("close");
                				$('select',$form).trigger("change");
                        $('input[type=hidden][name=members_page]').val( 1 );
                

                				if ( history.pushState ) {
                     			history.pushState( {}, document.title, location.protocol + '//' + location.host + location.pathname);
                     		}
            
                        var search_param = $form.find('.um-search-filter input').serialize();
                        
                        AJAXIFY_UM.common.search_users(  search_param );
                                
        				     return false;
                	
        			     });

                  if( $('div.um-members-pagi').size() <= 0){
                    $('div.um-members').after('<div class="um-members-pagi uimob340-hide uimob500-hide"></div>');
                  }
                  var $paginate_wrap = $('div.um-members-pagi');
                  AJAXIFY_UM.common.bind_paginate( $paginate_wrap );
   
            }
        },
        search_users: function( params ){

          console.log(params);

        	$("div[class=um-members]").css("opacity","0.6");
        	$("#aumd-ajaxify-wrapper").css("opacity","0.6");

          params = params + '&um_search=1';
         	if ( history.pushState ) {
         		history.pushState( {}, document.title, location.protocol + '//' + location.host + location.pathname+"?"+params );
         	}

            var $ajax_wrapper = $("#aumd-ajaxify-wrapper");
            var post_id = $ajax_wrapper.data("post-id");
                $ajax_wrapper.addClass('aumd-loading');
            // Load progress 
            NProgress.start();
           
            $.ajax({
              type: "POST",
              url: ultimatemember_ajax_url,
              data: {
                    action: "search_directory_members",
                    params: params,
                    post_id: post_id,
               },
              dataType: 'json',
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            //Do something with upload progress here
                        }
                   }, false);

                   return xhr;
                },
            }).success(function( d ){
               console.log(d);
                var $total_members = $("div[class=um-members-total]");
                var total_members = ( d.result.total_users+' Member'+(d.result.total_users > 1?'s':''));
                $("div[class=um-members]").remove();

                if(  d.result.total_users > 0 ){
                   
                    $ajax_wrapper.html( d );
                    $total_members.text(total_members);
                    $total_members.show();
                    $("div[class=um-members-none]").hide();
                   
                    if( $total_members.length <= 0 ){
                         $ajax_wrapper.html('<div class="um-members-intro"><div class="um-members-total">'+total_members+'</div></div>');
                    }
                    $ajax_wrapper.append(d.html);
                }else{
                    $ajax_wrapper.html('<div class="um-members-none"><p>We are sorry. We cannot find any users who match your search criteria.</p></div>');
                    $total_members.hide();
                }
                
                $("div[class=um-members]").css("opacity","1");
                $ajax_wrapper.css("opacity","1");
                um_responsive();
                 AJAXIFY_UM.common.paginate(  d.result );
                
                // done searching
                NProgress.done();
                $ajax_wrapper.removeClass('aumd-loading');

            }).error(function( e ){
                console.log( e );
            });
        },
        paginate: function( d ){
         	 
          var $paginate_wrap = $("div.um-members-pagi");

              $paginate_wrap.empty();
          var html = [];
             if( d.total_users > 1 ){
                $.each(d.pages_to_show, function(i,dd){
                   dd = parseInt(dd);
                   d.page = parseInt(d.page);

                  if( i == 0){
                    if( d.page == 1 ){
                      html.push('<span class="pagi pagi-arrow disabled"><i class="um-faicon-angle-double-left"></i></span>');
                      html.push('<span class="pagi pagi-arrow disabled"><i class="um-faicon-angle-left"></i></span>');
                      html.push('<span class="pagi current">1</span>');
                    }else{
                      html.push('<a data-paginate-id="1" href="?first_name&amp;display_name&amp;gender&amp;um_search=1&amp;members_page=1" class="pagi"><i class="um-faicon-angle-double-left"></i></a>');
                      html.push('<a data-paginate-id="'+(d.page-1)+'" href="?first_name&amp;display_name&amp;gender&amp;um_search=1&amp;members_page='+(d.page - 1)+'" class="pagi"><i class="um-faicon-angle-left"></i></a>');
                      html.push('<a data-paginate-id="'+dd+'" href="?first_name&amp;display_name&amp;gender&amp;um_search=1&amp;members_page='+dd+'" class="pagi">'+dd+'</a>');
                     }
                  }else {
                      if( d.page == dd ){
                          html.push('<span class="pagi current">'+dd+'</span>');
                      }else{
                          html.push('<a data-paginate-id="'+dd+'" href="?first_name&amp;display_name&amp;gender&amp;um_search=1&amp;members_page='+dd+'" class="pagi">'+dd+'</a>');
                      }
                  }
                  if((i+1) == d.pages_to_show.length  ){
                      if( d.page != d.pages_to_show[d.pages_to_show.length - 1] ){
                          html.push('<a data-paginate-id="'+dd+'" href="?first_name&amp;display_name&amp;gender&amp;um_search=1&amp;members_page='+(d.page + 1)+'" class="pagi"><i class="um-faicon-angle-right"></i></a>');
                          html.push('<a data-paginate-id="'+d.pages_to_show[d.pages_to_show.length - 1]+'" href="?first_name&amp;display_name&amp;gender&amp;um_search=1&amp;members_page='+d.pages_to_show[d.pages_to_show.length - 1]+'" class="pagi"><i class="um-faicon-angle-double-right"></i></a>');
                      }else{
                          html.push('<span class="pagi pagi-arrow disabled"><i class="um-faicon-angle-double-right"></i></span>');
                          html.push('<span class="pagi pagi-arrow disabled"><i class="um-faicon-angle-right"></i></span>');
                      }
                  }
                });
            }
               
            $paginate_wrap.html(html.join(''));
            AJAXIFY_UM.common.bind_paginate( $paginate_wrap );
   

        },
        bind_paginate: function( $elem ){
                 var $form = null;
                 var $umform = $(".um-search form ");
                 var $gform = $("#gform_1");

                if( $umform.length > 0 ){
                    $form = $umform;
                }else{
                    $form = $gform;
                }

            $('a',$elem).click(function(){
                var $page_id = getParameterByName('members_page', $(this).attr('href') );
                $('input[type=hidden][name=members_page]').val( $page_id );
                var search_param = $form.find('.um-search-filter input').serialize();
                AJAXIFY_UM.common.search_users( search_param );
                
                return false;
            });
            
        }
    }
};

// The routing fires all common scripts, followed by the page specific scripts.
// Add additional events for more control over timing e.g. a finalize event
var UTIL = {
  fire: function(func, funcname, args) {
    var namespace = AJAXIFY_UM;
    funcname = (funcname === undefined) ? 'init' : funcname;
    if (func !== '' && namespace[func] && typeof namespace[func][funcname] === 'function') {
      namespace[func][funcname](args);
    }
  },
  loadEvents: function() {
    UTIL.fire('common');

    $.each(document.body.className.replace(/-/g, '_').split(/\s+/),function(i,classnm) {
      UTIL.fire(classnm);
    });
  }
};
	
// Shuffle Array
$.fn.shuffle = function() {
    return this.each(function(){
        var items = $(this).children().clone(true);
        return (items.length) ? $(this).html($.shuffle(items)) : this;
    });
}
 
$.shuffle = function(arr) {
    for(var j, x, i = arr.length; i; j = parseInt(Math.random() * i), x = arr[--i], arr[i] = arr[j], arr[j] = x);
    return arr;
}

function getParameterByName(name, url) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(url);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

/**
 * ValidEmail 
 * @author Andrew Chalkley
 * @link  https://github.com/chalkers/validEmail
 */
 $.fn.validEmail = function(options) {
    options = options || {};
    var on = options.on;
    var success = options.success || (function(){});
    var failure = options.failure || (function(){});
    var testInitially = options.testInitially || false;

    var $input = $(this);

    function check($input) {
      if($input.is("input,textarea")) {
        var emailRegExp = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
        return emailRegExp.test($input.val());
      } else {
        return false;
      }

    }

    function applyCode($input) {
      check($input) ? success.call($input.get(0)) : failure.call($input.get(0));
    }
  
    if (typeof on === "string")
      $input.bind(on, function() { applyCode($(this)); });

    if (testInitially) $input.each(function() { applyCode($(this)); });
    return check($input);

  };

$(document).ready(UTIL.loadEvents);

})( jQuery );
