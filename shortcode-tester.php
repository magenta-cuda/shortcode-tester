<?php

/*
 * Description:   A Shortcode Tester
 * Documentation: http://shortcodetester.wordpress.com/
 * Author:        Magenta Cuda
 * License:       GPL2
 */

/*  Copyright 2013  Magenta Cuda

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
    Project IX: Shortcode Tester
    
    The Shortcode Tester is a post editor tool for WordPress developers that displays in a popup window the HTML generated by WordPress shortcodes,
    i.e. so you can quickly view the generated HTML without having to view the entire post. It is actually just the diagnostic part of Project III:
    A Tiny Post Content Template Interpreter. However, since it is generally useful I have separated into its own plugin.
*/

namespace mc_shortcode_tester {
        
    $construct = function( ) {

        if ( !is_admin( ) ) {
            
            # a 'template_redirect' handles evaluation of HTML fragments from post content editor shortcode tester
            # using a 'template_redirect' insures we have the correct context for evaluating shortcodes
            
            add_action( 'template_redirect', function( ) {
                global $post;
                if ( empty( $_GET[ 'mc-sct' ] ) || $_GET[ 'mc-sct' ] !== 'tpcti_eval_post_content' ) {
                    return;
                }
                if ( !wp_verify_nonce( $_REQUEST[ 'nonce' ], 'sct_ix-shortcode_tester_nonce' ) ) {
                    wp_nonce_ays( '' );
                }
                setup_postdata( $post );
                # instead of showing the post we evaluate the sent content in the context of the post
                $html = do_shortcode( stripslashes( $_REQUEST[ 'post_content' ] ) );
                if ( !empty( $_REQUEST[ 'prettify' ] ) && $_REQUEST[ 'prettify' ] === 'true' ) {
                    #$html = str_replace( ' ', '#', $html );
                    #$html = str_replace( "\t", 'X', $html );
                    $html = preg_replace( '#>\s+<#', '><', $html );
                    #echo $html;
                    #die;
                    # DOMDocument doesn't understand some HTML5 tags, e.g. figure so
                    libxml_use_internal_errors( TRUE );
                    $dom = new \DOMDocument( );
                    $dom->preserveWhiteSpace = FALSE;
                    $dom->loadHTML( $html );
                    $dom->normalizeDocument( );
                    $dom->formatOutput = TRUE;
                    # saveHTML( ) doesn't format but saveXML( ) does. Why? see http://stackoverflow.com/questions/768215/php-pretty-print-html-not-tidy
                    $html = $dom->saveXML( $dom->documentElement );
                    # remove the <html> and <body> elements that were added by saveHTML( )
                    $html = preg_replace( [ '#^.*<body>\r?\n#s', '#</body>.*$#s' ], '', $html );
                    #$html = str_replace( ' ', '#', $html );
                    #$html = str_replace( "\t", 'X', $html );
                }
                echo $html;
                die;
            } );   # add_action( 'template_redirect', function( ) {

        } else {
       
            # things to do only on post.php and post-new.php admin pages

            $post_editor_actions = function( ) {

                add_action( 'media_buttons', function( ) {
                   $nonce = wp_create_nonce( 'sct_ix-shortcode_tester_nonce' );
?>
<button class="button" type="button" id="sct_ix-shortcode-tester" data-nonce="<?php echo $nonce; ?>">Shortcode Tester</button>
<?php
                } );

                add_action( 'admin_enqueue_scripts', function( $hook ) {
                    if ( $hook !== 'post.php' && $hook !== 'post-new.php' ) {
                        return;
                    }
                    wp_enqueue_style(  'mf2tk_macros_admin', plugins_url( 'css/mf2tk_macros_admin.css', __FILE__ ) );
                    wp_enqueue_script( 'mf2tk_macros_admin', plugins_url(  'js/mf2tk_macros_admin.js',  __FILE__ ), [ 'jquery' ] );
                } );

                # $shortcode_tester() outputs the HTML generated by WordPress shortcodes for the "Shortcode Tester" popup

                $shortcode_tester = function( ) {
?>
<!-- start shortcode tester -->
<div id="sct_ix-popup_margin" style="display:none;"></div>
<div id="mf2tk-shortcode-tester" class="sct_ix-popup" style="display:none;">
    <div class="sct_ix-heading">
        <h3>Shortcode Tester</h3>
        <button id="button-mf2tk-shortcode-tester-close">X</button>
    </div>
    <div class="sct_ix-instructions">
        Enter HTML and WordPress shortcodes in the Source text area.<br />
        Click the Evaluate button to display the generated HTML from WordPress shortcode processing in the Result text area.
    </div>
    <div class="sct_ix-button_bar">
        <button id="mf2tk-shortcode-tester-evaluate" class="mf2tk-shortcode-tester-button">Evaluate</button>
        <button id="mf2tk-shortcode-tester-evaluate-and-prettify" class="mf2tk-shortcode-tester-button">Evaluate & Prettify</button>
        <button id="mf2tk-shortcode-tester-show-source" class="mf2tk-shortcode-tester-button">Show Source Only</button>
        <button id="mf2tk-shortcode-tester-show-result" class="mf2tk-shortcode-tester-button">Show Result Only</button>
        <button id="mf2tk-shortcode-tester-show-both" class="mf2tk-shortcode-tester-button">Show Both</button>
    </div>
    <div class="sct_ix-shortcode_tester_input_output">
        <div class="sct_ix-shortcode_tester_half">
            <div id="mf2tk-shortcode-tester-area-source" class="sct_ix-shortcode_tester_area">
                <h3>Source</h3>
                <textarea rows="12"></textarea>
            </div>
        </div>
        <div class="sct_ix-shortcode_tester_half">
            <div  id="mf2tk-shortcode-tester-area-result" class="sct_ix-shortcode_tester_area">
                <h3>Result</h3>
                <textarea rows="12" readonly></textarea>
            </div>
        </div>
    </div>
</div>
<!-- end shortcode tester -->
<?php
                };   # $shortcode_tester = function( ) {

                # the "Insert Template" and "Shortcode Tester" are only injected on post.php and post-new.php admin pages
                add_action( 'admin_footer-post.php',     $shortcode_tester );
                add_action( 'admin_footer-post-new.php', $shortcode_tester );
                
            };   # $post_editor_actions = function( ) {
                
            add_action( 'load-post-new.php', $post_editor_actions );
            add_action( 'load-post.php',     $post_editor_actions );
            
        }   # if ( is_admin( ) ) {
            
    };   # $construct = function( ) {

    $construct( );
}
?>