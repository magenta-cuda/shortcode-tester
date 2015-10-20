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

namespace {
        
    include_once ABSPATH . 'wp-admin/includes/plugin.php';
    
    $construct = function( ) {

        if ( is_admin( ) ) {
            
            # AJAX action 'wp_ajax_tpcti_eval_post_content' handles evaluation of HTML fragments from post content editor shortcode tester
            
            add_action( 'wp_ajax_tpcti_eval_post_content', function( ) {
                global $post;
                $save_post = $post;
                $post = get_post( $_POST[ 'post_id' ] );
                echo do_shortcode( stripslashes( $_POST[ 'post_content' ] ) );
                $post = $save_post;
                die;
            } );   # add_action( 'wp_ajax_tpcti_eval_post_content', function( ) {
         
            # things to do only on post.php and post-new.php admin pages

            $post_editor_actions = function( ) {

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
<div id="mf2tk-shortcode-tester" class="mf2tk-popup" style="display:none;">
    <h3>Shortcode Tester</h3>
    <button id="button-mf2tk-shortcode-tester-close">X</button>
    <div style="padding:0;margin:0;clear:both;">
        <div style="padding:0px 20px;margin:0;">
            Enter HTML and WordPress shortcodes in the Source text area.<br />
            Click the Evaluate button to display the generated HTML from WordPress shortcode processing in the Result text area.<br />
            <button id="mf2tk-shortcode-tester-evaluate" class="mf2tk-shortcode-tester-button">Evaluate</button>
            <button id="mf2tk-shortcode-tester-show-source" class="mf2tk-shortcode-tester-button">Show Source Only</button>
            <button id="mf2tk-shortcode-tester-show-result" class="mf2tk-shortcode-tester-button">Show Result Only</button>
            <button id="mf2tk-shortcode-tester-show-both" class="mf2tk-shortcode-tester-button">Show Both</button>
        </div>
        <div class="mf2tk-shortcode-tester-half">
            <div id="mf2tk-shortcode-tester-area-source" class="mf2tk-shortcode-tester-area">
                <h3>Source</h3>
                <textarea rows="12"></textarea>
            </div>
        </div>
        <div class="mf2tk-shortcode-tester-half">
            <div  id="mf2tk-shortcode-tester-area-result" class="mf2tk-shortcode-tester-area">
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
            
    };   # $construct = function( ) use ( &$mf2tk_the_do_macro ) {

    $construct( );
}
?>