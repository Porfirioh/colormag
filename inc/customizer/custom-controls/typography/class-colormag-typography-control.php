<?php
/**
 * Extend WP_Customize_Control to include typography control.
 *
 * Class ColorMag_Typography_Control
 *
 * @package    ThemeGrill
 * @subpackage ColorMag
 * @since      ColorMag 2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to extend WP_Customize_Control to add the typography customize control.
 *
 * Class ColorMag_Typography_Control
 */
class ColorMag_Typography_Control extends ColorMag_Customize_Base_Additional_Control {

	/**
	 * Control's Type.
	 *
	 * @var string
	 */
	public $type = 'colormag-typography';

	/**
	 * Languages required subsets.
	 *
	 * @var array
	 */
	public $languages = array();

	/**
	 * Enqueue control related scripts/styles.
	 */
	public function enqueue() {

		$standard_fonts   = $this->get_system_fonts();
		$google_fonts     = $this->get_google_fonts();
		$custom_fonts     = $this->get_custom_fonts();
		$localize_scripts = array(
			'standardfontslabel' => esc_html__( 'Standard Fonts', 'colormag' ),
			'googlefontslabel'   => esc_html__( 'Google Fonts', 'colormag' ),
			'standard'           => $standard_fonts,
			'google'             => $google_fonts,
		);

		// If custom fonts is available,then add it for localization.
		if ( ! empty( $custom_fonts ) ) {
			$localize_scripts['customfontslabel'] = esc_html__( 'Custom Fonts', 'colormag' );
			$localize_scripts['custom']           = $custom_fonts;
		}

		wp_localize_script(
			'colormag-customize-controls',
			'ColorMagCustomizerControlTypography',
			$localize_scripts
		);

	}

	/**
	 * Formats variants.
	 *
	 * @access protected
	 *
	 * @param array $variants The variants.
	 *
	 * @return array
	 */
	protected function format_variants_array( $variants ) {

		$font_variants  = ColorMag_Fonts::get_font_variants();
		$variants_array = array();

		foreach ( $variants as $variant ) {

			if ( is_string( $variant ) ) {
				$variants_array[] = array(
					'id'    => $variant,
					'label' => isset( $font_variants[ $variant ] ) ? $font_variants[ $variant ] : $variant,
				);
			} elseif ( is_array( $variant ) && isset( $variant['id'] ) && isset( $variant['label'] ) ) {
				$variants_array[] = $variant;
			}
		}

		return $variants_array;

	}

	/**
	 * Gets standard fonts properly formatted for control.
	 */
	public function get_system_fonts() {

		$standard_fonts       = ColorMag_Fonts::get_system_fonts();
		$standard_fonts_array = array();
		$default_variants     = $this->format_variants_array(
			array(
				'regular',
				'italic',
			)
		);

		foreach ( $standard_fonts as $key => $font ) {

			$standard_fonts_array[] = array(
				'family'   => $font['family'],
				'label'    => $font['label'],
				'subsets'  => array(),
				'variants' => ( isset( $font['variants'] ) ) ? $this->format_variants_array( $font['variants'] ) : $default_variants,
			);

		}

		return $standard_fonts_array;

	}

	/**
	 * Gets Google fonts properly formatted for control.
	 */
	public function get_google_fonts() {

		// Get formatted array of google fonts.
		$google_fonts          = ColorMag_Fonts::get_google_fonts();
		$font_variants         = ColorMag_Fonts::get_font_variants();
		$foogle_fonts__subsets = ColorMag_Fonts::get_google_font_subsets();
		$google_fonts_array    = array();

		foreach ( $google_fonts as $family => $args ) {

			// Get label, variants, subsets of individual font.
			$label    = ( isset( $args['label'] ) ) ? $args['label'] : $family;
			$variants = ( isset( $args['variants'] ) ) ? $args['variants'] : array( 'regular' );
			$subsets  = ( isset( $args['subsets'] ) ) ? $args['subsets'] : array();

			$available_variants = array();
			if ( is_array( $variants ) ) {
				foreach ( $variants as $variant ) {
					if ( array_key_exists( $variant, $font_variants ) ) {
						$available_variants[] = array(
							'id'    => $variant,
							'label' => $font_variants[ $variant ],
						);
					}
				}
			}

			$available_subsets = array();
			if ( is_array( $subsets ) ) {
				foreach ( $subsets as $subset ) {
					if ( array_key_exists( $subset, $foogle_fonts__subsets ) ) {
						$available_subsets[] = array(
							'id'    => $subset,
							'label' => $foogle_fonts__subsets[ $subset ],
						);
					}
				}
			}

			$google_fonts_array[] = array(
				'family'   => $family,
				'label'    => $label,
				'variants' => $available_variants,
				'subsets'  => $available_subsets,
			);

		}

		return $google_fonts_array;

	}

	/**
	 * Gets custom fonts properly formatted for control.
	 */
	public function get_custom_fonts() {

		$custom_fonts       = ColorMag_Fonts::get_custom_fonts();
		$custom_fonts_array = array();
		$default_variants   = $this->format_variants_array(
			array(
				'regular',
				'italic',
			)
		);

		foreach ( $custom_fonts as $key => $font ) {

			$custom_fonts_array[] = array(
				'family'   => $font['family'],
				'label'    => $font['label'],
				'subsets'  => array(),
				'variants' => ( isset( $font['variants'] ) ) ? $this->format_variants_array( $font['variants'] ) : $default_variants,
			);

		}

		return $custom_fonts_array;

	}

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @see WP_Customize_Control::to_json()
	 */
	public function to_json() {

		parent::to_json();

		$this->json['default'] = $this->setting->default;
		if ( isset( $this->default ) ) {
			$this->json['default'] = $this->default;
		}
		$this->json['value'] = $this->value();

		$this->json['link']        = $this->get_link();
		$this->json['id']          = $this->id;
		$this->json['label']       = esc_html( $this->label );
		$this->json['description'] = $this->description;

		$this->json['choices']   = $this->choices;
		$this->json['languages'] = ColorMag_Fonts::get_google_font_subsets();

	}

	/**
	 * An Underscore (JS) template for this control's content (but not its container).
	 *
	 * Class variables for this control class are available in the `data` JS object;
	 * export custom variables by overriding {@see WP_Customize_Control::to_json()}.
	 *
	 * @see    WP_Customize_Control::print_template()
	 *
	 * @access protected
	 */
	protected function content_template() {
		?>

		<div class="customizer-text">
			<# if ( data.label ) { #>
			<span class="customize-control-title">{{{ data.label }}}</span>
			<# } #>

			<# if ( data.description ) { #>
			<span class="description customize-control-description">{{{ data.description }}}</span>
			<# } #>
		</div>

		<div class="customize-control-content">

			<# if ( data.default['font-family'] ) { #>

			<div class="font-family">
				<span class="customize-control-title"><?php esc_html_e( 'Family', 'colormag' ); ?></span>
				<div class="colormag-field-content">
					<select {{{ data.inputAttrs }}} id="colormag-font-family-{{{ data.id }}}"></select>
				</div>
			</div>

			<# if ( data.default['font-weight'] ) { #>
			<div class="font-weight">
				<span class="customize-control-title"><?php esc_html_e( 'Weight', 'colormag' ); ?></span>
				<div class="colormag-field-content">
					<select {{{ data.inputAttrs }}} id="colormag-font-weight-{{{ data.id }}}"></select>
				</div>
			</div>
			<# } #>

			<# if ( data.default['subsets'] ) { #>
			<div class="subsets">
				<span class="customize-control-title"><?php esc_html_e( 'Subset(s)', 'colormag' ); ?></span>
				<div class="colormag-field-content">
					<select {{{ data.inputAttrs }}} id="colormag-subsets-{{{ data.id }}}"
					<# if ( _.isUndefined( data.choices['disable-multiple-variants'] ) || false ===
					data.choices['disable-multiple-variants'] ) { #> multiple<# } #>>
					<# _.each( data.value.subsets, function( subset ) { #>
					<option value="{{ subset }}" selected="selected">{{ data.languages[ subset ] }}</option>
					<# } ); #>
					</select>
				</div>
			</div>
			<# } #>

			<# } #>

			<# if ( data.default['font-size'] ) { #>
			<ul class="responsive-switchers">
				<li class="desktop">
					<button type="button" class="preview-desktop active" data-device="desktop">
						<i class="dashicons dashicons-desktop"></i>
					</button>
				</li>
				<li class="tablet">
					<button type="button" class="preview-tablet" data-device="tablet">
						<i class="dashicons dashicons-tablet"></i>
					</button>
				</li>
				<li class="mobile">
					<button type="button" class="preview-mobile" data-device="mobile">
						<i class="dashicons dashicons-smartphone"></i>
					</button>
				</li>
			</ul>
			<# } #>

			<# if ( data.default['font-style'] ) { #>
			<div class="font-style">
				<span class="customize-control-title"><?php esc_html_e( 'Style', 'colormag' ); ?></span>
				<div class="colormag-field-content">
					<select {{{ data.inputAttrs }}} id="colormag-font-style-{{{ data.id }}}">
						<option value="normal"
						<# if ( 'normal' === data.value['font-style'] ) { #> selected <# }
						#>><?php esc_attr_e( 'Normal', 'colormag' ); ?></option>
						<option value="italic"
						<# if ( 'italic' === data.value['font-style'] ) { #> selected <# }
						#>><?php esc_attr_e( 'Italic', 'colormag' ); ?></option>
						<option value="oblique"
						<# if ( 'oblique' === data.value['font-style'] ) { #> selected <# }
						#>><?php esc_attr_e( 'Oblique', 'colormag' ); ?></option>
						<option value="initial"
						<# if ( 'initial' === data.value['font-style'] ) { #> selected <# }
						#>><?php esc_attr_e( 'Initial', 'colormag' ); ?></option>
						<option value="inherit"
						<# if ( 'inherit' === data.value['font-style'] ) { #> selected <# }
						#>><?php esc_attr_e( 'Inherit', 'colormag' ); ?></option>
					</select>
				</div>
			</div>
			<# } #>

			<# if ( data.default['text-transform'] ) { #>
			<div class="text-transform">
				<span class="customize-control-title"><?php esc_html_e( 'Transform', 'colormag' ); ?></span>
				<div class="colormag-field-content">
					<select {{{ data.inputAttrs }}} id="colormag-text-transform-{{{ data.id }}}">
						<option value="none"
						<# if ( 'none' === data.value['text-transform'] ) { #> selected <# }
						#>><?php esc_attr_e( 'None', 'colormag' ); ?></option>
						<option value="capitalize"
						<# if ( 'capitalize' === data.value['text-transform'] ) { #> selected <# }
						#>><?php esc_attr_e( 'Capitalize', 'colormag' ); ?></option>
						<option value="uppercase"
						<# if ( 'uppercase' === data.value['text-transform'] ) { #> selected <# }
						#>><?php esc_attr_e( 'Uppercase', 'colormag' ); ?></option>
						<option value="lowercase"
						<# if ( 'lowercase' === data.value['text-transform'] ) { #> selected <# }
						#>><?php esc_attr_e( 'Lowercase', 'colormag' ); ?></option>
						<option value="initial"
						<# if ( 'initial' === data.value['text-transform'] ) { #> selected <# }
						#>><?php esc_attr_e( 'Initial', 'colormag' ); ?></option>
						<option value="inherit"
						<# if ( 'inherit' === data.value['text-transform'] ) { #> selected <# }
						#>><?php esc_attr_e( 'Inherit', 'colormag' ); ?></option>
					</select>
				</div>
			</div>
			<# } #>

			<# if ( data.default['text-decoration'] ) { #>
			<div class="text-decoration">
				<span class="customize-control-title"><?php esc_html_e( 'Decoration', 'colormag' ); ?></span>
				<div class="colormag-field-content">
					<select {{{ data.inputAttrs }}} id="colormag-text-decoration-{{{ data.id }}}">
						<option value="none"
						<# if ( 'none' === data.value['text-decoration'] ) { #> selected <# }
						#>><?php esc_attr_e( 'None', 'colormag' ); ?></option>
						<option value="underline"
						<# if ( 'underline' === data.value['text-decoration'] ) { #> selected <# }
						#>><?php esc_attr_e( 'Underline', 'colormag' ); ?></option>
						<option value="overline"
						<# if ( 'overline' === data.value['text-decoration'] ) { #> selected <# }
						#>><?php esc_attr_e( 'Overline', 'colormag' ); ?></option>
						<option value="line-through"
						<# if ( 'line-through' === data.value['text-decoration'] ) { #> selected <# }
						#>><?php esc_attr_e( 'Line Through', 'colormag' ); ?></option>
						<option value="initial"
						<# if ( 'initial' === data.value['text-decoration'] ) { #> selected <# }
						#>><?php esc_attr_e( 'Initial', 'colormag' ); ?></option>
						<option value="inherit"
						<# if ( 'inherit' === data.value['text-decoration'] ) { #> selected <# }
						#>><?php esc_attr_e( 'Inherit', 'colormag' ); ?></option>
					</select>
				</div>
			</div>
			<# } #>

			<input class="typography-hidden-value"
			       value="{{ JSON.stringify( data.value ) }}"
			       type="hidden" {{{ data.link }}}
			>

		</div>

		<?php
	}

	/**
	 * Don't render the control content from PHP, as it's rendered via JS on load.
	 */
	public function render_content() {
	}

}
