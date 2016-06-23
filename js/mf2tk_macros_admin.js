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
    divShortcode.find("button#mf2tk-shortcode-tester-evaluate,button#mf2tk-shortcode-tester-evaluate-and-prettify").click(function(){
        var prettify=this.id==="mf2tk-shortcode-tester-evaluate-and-prettify";
        var post_id=jQuery("form#post input#post_ID[type='hidden']").val();
        var source=jQuery("div#mf2tk-shortcode-tester div#mf2tk-shortcode-tester-area-source textarea").val();
        var button=jQuery("button#sct_ix-shortcode-tester")[0];
        jQuery("div#mf2tk-shortcode-tester div#mf2tk-shortcode-tester-area-result textarea").val("Evaluating..., please wait...");
        // Use AJAX to request the server to evaluate the post content fragment
        jQuery.post(ajaxurl,{action:'tpcti_eval_post_content',post_id:post_id,post_content:source,prettify:prettify,nonce:button.dataset.nonce},function(r){
            jQuery("div#mf2tk-shortcode-tester div#mf2tk-shortcode-tester-area-result textarea").val(r);
        });
    });
    // "Shortcode Tester" show both source and result button
    divShortcode.find("button#mf2tk-shortcode-tester-show-both").click(function(){
        divShortcode.find("div.sct_ix-shortcode_tester_half")
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
    // wire up the "Shortcode Tester" button
    jQuery("button#sct_ix-shortcode-tester").click(function(){
        divShortcode.find("div#mf2tk-shortcode-tester-area-source textarea").val("");
        divShortcode.find("div#mf2tk-shortcode-tester-area-result textarea").val("");
        divPopupOuter.show();
        divShortcode.show();
    });
});
