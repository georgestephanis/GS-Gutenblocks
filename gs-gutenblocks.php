<?php

/**
 * Plugin name: Gutenblocks
 */

add_action( 'init', array( 'GS_Gutenblocks', 'register_block_type' ) );
add_action( 'enqueue_block_editor_assets', array( 'GS_Gutenblocks', 'enqueue_block_editor_assets' ) );

class GS_Gutenblocks {

	/**
	 * This is a hacky way to simplify building your first block with only one file. Please
	 * don't do it in production.
	 *
	 * You should enqueue your file normally.  This just suffices to get it loaded in the
	 * footer with other enqueued scripts by adding the actual script as data to a dummy
	 * script stub.
	 */
	static public function enqueue_block_editor_assets() {
		wp_register_script(
			'gs-gutenblocks',
			null,
			array( 'wp-blocks', 'wp-i18n', 'wp-element', 'shortcode' )
		);
		wp_enqueue_script( 'gs-gutenblocks' );

		ob_start();
		self::gs_gutenblocks_script();
		$content = ob_get_clean();

		wp_script_add_data( 'gs-gutenblocks', 'data', $content );
	}

	/**
	 * This will register the block type for rendering on front-end output, similar
	 * to how a Shortcode would get transformed on display.
	 */
	static public function register_block_type() {
		register_block_type( 'gs-gutenblocks/button', array(
			'render_callback' => array( 'GS_Gutenblocks', 'render_callback' ),
		) );
	}

	/**
	 * This `render_callback` operates similarly to how a Shortcode would get
	 * transformed on display.
	 *
	 * @param $attributes
	 * @return string
	 */
	static public function render_callback( $attributes ) {
		return '<pre>' . print_r( $attributes, true ) . '</pre>';
	}

	/**
	 * This should be in its own .js file.  It's just here for temporary
	 * convenience and simplification while learning.
	 *
	 * The commented out `<script>` tags are just here to trick PHPStorm
	 * into syntax highlighting the contents as javascript.
	 */
	static public function gs_gutenblocks_script() {
		?>
		// <script>
			( function( wp ) {
				wp.blocks.registerBlockType( 'gs-gutenblocks/button', {
					/**
					 * Pick a name, any name!
					 */
					title : '<?php echo esc_js( __( 'GS Button', 'gs-gutenblocks' ) ); ?>',

					/**
					 * The `icon` is by default a dashicon.  Just remove the `dashicons-` prefix.
					 *
					 * @see https://developer.wordpress.org/resource/dashicons/
					 */
					icon : 'image-filter',

					/**
					 * Default categories include: `common` `formatting` `layout` `widgets`
					 */
					category : 'common',

					/**
					 * This array describes the attributes of the block that we care about.
					 */
					attributes : {
						label : {
							/**
							 * `type` can be anything of `string` `number` `boolean` `array` etc.
							 */
							type : 'string',
							/**
							 * `default` is the default value of the key.  Not too much to understand here.
							 */
							default : '<?php echo esc_js( __( 'Default Label', 'gs-gutenblocks' ) ); ?>'
							/**
							 * Other available keys:
							 *
							 * `value` would get a `source` function to parse its value from the content.
							 * @see http://gutenberg-devdoc.surge.sh/blocks/introducing-attributes-and-editable-fields/attribute-sources.md
							 */
						}
					},

					/**
					 * This method generates the edit form for the block in the Gutenberg
					 * Editor, and handles updating the attribute.
					 *
					 * @returns {Array} of {WPElement}s
					 */
					edit : function( props ) {
						/**
						 * We have to declare the function to handle the change first, to
						 * avoid some warnings from React.
						 *
						 * @see https://facebook.github.io/react/docs/forms.html#controlled-components
						 */
						function handleLabelChange( event ) {
							props.setAttributes({
								label : event.target.value
							});
						}

						/**
						 * This handles the rendering.  We can add more rendered parts to the array
						 * of type `BlockControls` or `InspectorControls` to let us render block
						 * alignment toolbars or extra settings fields in the Block Inspector
						 * respectively.
						 *
						 * They would look something like this:
						 *
						 * ```
						 *     !! props.focus && wp.element.createElement(
						 *         wp.blocks.BlockControls,
						 *         { key : 'controls' },
						 *         wp.element.createElement(
						 *             // stuff
						 *         )
						 *     ),
						 * ```
						 *
						 * The initial `!! props.focus &&` short circuits so that the latter part
						 * of the conditional (that actually builds the relevant controls) never
						 * evaluates unless the block itself has focus.
						 */
						return [
							wp.element.createElement(
								'input',
								{
									/**
									 * Everything needs a key.  Because of reasons.
									 */
									key : 'gs-gutenblocks/button/label',
									type : 'text',
									onChange : handleLabelChange,
									value : props.attributes.label
								}
							)
						];
					},

					/**
					 * By returning `null` here, the block is stored as just a html comment,
					 * with no content markup.
					 *
					 * This lets us use `register_block_type()` in PHP to specify a
					 * `render_callback` that gets passed the specified attributes,
					 * similar to how shortcodes work.
					 *
					 * Alternately, we could return either a WPElement or even just a string to save instead.
					 *
					 * @returns {null|WPElement}
					 */
					save : function() {
						return null;
					}

				} );
			} )( window.wp );
			// </script>
		<?php
	}
}
