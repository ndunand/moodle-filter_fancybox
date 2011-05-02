<?php
    
defined('MOODLE_INTERNAL') || die();

class filter_fancybox extends moodle_text_filter {
    
    function filter($text, array $options = array()) {
        
        $search1   =  '/\<img(\s+)[^\>]*src="([^"]+)"(\s+)[^\>]*alt="([^"]+)__fancy(_(\d+))?"(\s+)[^\>]*\>/iU';
        $search2   =  '/\<img(\s+)[^\>]*alt="([^"]+)__fancy(_(\d+))?"(\s+)[^\>]*src="([^"]+)"(\s+)[^\>]*\>/iU';
        
        $search3   =  '/\<a(\s+)href="([^"]+)"(\s+)title="([^"]+)__fancy(_(\d+))?"(\s*)\>([^\<]+)\<\/a\>/iU';
        $search4   =  '/\<a(\s+)title="([^"]+)__fancy(_(\d+))?"(\s+)href="([^"]+)"(\s*)\>([^\<]+)\<\/a\>/iU';
        
        if (!$match1 = preg_match($search1, $text, $matches1) && !$match2 = preg_match($search2, $text, $matches2) && !$match3 = preg_match($search3, $text, $matches3) && !$match4 = preg_match($search4, $text, $matches4)) {
            return $text;
        }
        
        global $CFG;
        
        static $included_js = 0;
        $javascript = '';
        
        if (!$included_js) {
            $included_js++;
            $javascript = '
<script type="text/javascript" src="'.$CFG->wwwroot.'/filter/fancybox/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="'.$CFG->wwwroot.'/filter/fancybox/fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<script type="text/javascript" src="'.$CFG->wwwroot.'/filter/fancybox/jquery.easydrag.handler.beta2.js"></script>
<link rel="stylesheet" type="text/css" href="'.$CFG->wwwroot.'/filter/fancybox/fancybox/jquery.fancybox-1.3.1.css" />
<script type="text/javascript">
    jq_mdl_filter_fancy = jQuery.noConflict();
    (function($){
        $(document).ready(function(){
            $("div.fancybox.draggable a").fancybox({
                \'opacity\'             : true,
                \'zoomOpacity\'			: true,
                \'overlayShow\'			: false,
                \'autoScale\'			: false,
                \'titlePosition\'       : \'inside\',
                \'zoomSpeedIn\'			: 500,
                \'zoomSpeedOut\'        : 500,
                \'transitionIn\'        : \'elastic\',
                \'transitionOut\'       : \'elastic\',
                \'hideOnContentClick\'  : false,
                \'onComplete\'          : function(){ $("#fancybox-wrap").easydrag(); }
            });
            
            $(".fancybox img").each(function(){
                var $img = $(this);
                $.ajax({
                    url: $img.attr("src"),
                    error: function(data, status, xhr){
                        if (status==="error"){ 
                            $img.before($img.attr("alt")).hide();
                        }
                    }
                });
            });
            
        });
    })(jq_mdl_filter_fancy);
</script>
';
        }
        
        $original_text = $text;
        
        // pseudo-localization :
        $hint_str = (current_language() == 'fr_utf8') ? ('Agrandir...') : ('Enlarge...');
        $hint_str_2 = (current_language() == 'fr_utf8') ? ("Vous pouvez déplacer l'image à l'aide de la souris") : ('You can use the mouse to move the image frame');
        
        if (!empty($matches1[6]) || !empty($matches2[4]) || !empty($matches3[6]) || !empty($matches4[4])) {
        
            // we want a thumbnail :
            
            $replace1  =  '  <div class="fancybox draggable">
                                <a href="${2}" title="<strong>${4}</strong><br /><em>'.$hint_str_2.'</em>" >
                                    <img src="${2}" width="${6}" alt="${4}" title="${4}" />
                                </a>
                            </div>';
            
            $replace2  =  '  <div class="fancybox draggable">
                                <a href="${6}" title="<strong>${2}</strong><br /><em>'.$hint_str_2.'</em>" >
                                    <img src="${6}" width="${4}" alt="${2}" title="${2}" />
                                </a>
                            </div>';
            
            $replace3  =  '  <div class="fancybox draggable">
                                <a href="${2}" title="<strong>${4}</strong><br /><em>'.$hint_str_2.'</em>" >
                                    <img src="${2}" width="${6}" alt="${4}" title="${4}" />
                                </a>
                            </div>';
            
            $replace4  =  '  <div class="fancybox draggable">
                                <a href="${6}" title="<strong>${2}</strong><br /><em>'.$hint_str_2.'</em>" >
                                    <img src="${6}" width="${4}" alt="${2}" title="${2}" />
                                </a>
                            </div>';
            
        }
        else {
            
            // no thumbnail :
            
            $replace1  =  '  <div class="fancybox draggable">
                                <a href="${2}" title="<strong>${4}</strong><br /><em>'.$hint_str_2.'</em>" >${4}</a>
                            </div>';
            
            $replace2  =  '  <div class="fancybox draggable">
                                <a href="${4}" title="<strong>${2}</strong><br /><em>'.$hint_str_2.'</em>" >${2}</a>
                            </div>';
            
            $replace3  =  '  <div class="fancybox draggable">
                                <a href="${2}" title="<strong>${4}</strong><br /><em>'.$hint_str_2.'</em>" >${4}</a>
                            </div>';
            
            $replace4  =  '  <div class="fancybox draggable">
                                <a href="${4}" title="<strong>${2}</strong><br /><em>'.$hint_str_2.'</em>" >${2}</a>
                            </div>';
            
        }
        
        $text = preg_replace($search1, $replace1, $text);
        $text = preg_replace($search2, $replace2, $text);
        $text = preg_replace($search3, $replace3, $text);
        $text = preg_replace($search4, $replace4, $text);
        
        $text .= $javascript;
        
        return $text;
        
    }

}
    
?>