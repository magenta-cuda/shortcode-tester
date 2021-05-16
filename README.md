# shortcode-tester

![Screenshot](https://raw.githubusercontent.com/magenta-cuda/shortcode-tester/master/assets/both-70.png)

A more recent version for ClassicPress is available at https://github.com/basic-tech/classic-shortcode-tester.

The shortcode tester is a very simple post content editor tool for displaying the HTML generated from WordPress shortcodes in a popup window. To display the shortcode tester from the post editor page click on the Shortcode Tester button in the editor’s toolbar at the top of the page. (For the "Block Editor" it should be to the immediate right of the Block Navigation button. For the "Classic Editor" it should be to the immediate right of the "Add Media" button.)

"Block Editor": ![Toolbar](https://raw.githubusercontent.com/magenta-cuda/shortcode-tester/master/assets/toolbar-gutenberg.png)
"Classic Editor": ![Toolbar](https://raw.githubusercontent.com/magenta-cuda/shortcode-tester/master/assets/toolbar-classic.png)

Enter the shortcode into the left pane. (\[ gallery \] in this example.) Click on the Evaluate button. The HTML generated by the shortcode will be displayed in the right pane. Of course the shortcode is evaluated in the context of the post in the editor. The other buttons lets you hide one of the panes to give you a larger view of the other pane.

### Differences between the "Shortcode Tester" and WordPress evaluations of the shortcode
There is a tiny possibility that the environment that the shortcode tester uses to evaluate the shortcode will differ from the environment when the post is normally rendered. The shortcode tester sends a HTTP request for the post in editor to the server. The server processes this request normally until the time to render the HTML. Then a 'template_redirect' action renders the HTML using a special template that only renders the shortcode instead of rendering the entire post. Since, the normal template is not used, the filters and actions in the normal template will not be done. There is a small possibility that these filters and actions could have changed the environment in a way that affects the rendering of the shortcode.

### Notes on the implementation of the "Show Rendered" feature
The "Show Rendered" feature tries to hide non shortcode HTML elements by returning empty values for the data of some non shortcode HTML elements, setting the CSS display property to "none" and changing the ids of non shortcode elements. In order for CSS rules to be properly applied the parent containers of the HTML shortcode elements must be preserved. Other HTML elements may be hidden. The "Shortcode Tester" attempts to identify these "other" HTML elements by observing if these HTML elements were emitted as part of the header, a sidebar or the footer. Hence, the "Show Rendered" feature will not work if the current theme deviates from the WordPress framework for emitting the header, sidebars and the footer. In addition to the HTML emitted by the server PHP code, client JavaScript code may dynamically emit additional HTML elements. The "Shortcode Tester" tries to suppress client JavaScript code from emitting additional HTML elements for non shortcode HTML elements by changing their ids. Unfortunately these now missing HTML elements may sometimes cause the JavaScript code to abort.

## Frequently Asked Questions
### Error: Permalink not found. Please report this to the developer.
"The Shortcode Tester" uses permalinks. In particular, it will not work with posts or pages that have not been saved.

### "Show Rendered" shows more than the shortcode HTML elements.
"The Shortcode Tester" tries to hide HTML elements that are not part of the shortcode. It assumes that the current theme uses the standard WordPress framework for emitting the header, the content, sidebars and the footer ( actions/filters such as 'the_content', 'get_sidebar', 'get_footer', ... ). If the current theme deviates from this framework "The Shortcode Tester" will not be able to properly identify the header, sidebars and/or the footer.

### "Show Rendered" shows less than the shortcode HTML elements.
In addition to the HTML elements emitted by the server PHP code for the shortcode, the shortcode may have client JavaScript code that will dynamically emit additional HTML elements. The shortcode tester tries to hide non shortcode HTML elements by setting the CSS display property to none and changing the element id. These changes may affect the execution the client JavaScript code. In particular, the now missing HTML elements may cause the JavaScript code to abort.
