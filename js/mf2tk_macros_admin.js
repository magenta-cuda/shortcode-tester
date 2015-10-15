jQuery(document).ready(function(){
    
    // showPopup() shows a centered popup element, inner and semi-opaque full browser window background element
    function showPopup(inner,outer){
        var windowWidth=jQuery(window).width();
        var windowHeight=jQuery(window).height();
        // background uses the full browser window
        outer.css({width:windowWidth,height:windowHeight});
        // popup uses 90% of the browser window
        var width=Math.floor(windowWidth*9/10);
        var height=Math.floor(windowHeight*9/10);
        // center the popup
        inner.css({width:width+"px",height:height+"px",left:Math.floor((windowWidth-width)/2)+"px",
            top:Math.floor((windowHeight-height)/2)+"px"});
        inner.css("display","block");
        outer.css("display","block");
    }
    // div#mf2tk-popup-outer is semi-opaque full browser window background used to surround the popup
    var divPopupOuter=jQuery("div#mf2tk-popup-outer");
    
    // Shortcode Tester popup
    
    // connect "Shortcode Tester" popup HTML elements to their JavaScript code
    var divShortcode=jQuery("div#mf2tk-shortcode-tester");
    // "Shortcode Tester" close button
    divShortcode.find("button#button-mf2tk-shortcode-tester-close").click(function(){
        divShortcode.css("display","none");
        divPopupOuter.css("display","none");
    });
    // "Shortcode Tester" evaluate button
    divShortcode.find("button#mf2tk-shortcode-tester-evaluate").click(function(){
        var post_id=jQuery("form#post input#post_ID[type='hidden']").val();
        var source=jQuery("div#mf2tk-shortcode-tester div#mf2tk-shortcode-tester-area-source textarea").val();
        jQuery("div#mf2tk-shortcode-tester div#mf2tk-shortcode-tester-area-result textarea").val("Evaluating..., please wait...");
        // Use AJAX to request the server to evaluate the post content fragment
        jQuery.post(ajaxurl,{action:'tpcti_eval_post_content',post_id:post_id,post_content:source},function(r){
            jQuery("div#mf2tk-shortcode-tester div#mf2tk-shortcode-tester-area-result textarea").val(r.trim());
        });
    });
    // "Shortcode Tester" show both source and result button
    divShortcode.find("button#mf2tk-shortcode-tester-show-both").click(function(){
        divShortcode.find("div.mf2tk-shortcode-tester-half")
            .css({display:"block",width:"50%",padding:"0",margin:"0",float:"left"})
    });
    // "Shortcode Tester" show source only button
    divShortcode.find("button#mf2tk-shortcode-tester-show-source").click(function(){
        divShortcode.find("div#mf2tk-shortcode-tester-area-source").parent()
            .css({display:"block",width:"99%",float:"none","margin-left":"auto","margin-right":"auto"});
        divShortcode.find("div#mf2tk-shortcode-tester-area-result").parent().css("display","none");
    });
    // "Shortcode Tester" show result only button
    divShortcode.find("button#mf2tk-shortcode-tester-show-result").click(function(){
        divShortcode.find("div#mf2tk-shortcode-tester-area-source").parent().css("display","none");
        divShortcode.find("div#mf2tk-shortcode-tester-area-result").parent()
            .css({display:"block",width:"99%",float:"none","margin-left":"auto","margin-right":"auto"});
    });
    // create the "Shortcode Tester" button
    var a=document.createElement("a");
    a.className="button";
    a.href="#";
    a.textContent="Shortcode Tester";
    jQuery("a#insert-media-button,button#insert-media-button").after(a);
    jQuery(a).click(function(){
        divShortcode.find("div#mf2tk-shortcode-tester-area-source textarea").val("");
        divShortcode.find("div#mf2tk-shortcode-tester-area-result textarea").val("");
        showPopup(divShortcode,divPopupOuter);
    });
});
