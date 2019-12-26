jQuery(document).ready(function(){
    
    // div#sct_ix-popup_margin is semi-opaque full browser window background used to surround the popup
    var divPopupOuter=jQuery("div#sct_ix-popup_margin");
    
    // Shortcode Tester popup
    
    // connect "Shortcode Tester" popup HTML elements to their JavaScript code
    var divShortcode=jQuery("div#mf2tk-shortcode-tester");
    // "Shortcode Tester" close button
    divShortcode.find("button#button-mf2tk-shortcode-tester-close").click(function(){
        divShortcode.hide();
        divPopupOuter.hide();
    });
    // "Shortcode Tester" evaluate button
    divShortcode.find("button#mf2tk-shortcode-tester-evaluate,button#mf2tk-shortcode-tester-evaluate-and-prettify,"
            +"button#mf2tk-shortcode-tester-show-rendered,button#mf2tk-shortcode-tester-alt-show-rendered").click(function(){
        var permalink=jQuery("#sample-permalink a");
        if(!permalink.length){
            // Gutenberg
            permalink=jQuery("#wp-admin-bar-view a");
            if(!permalink.length){
                permalink=jQuery("a.editor-post-preview");
                if(!permalink.length){
                    permalink=jQuery("a#sample-permalink");
                    if(!permalink.length){
                        window.alert("Error: Permalink not found. Please report this to the developer.");
                        return;
                    }
                }
            }
        }
        var button=jQuery("button#sct_ix-shortcode-tester");
        var nonce;
        if(button.length){
            nonce=button[0].dataset.nonce;
        }else{
            // Gutenberg
            nonce=mf2tk_macros_admin.shortcode_tester_nonce;
        }
        var url=permalink[0].href;
        var punc=url.indexOf("?")===-1?"?":"&";
        var source=jQuery("div#mf2tk-shortcode-tester div#mf2tk-shortcode-tester-area-source textarea").val();
        if(this.id==="mf2tk-shortcode-tester-show-rendered"||this.id==="mf2tk-shortcode-tester-alt-show-rendered"){
            var width=640;
            var height=480;
            var left=window.screen.width/2-width/2;
            var top=window.screen.height/2-height/2;
            var features="left="+left+",top="+top+",width="+width+",height="+height+",location=0,resizable,scrollbars,menubar=0";
            url+=punc+"mc-sct=tpcti_html_eval_post_content&post_content="+encodeURI(source);
            if(this.id==="mf2tk-shortcode-tester-alt-show-rendered"){
                url+="&theme_scripts=nullify";
            }
            window.open(url,"mc-sct-rendered",features);
            return;
        }
        var prettify=this.id==="mf2tk-shortcode-tester-evaluate-and-prettify";
        var post_id=jQuery("form#post input#post_ID[type='hidden']").val();
        url+=punc+"mc-sct=tpcti_eval_post_content";
        jQuery("div#mf2tk-shortcode-tester div#mf2tk-shortcode-tester-area-result textarea").val("Evaluating..., please wait...");
        // Use AJAX to request the server to evaluate the post content fragment
        // N.B. - This is not the usual WordPress .../wp-admin/adim-ajax.php AJAX request. The shortcode must be
        // evaluated in the context of the web page so this AJAX request is a request for the web page with additional
        // query parameters. These query parameters will cause a template redirect to code which will evaluate the
        // shortcode in the context of the web page instead of rendering the web page.
        jQuery.post(url,{action:'tpcti_eval_post_content',post_id:post_id,post_content:source,prettify:prettify,nonce:nonce},function(r){
            jQuery("div#mf2tk-shortcode-tester div#mf2tk-shortcode-tester-area-result textarea").val(r);
        });
    });
    // "Shortcode Tester" show both source and result button
    divShortcode.find("button#mf2tk-shortcode-tester-show-both").click(function(){
        divShortcode.find("div.sct_ix-shortcode_tester_half").removeClass("sct_ix-this_half_only").show();
        divShortcode.find("button#mf2tk-shortcode-tester-show-source,button#mf2tk-shortcode-tester-show-result").prop("disabled",false);
        jQuery(this).prop("disabled",true);
    });
    // "Shortcode Tester" show source only button
    divShortcode.find("button#mf2tk-shortcode-tester-show-source").click(function(){
        divShortcode.find("div#mf2tk-shortcode-tester-area-result").parent().removeClass("sct_ix-this_half_only").hide();
        divShortcode.find("div#mf2tk-shortcode-tester-area-source").parent().addClass("sct_ix-this_half_only").show();
        divShortcode.find("button#mf2tk-shortcode-tester-show-both,button#mf2tk-shortcode-tester-show-result").prop("disabled",false);
        jQuery(this).prop("disabled",true);
    });
    // "Shortcode Tester" show result only button
    divShortcode.find("button#mf2tk-shortcode-tester-show-result").click(function(){
        divShortcode.find("div#mf2tk-shortcode-tester-area-source").parent().removeClass("sct_ix-this_half_only").hide();
        divShortcode.find("div#mf2tk-shortcode-tester-area-result").parent().addClass("sct_ix-this_half_only").show();
        divShortcode.find("button#mf2tk-shortcode-tester-show-both,button#mf2tk-shortcode-tester-show-source").prop("disabled",false);
        jQuery(this).prop("disabled",true);
    });
    // wire up the "Shortcode Tester" button
    function showShortcodeTester(){
        divShortcode.find("button#mf2tk-shortcode-tester-show-both").prop("disabled",true);
        divShortcode.find("button#mf2tk-shortcode-tester-show-source,button#mf2tk-shortcode-tester-show-result").prop("disabled",false);
        divShortcode.find("div#mf2tk-shortcode-tester-area-source textarea").val("");
        divShortcode.find("div#mf2tk-shortcode-tester-area-result textarea").val("");
        divShortcode.find("div.sct_ix-shortcode_tester_half").removeClass("sct_ix-this_half_only").show();
        divPopupOuter.show();
        divShortcode.show();
    }
    var shortcodeTesterButton=jQuery("button#sct_ix-shortcode-tester");
    if(shortcodeTesterButton.length){
        shortcodeTesterButton.click(showShortcodeTester);
    }else{
        // Gutenberg
        var count=0;
        window.setTimeout(function addShortcodeTesterButton(){
            var toolbar=jQuery(".edit-post-header-toolbar");
            if(toolbar.length){
                toolbar.append('<button type="button" aria-label="Test Shortcode" aria-disabled="false" class="components-button components-icon-button sct_ix-shortcode-tester"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path d="M8.58 2.39c.32 0 .59.05.81.14 1.25.55 1.69 2.24 1.7 3.97.59-.82 2.15-2.29 3.41-2.29s2.94.73 3.53 3.55c-1.13-.65-2.42-.94-3.65-.94-1.26 0-2.45.32-3.29.89.4-.11.86-.16 1.33-.16 1.39 0 2.9.45 3.4 1.31.68 1.16.47 3.38-.76 4.14-.14-2.1-1.69-4.12-3.47-4.12-.44 0-.88.12-1.33.38C8 10.62 7 14.56 7 19H2c0-5.53 4.21-9.65 7.68-10.79-.56-.09-1.17-.15-1.82-.15C6.1 8.06 4.05 8.5 2 10c.76-2.96 2.78-4.1 4.69-4.1 1.25 0 2.45.5 3.2 1.29-.66-2.24-2.49-2.86-4.08-2.86-.8 0-1.55.16-2.05.35.91-1.29 3.31-2.29 4.82-2.29zM13 11.5c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5.67 1.5 1.5 1.5 1.5-.67 1.5-1.5z"/></svg></button>')
                    .find(".sct_ix-shortcode-tester").click(showShortcodeTester).hover(
                        function(e){
                            // Show tooltip
                            var target=jQuery(e.currentTarget);
                            var offset=target.offset();
                            var tooltip=jQuery(".sct_ix-shortcode_tester_tooltip");
                            var style=tooltip[0].style;
                            style.left=(offset.left+target.outerWidth()/2)+"px";
                            style.top=(offset.top+target.outerHeight()-8)+"px";
                            tooltip.show();
                        },
                        function(e){
                            // Hide tooltip
                            jQuery(".sct_ix-shortcode_tester_tooltip").hide();
                        }
                    );
            }else{
                if(++count<8){
                    window.setTimeout(addShortcodeTesterButton,250);
                }
            }
        },250);
    }
});
