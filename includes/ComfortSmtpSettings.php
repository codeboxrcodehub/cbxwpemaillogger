<?php

namespace Comfort\Crm\Smtp;

/**
 * weDevs Settings API wrapper class
 *
 * @version 1.1
 *
 * @author  Tareq Hasan <tareq@weDevs.com>
 * @link    http://tareq.weDevs.com Tareq's Planet
 * @example src/settings-api.php How to use the class
 * Further modified by codeboxr.com team
 */
class ComfortSmtpSettings {
	/**
	 * settings sections array
	 *
	 * @var array
	 */
	private $settings_sections = [];

	/**
	 * Settings fields array
	 *
	 * @var array
	 */
	private $settings_fields = [];

	/**
	 * Singleton instance
	 *
	 * @var object
	 */
	private static $_instance;

	/**
     * return instance
     *
	 * @return object|self
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}//end method instance

	/**
	 * Cloning is forbidden.
	 *
	 * @since 2.1
	 */
	public function __clone() {
		comfortsmtp_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning is forbidden.', 'cbxwpemaillogger' ), '2.0.4' );
	}//end method clone

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 2.1
	 */
	public function __wakeup() {
		comfortsmtp_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of this class is forbidden.', 'cbxwpemaillogger' ), '2.0.4' );
	}//end method wakeup

	public function __construct() {

	}

	/**
	 * Set settings sections
	 *
	 * @param $sections
	 *
	 * @return $this
	 */
	function set_sections( $sections ) {
		$this->settings_sections = $sections;

		return $this;
	}

	/**
	 * Add a single section
	 *
	 * @param $section
	 *
	 * @return $this
	 */
	function add_section( $section ) {
		$this->settings_sections[] = $section;

		return $this;
	}

	/**
	 * Set settings fields
	 *
	 * @param $fields
	 *
	 * @return $this
	 */
	function set_fields( $fields ) {
		$this->settings_fields = $fields;

		return $this;
	}

	function add_field( $section, $field ) {
		$defaults = [
			'name'  => '',
			'label' => '',
			'desc'  => '',
			'type'  => 'text'
		];

		$arg                                 = wp_parse_args( $field, $defaults );
		$this->settings_fields[ $section ][] = $arg;

		return $this;
	} //end add_field


	function admin_init() {

		//register settings sections
		foreach ( $this->settings_sections as $section ) {

			if ( false == get_option( $section['id'] ) ) {
				$section_default_value = $this->getDefaultValueBySection( $section['id'] );
				add_option( $section['id'], $section_default_value );
			} else {
				$section_default_value = $this->getMissingDefaultValueBySection( $section['id'] );
				update_option( $section['id'], $section_default_value );
			}

			if ( isset( $section['desc'] ) && ! empty( $section['desc'] ) ) {
				$section['desc'] = '<div class="inside">' . $section['desc'] . '</div>';
				$callback        = function () use ( $section ) {
					echo str_replace( '"', '\"', $section['desc'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				};
			} elseif ( isset( $section['callback'] ) ) {
				$callback = $section['callback'];
			} else {
				$callback = null;
			}

			add_settings_section( $section['id'], $section['title'], $callback, $section['id'] );
		}

		//register settings fields
		foreach ( $this->settings_fields as $section => $field ) {
			foreach ( $field as $option ) {

				$name     = $option['name'];
				$type     = isset( $option['type'] ) ? $option['type'] : 'text';
				$label    = isset( $option['label'] ) ? $option['label'] : '';
				$callback = isset( $option['callback'] ) ? $option['callback'] : [ $this, 'callback_' . $type ];

				$label_for = $this->settings_clean_label_for( "{$section}_{$option['name']}" );

				$args = [
					'id'                => $option['name'],
					'class'             => isset( $option['class'] ) ? $option['class'] : $name,
					'label_for'         => $label_for,
					'desc'              => isset( $option['desc'] ) ? $option['desc'] : '',
					'name'              => $label,
					'section'           => $section,
					'size'              => isset( $option['size'] ) ? $option['size'] : null,
					'min'               => isset( $option['min'] ) ? $option['min'] : '',
					'max'               => isset( $option['max'] ) ? $option['max'] : '',
					'step'              => isset( $option['step'] ) ? $option['step'] : '',
					'options'           => isset( $option['options'] ) ? $option['options'] : '',
					'default'           => isset( $option['default'] ) ? $option['default'] : '',
					'sanitize_callback' => isset( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : '',
					'placeholder'       => isset( $option['placeholder'] ) ? $option['placeholder'] : '',
					'type'              => $type,
					'optgroup'          => isset( $option['optgroup'] ) ? absint( $option['optgroup'] ) : 0,
					'multi'             => isset( $option['multi'] ) ? absint( $option['multi'] ) : 0,
					'fields'            => isset( $option['fields'] ) ? $option['fields'] : [],
					'sortable'          => isset( $option['sortable'] ) ? absint( $option['sortable'] ) : 0,
					'allow_new'         => isset( $option['allow_new'] ) ? absint( $option['allow_new'] ) : 0,
					//only works for repeatable
					'allow_clear'       => isset( $option['allow_clear'] ) ? intval( $option['allow_clear'] ) : 0,
					//for select2
					'check_content'     => isset( $option['check_content'] ) ? $option['check_content'] : '',
					'inline'            => isset( $option['inline'] ) ? absint( $option['inline'] ) : 1,
				];

				add_settings_field( "{$section}[{$name}]", $label, $callback, $section, $section, $args );
			}
		}

		// creates our settings in the options table
		foreach ( $this->settings_sections as $section ) {
			register_setting( $section['id'], $section['id'], [ $this, 'sanitize_options' ] );
		}
	} //end admin_init

	/**
	 * Prepares default values by section
	 *
	 * @param $section_id
	 *
	 * @return array
	 */
	function getDefaultValueBySection( $section_id ) {
		$default_values = [];

		$fields = $this->settings_fields[ $section_id ];
		foreach ( $fields as $field ) {
			$default_values[ $field['name'] ] = isset( $field['default'] ) ? $field['default'] : '';
		}

		return $default_values;
	} //end getDefaultValueBySection

	/**
	 * Prepares default values by section
	 *
	 * @param $section_id
	 *
	 * @return array
	 */
	function getMissingDefaultValueBySection( $section_id ) {
		$section_value = get_option( $section_id );
		$fields        = $this->settings_fields[ $section_id ];

		foreach ( $fields as $field ) {
			if ( ! isset( $section_value[ $field['name'] ] ) ) {
				$section_value[ $field['name'] ] = isset( $field['default'] ) ? $field['default'] : '';
			}

		}

		return $section_value;
	} //end getMissingDefaultValueBySection

	/**
	 * Get field description for display
	 *
	 * @param $args
	 *
	 * @return string
	 */
	public function get_field_description( $args ) {
		if ( ! empty( $args['desc'] ) ) {
			$desc = sprintf( '<div class="description mt-10">%s</div>', $args['desc'] );
		} else {
			$desc = '';
		}

		return $desc;
	} //end get_field_description

	/**
	 * Displays a textarea for a settings field
	 *
	 * @param array $args
	 * @param $value
	 *
	 * @return void
	 */
	function callback_html( $args, $value = null ) {
		echo $this->get_field_description( $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} //end method callback_html

	/**
	 * Displays heading field using h3
	 *
	 * @param array $args
	 *
	 * @return void
	 */
	function callback_heading( $args ) {
		$plus_svg  = comfortsmtp_esc_svg( comfortsmtp_load_svg( 'icon_plus' ) );
		$minus_svg = comfortsmtp_esc_svg( comfortsmtp_load_svg( 'icon_minus' ) );

		$html = '<h3 class="setting_heading"><span class="setting_heading_title">' . esc_html( $args['name'] ) . '</span><a title="' . esc_attr__( 'Click to show hide',
				'cbxwpemaillogger' ) . '" class="setting_heading_toggle button outline primary icon icon-only icon-inline" href="#"><i class="cbx-icon setting_heading_toggle_plus">' . $plus_svg . '</i><i class="cbx-icon setting_heading_toggle_minus">' . $minus_svg . '</i></a></h3>';
		$html .= $this->get_field_description( $args );

		echo $html;//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}//end method callback_heading


	/**
	 * Displays sub heading field using h4
	 *
	 * @param array $args
	 *
	 * @return void
	 */
	function callback_subheading( $args ) {
		$html = '<h4 class="setting_subheading">' . $args['name'] . '</h4>';
		$html .= $this->get_field_description( $args );

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} //end method callback_subheading


	/**
	 * Displays a text field for a settings field
	 *
	 * @param array $args
	 * @param $value
	 *
	 * @return void
	 */
	function callback_text( $args, $value = null ) {
		if ( $value === null ) {
			$value = esc_attr( $this->get_field( $args['id'], $args['section'], $args['default'] ) );
		}
		$size = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
		$type = isset( $args['type'] ) ? $args['type'] : 'text';

		$html_id = "{$args['section']}_{$args['id']}";
		$html_id = $this->settings_clean_label_for( $html_id );

		$html = sprintf( '<input autocomplete="none" onfocus="this.removeAttribute(\'readonly\');" readonly type="%1$s" class="%2$s-text" id="%6$s" name="%3$s[%4$s]" value="%5$s"/>',
			$type, $size, $args['section'], $args['id'], $value, $html_id );
		$html .= $this->get_field_description( $args );

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} //end callback_text

	/**
	 * Displays an url field for a settings field
	 *
	 * @param array $args
	 * @param null $value
	 *
	 * @return void
	 */
	function callback_url( $args, $value = null ) {
		$this->callback_text( $args, $value );
	} //end method callback_url

	/**
	 * Displays a number field for a settings field
	 *
	 * @param array $args
	 *
	 * @return void
	 */
	function callback_number( $args, $value = null ) {
		if ( $value === null ) {
			$value = esc_attr( $this->get_field( $args['id'], $args['section'], $args['default'] ) );
		}

		$size        = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
		$type        = isset( $args['type'] ) ? $args['type'] : 'number';
		$placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . $args['placeholder'] . '"';
		$min         = empty( $args['min'] ) ? '' : ' min="' . $args['min'] . '"';
		$max         = empty( $args['max'] ) ? '' : ' max="' . $args['max'] . '"';
		$step        = empty( $args['max'] ) ? ' step="1" ' : ' step="' . $args['step'] . '" ';

		$html_id = "{$args['section']}_{$args['id']}";
		$html_id = $this->settings_clean_label_for( $html_id );

		$html = sprintf( '<input type="%1$s" class="no-spinners %2$s-number" id="%10$s" name="%3$s[%4$s]" value="%5$s"%6$s%7$s%8$s%9$s/>',
			$type, $size, $args['section'], $args['id'], $value, $placeholder, $min, $max, $step, $html_id );
		$html .= $this->get_field_description( $args );
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} //end method callback_number

	/**
	 * Displays a multicheckbox a settings field
	 *
	 * @param array $args
	 * @param $value
	 *
	 * @return void
	 */
	function callback_radio( $args, $value = null ) {
		if ( $value === null ) {
			$value = $this->get_field( $args['id'], $args['section'], $args['default'] );
		}

		$display_inline       = isset( $args['inline'] ) ? absint( $args['inline'] ) : 1;
		$display_inline_class = ( $display_inline ) ? 'radio_fields_inline' : '';

		$html = '<div class="radio_fields magic_radio_fields ' . esc_attr( $display_inline_class ) . '">';

		foreach ( $args['options'] as $key => $label ) {

			$html_id = "{$args['section']}_{$args['id']}_{$key}";
			$html_id = $this->settings_clean_label_for( $html_id );


			$html .= '<div class="magic-radio-field">';
			//$html .= sprintf( '<input type="radio" class="radio" id="wpuf-%5$s" name="%1$s[%2$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, checked( $value, $key, false ), $html_id );
			$html .= sprintf( '<input type="radio" class="magic-radio" id="wpuf-%5$s" name="%1$s[%2$s]" value="%3$s" %4$s />',
				$args['section'], $args['id'], $key, checked( $value, $key, false ), $html_id );
			$html .= sprintf( '<label for="wpuf-%1$s">', $html_id );
			$html .= sprintf( '%1$s</label>', $label );
			$html .= '</div>';
		}

		$html .= '</div>';
		$html .= $this->get_field_description( $args );

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} //end method callback_radio

	/**
	 * Displays a checkbox for a settings field
	 *
	 * @param array $args
	 * @param $value
	 *
	 * @return void
	 */
	function callback_checkbox( $args, $value = null ) {
		if ( $value === null ) {
			$value = esc_attr( $this->get_field( $args['id'], $args['section'], $args['default'] ) );
		}

		$html_id = "{$args['section']}_{$args['id']}";
		$html_id = $this->settings_clean_label_for( $html_id );

		//$display_inline        = isset( $args['inline']) ? absint($args['inline']) : 1;
		//$display_inline_class = ($display_inline)? 'radio_fields_inline' : '';

		$html = '<div class="checkbox_field">';

		$html .= sprintf( '<input type="hidden" name="%1$s[%2$s]" value="off" />', $args['section'], $args['id'] );


		//$active_class = ( $value == 'on' ) ? 'active' : '';
		//$html         .= '<span class="checkbox-toggle-btn ' . esc_attr( $active_class ) . '">';
		$html .= sprintf( '<input type="checkbox" class="magic-checkbox" id="wpuf-%4$s" name="%1$s[%2$s]" value="on" %3$s />',
			$args['section'], $args['id'], checked( $value, 'on', false ), $html_id );
		//$html .= '<i class="checkbox-round-btn"></i></span>';

		$html .= sprintf( '<label for="wpuf-%1$s">', $html_id );
		$html .= sprintf( '%1$s</label>', $args['desc'] );


		$html .= '</div>';


		/*$html = '<fieldset>';
			  $html .= sprintf( '<label for="wpuf-%1$s">', $html_id );
			  $html .= sprintf( '<input type="hidden" name="%1$s[%2$s]" value="off" />', $args['section'], $args['id'] );

			  $active_class = ( $value == 'on' ) ? 'active' : '';
			  $html         .= '<span class="checkbox-toggle-btn ' . esc_attr( $active_class ) . '">';
			  $html .= sprintf( '<input type="checkbox" class="checkbox" id="wpuf-%4$s" name="%1$s[%2$s]" value='on' %3$s />', $args['section'], $args['id'], checked( $value, 'on', false ), $html_id );
			  $html .= '<i class="checkbox-round-btn"></i></span>';

			  $html .= sprintf( '<i class="checkbox-round-btn-text">%1$s</i></label>', $args['desc'] );
			  $html .= '</fieldset>';*/

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} //end method callback_checkbox

	/**
	 * Displays a multicheckbox settings field
	 *
	 * @param array $args
	 * @param $value
	 *
	 * @return void
	 */
	function callback_multicheck( $args, $value = null ) {
		$sortable = isset( $args['sortable'] ) ? intval( $args['sortable'] ) : 0;

		if ( $value === null ) {
			$value = $this->get_field( $args['id'], $args['section'], $args['default'] );
		}

		if ( ! is_array( $value ) ) {
			$value = [];
		}

		$display_inline       = isset( $args['inline'] ) ? absint( $args['inline'] ) : 1;
		$display_inline_class = '';
		if ( $sortable ) {
			$display_inline = 0;
		} else {
			$display_inline_class = ( $display_inline ) ? 'checkbox_fields_inline' : '';
		}

		$sortable_class = ( $sortable ) ? 'checkbox_fields_sortable' : '';

		$html = '<div class="checkbox_fields magic_checkbox_fields ' . esc_attr( $sortable_class ) . ' ' . esc_attr( $display_inline_class ) . '">';

		$options = $args['options']; //this can be regular array or associative array
		//$options_keys        = array_keys( $options );
		//$options_keys_diff   = array_diff( $options_keys, $value );
		//$options_keys_sorted = array_merge( $value, $options_keys_diff );

		foreach ( $options as $key => $option ) {
			$label = isset( $options[ $key ] ) ? esc_attr( $options[ $key ] ) : $option;

			$checked      = in_array( $key, $value ) ? ' checked="checked" ' : '';
			$active_class = in_array( $key, $value ) ? 'active' : '';

			$html_id = "{$args['section']}_{$args['id']}_{$key}";
			$html_id = $this->settings_clean_label_for( $html_id );

			$html .= '<div class="checkbox_field magic_checkbox_field">';
			if ( $sortable ) {
				$html .= '<span class="checkbox_field_handle"></span>';
			}

			/*$html .= sprintf( '<label for="wpuf-%1$s">', $html_id );
					 $html .= sprintf( '<input type="hidden" name="%1$s[%2$s][%3$s]" value="" />', $args['section'], $args['id'], $key );
					 $html .= '<span class="checkbox-toggle-btn ' . esc_attr( $active_class ) . '">';
					 $html .= sprintf( '<input type="checkbox" class="checkbox" id="wpuf-%5$s" name="%1$s[%2$s][%3$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, $checked, $html_id );
					 $html .= '<i class="checkbox-round-btn"></i></span>';

					 $html .= sprintf( '<i class="checkbox-round-btn-text">%1$s</i></label></p>', $label );*/

			$html .= sprintf( '<input type="hidden" name="%1$s[%2$s][%3$s]" value="" />', $args['section'], $args['id'],
				$key );
			$html .= sprintf( '<input type="checkbox" class="magic-checkbox" id="wpuf-%5$s" name="%1$s[%2$s][%3$s]" value="%3$s" %4$s />',
				$args['section'], $args['id'], $key, $checked, $html_id );
			$html .= sprintf( '<label for="wpuf-%1$s">', $html_id );
			$html .= sprintf( '%1$s</i></label>', $label );
			$html .= '</div>';
		}

		$html .= $this->get_field_description( $args );
		$html .= '</div>';

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} //end method callback_multicheck


	/**
	 * Displays a select box for a settings field
	 *
	 * @param $args
	 *
	 * @return void
	 */
	function callback_select( $args ) {
		$value = $this->get_field( $args['id'], $args['section'], $args['default'] );

		$multi      = isset( $args['multi'] ) ? intval( $args['multi'] ) : 0;
		$multi_name = ( $multi ) ? '[]' : '';
		$multi_attr = ( $multi ) ? 'multiple' : '';

		if ( $multi && ! is_array( $value ) ) {
			$value = [];
		}

		$allow_clear = isset( $args['allow_clear'] ) ? intval( $args['allow_clear'] ) : 0;

		/*if ( ! is_array( $value ) ) {
				  $value = [];
			  }*/

		$size = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular selecttwo-select';

		if ( $args['placeholder'] == '' ) {
			$placeholder         = $args['placeholder'] = esc_html__( 'Please Select', 'cbxwpemaillogger' );
			$args['placeholder'] = esc_html__( 'Please Select', 'cbxwpemaillogger' );
		} else {
			$placeholder = esc_attr( $args['placeholder'] );
		}

		//$html = sprintf( '<input type="hidden" name="%1$s[%2$s]'.$multi_name.'" value="" />', $args['section'], $args['id'] );

        $html = sprintf( '<div class="selecttwo-select-wrapper" data-placeholder="' . $placeholder . '" data-allow-clear="' . $allow_clear . '"><select ' . $multi_attr . ' class="%1$s" name="%2$s[%3$s]' . $multi_name . '" id="%2$s[%3$s]" style="min-width: 150px !important;"  placeholder="%4$s" data-placeholder="%4$s">',
			$size, $args['section'], $args['id'], $args['placeholder'] );

		if ( isset( $args['optgroup'] ) && $args['optgroup'] ) {
			foreach ( $args['options'] as $opt_grouplabel => $option_vals ) {
				$html .= '<optgroup label="' . $opt_grouplabel . '">';

				if ( ! is_array( $option_vals ) ) {
					$option_vals = [];
				} else {
					$option_vals = $option_vals;
				}

				foreach ( $option_vals as $key => $val ) {
					$selected = in_array( $key, $value ) ? ' selected="selected" ' : '';
					$html     .= sprintf( '<option value="%s" ' . $selected . '>%s</option>', $key, $val );
				}
				$html .= '</optgroup>';
			}
		} else {
			$option_vals = $args['options'];

			foreach ( $option_vals as $key => $val ) {
				if ( $multi ) {
					$selected = in_array( $key, $value ) ? ' selected="selected" ' : '';
					$html     .= sprintf( '<option value="%s" ' . $selected . '>%s</option>', $key, $val );
				} else {
					$html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $value, $key, false ), $val );
				}
			}
		}

		$html .= '</select></div>';
		$html .= $this->get_field_description( $args );

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} //end method callback_select

	/**
	 * Displays a select box for a settings field
	 *
	 * @param $args
	 *
	 * @return void
	 */
	function callback_page( $args ) {
		$edit_svg     = comfortsmtp_esc_svg( comfortsmtp_load_svg( 'icon_edit' ) );
		$external_svg = comfortsmtp_esc_svg( comfortsmtp_load_svg( 'icon_external' ) );

		$value         = $this->get_field( $args['id'], $args['section'], intval( $args['default'] ) );
		$size          = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular selecttwo-select';
		$check_content = isset( $args['check_content'] ) && ! is_null( $args['check_content'] ) ? $args['check_content'] : '';

		$allow_clear = isset( $args['allow_clear'] ) ? intval( $args['allow_clear'] ) : 0;

		$page_content          = '';
		$page_shortcode_note   = '';
		$shortcode_found_class = '';

		$multi      = isset( $args['multi'] ) ? intval( $args['multi'] ) : 0;
		$multi_name = ( $multi ) ? '[]' : '';
		$multi_attr = ( $multi ) ? 'multiple' : '';

		if ( $multi && ! is_array( $value ) ) {
			$value = [];
		} else {
			$value = absint( $value );
		}


		$html = '<div class="setting_pages_actions_wrapper">';

		$page_html = '<div class="setting_pages_actions">';
		if ( $value > 0 ) {
			if ( $check_content != '' ) {
				$page         = get_post( $value );
				$page_content = $page->post_content;
				if ( has_shortcode( $page_content, $check_content ) ) {
					$shortcode_found_class = 'description_note_on';
					/* translators: %s: Shortcode name  */
					$page_shortcode_note = sprintf( esc_html__( 'This page has shortcode %s', 'cbxwpemaillogger' ), $check_content );
				} else {
					$shortcode_found_class = 'description_note_off';
					/* translators: %s: Shortcode name  */
					$page_shortcode_note = sprintf( esc_html__( 'This page doesn\'t have shortcode %s', 'cbxwpemaillogger' ), $check_content );
				}
			}

			//edit
			if ( current_user_can( 'edit_post', $value ) ) {
				$page_html .= '<a class="setting_pages_action setting_pages_action_edit button primary icon icon-only icon-inline small" target="_blank" title="' . esc_attr__( 'Edit', 'cbxwpemaillogger' ) . '" href="' . esc_url( get_edit_post_link( $value ) ) . '"><i class="cbx-icon">' . $edit_svg . '</i></a>';
			}

			//view
			$page_html .= '<a class="setting_pages_action setting_pages_action_view button outline primary icon icon-only icon-inline small" title="' . esc_attr__( 'View', 'cbxwpemaillogger' ) . '" target="_blank" href="' . esc_url( get_the_permalink( $value ) ) . '"><i class="cbx-icon">' . $external_svg . '</i></a>';
		}

		$page_html .= '</div>';


		if ( $args['placeholder'] == '' ) {
			$placeholder = $args['placeholder'] = esc_html__( 'Please Select', 'cbxwpemaillogger' );
		} else {
			$placeholder = esc_attr( $args['placeholder'] );
		}


		//$html .= sprintf( '<input type="hidden" name="%1$s[%2$s]' . $multi_name . '" value="" />', $args['section'], $args['id'] );

        $html .= sprintf( '<div class="selecttwo-select-wrapper" data-placeholder="' . $placeholder . '" data-allow-clear="' . $allow_clear . '"><select ' . $multi_attr . '  class="%1$s" name="%2$s[%3$s]' . $multi_name . '" id="%2$s[%3$s]" style="min-width: 150px !important;" >', $size, $args['section'], $args['id'] );

		if ( isset( $args['optgroup'] ) && $args['optgroup'] ) {
			foreach ( $args['options'] as $opt_grouplabel => $option_vals ) {
				$html .= '<optgroup label="' . esc_attr( $opt_grouplabel ) . '">';

				if ( ! is_array( $option_vals ) ) {
					$option_vals = [];
				} else {
					//$option_vals = $option_vals;
				}

				foreach ( $option_vals as $key => $val ) {
					$selected = in_array( $key, $value ) ? ' selected="selected" ' : '';
					$html     .= sprintf( '<option value="%s" ' . $selected . '>%s</option>', $key, $val );
				}

				$html .= '</optgroup>';
			}
		} else {
			$option_vals = $args['options'];
			foreach ( $option_vals as $key => $val ) {
				$html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $value, $key, false ), $val );

			}
		}

		$html .= '</select></div>' . $page_html;
		$html .= '</div>'; //.setting_pages_actions_wrapper

		if ( $page_shortcode_note != '' ) {
			$html .= '<p class="description_note ' . esc_attr( $shortcode_found_class ) . '">' . $page_shortcode_note . '</p>';
		}

		$html .= $this->get_field_description( $args );

		echo $html; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}//end method callback_page

	/**
	 * Displays a textarea for a settings field
	 *
	 * @param array $args
	 * @param $value
	 *
	 * @return void
	 */
	function callback_textarea( $args, $value = null ) {
		if ( $value === null ) {
			$value = esc_textarea( $this->get_field( $args['id'], $args['section'], $args['default'] ) );
		}
		$size = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

		$html_id = "{$args['section']}_{$args['id']}";
		$html_id = $this->settings_clean_label_for( $html_id );

		$html = sprintf( '<textarea rows="5" cols="55" class="%1$s-text" id="%5$s" name="%2$s[%3$s]">%4$s</textarea>',
			$size, $args['section'], $args['id'], $value, $html_id );
		$html .= $this->get_field_description( $args );

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} //end method callback_textarea

	/**
	 * Displays a rich text textarea for a settings field
	 *
	 * @param array $args
	 * @param $value
	 *
	 * @return void
	 */
	function callback_wysiwyg( $args, $value = null ) {
		if ( $value === null ) {
			$value = $this->get_field( $args['id'], $args['section'], $args['default'] );
		}
		$size = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : '500px';

		echo '<div style="max-width: ' . $size . ';">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$html_id = "{$args['section']}_{$args['id']}";
		$html_id = $this->settings_clean_label_for( $html_id );

		$editor_settings = [
			'teeny'         => true,
			'textarea_name' => $args['section'] . '[' . $args['id'] . ']',
			'textarea_rows' => 10
		];
		if ( isset( $args['options'] ) && is_array( $args['options'] ) ) {
			$editor_settings = array_merge( $editor_settings, $args['options'] );
		}

		//wp_editor( $value, $args['section'] . '-' . $args['id'], $editor_settings );
		wp_editor( $value, $html_id, $editor_settings );

		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		echo $this->get_field_description( $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} //end method callback_wysiwyg

	/**
	 * Displays a file upload field for a settings field
	 *
	 * @param array $args settings field args
	 */
	function callback_file( $args ) {
		$value = esc_attr( $this->get_field( $args['id'], $args['section'], $args['default'] ) );
		$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

		//$id    = $args['section'] . '[' . $args['id'] . ']';
		$html_id = "{$args['section']}_{$args['id']}";
		$html_id = $this->settings_clean_label_for( $html_id );


		$label = isset( $args['options']['button_label'] ) ?	$args['options']['button_label'] :	esc_html__( 'Choose File', 'cbxwpemaillogger' );

		$html = '<div class="wpsa-browse-wrap">';
		$html .= sprintf( '<input type="text" class="chota-inline %1$s-text wpsa-url" id="%5$s" name="%2$s[%3$s]" value="%4$s"/>',
			$size, $args['section'], $args['id'], $value, $html_id );
		$html .= '<input type="button" class="button outline primary wpsa-browse" value="' . $label . '" />';
		$html .= '</div>';
		$html .= $this->get_field_description( $args );

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} //end method callback_file

	/**
	 * Displays a color picker field for a settings field
	 *
	 * @param array $args
	 * @param $value
	 *
	 * @return void
	 */
	function callback_color( $args, $value = null ) {

		if ( $value === null ) {
			$value = esc_attr( $this->get_field( $args['id'], $args['section'], $args['default'] ) );
		}

		$size = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

		$html_id = "{$args['section']}_{$args['id']}";
		$html_id = $this->settings_clean_label_for( $html_id );

		$choose_color = esc_html__( 'Choose Color', 'cbxwpemaillogger' );

		$html = '<div class="setting-color-picker-wrapper">';
		$html .= sprintf( '<input type="hidden" class="%1$s-text setting-color-picker" id="%6$s" name="%2$s[%3$s]" value="%4$s" /><span data-current-color="%4$s"  class="button setting-color-picker-fire">%7$s</span>',
			$size, $args['section'], $args['id'], $value, $args['default'], $html_id, $choose_color );
		$html .= '</div>';

		$html .= $this->get_field_description( $args );

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} //end callback_color

	/**
	 * Host servers type field
	 *
	 * @param $args
	 *
	 * @return void
	 */
	function callback_repeat( $args ) {
		$delete_svg = '<i class="cbx-icon">'.comfortsmtp_esc_svg( comfortsmtp_load_svg( 'icon_delete' ) ).'</i>';
		$edit_svg = '<i class="cbx-icon">'.comfortsmtp_esc_svg( comfortsmtp_load_svg( 'icon_edit' ) ).'</i>';
		$sort_svg = '<i class="cbx-icon">'.comfortsmtp_esc_svg( comfortsmtp_load_svg( 'icon_move' ) ).'</i>';

		$section_name = esc_attr( $args['section'] );
		$option_name  = esc_attr( $args['id'] );

		$default   = $args['default'];
		$fields    = isset( $args['fields'] ) ? $args['fields'] : [];
		$allow_new = isset( $args['allow_new'] ) ? intval( $args['allow_new'] ) : 0;
		$value     = $this->get_field( $args['id'], $args['section'], $args['default'] );


		if ( ! is_array( $value ) ) {
			$value = [];
		}


		$html  = '';
		$index = 0;

		$html .= '<div class="form-table-fields-parent-wrap">';
		$html .= '<div class="form-table-fields-parent">';
		if ( is_array( $fields ) & sizeof( $fields ) > 0 ) {
			foreach ( $value as $val ) {
				if ( ! is_array( $val ) ) {
					$val = [];
				}

				$html .= '<div class="form-table-fields-parent-item">';
				$html .= '<h5><p class="form-table-fields-parent-item-heading">' . $args['name'] . ' #' . ( $index + 1 ).'</p>';
				$html .= '<span class="form-table-fields-parent-item-icon form-table-fields-parent-item-sort icon icon-only">'.$sort_svg.'</span>';
				$html .= '<span class="form-table-fields-parent-item-icon form-table-fields-parent-item-control icon icon-only">'.$edit_svg.'</span>';
				if ( $allow_new ) {
					//if allow new then allow delete
					$html .= '<span class="form-table-fields-parent-item-icon form-table-fields-parent-item-delete icon icon-only">'.$delete_svg.'</span>';
				}
				$html .= '</h5>';
				$html .= '<div class="form-table-fields-parent-item-wrap">';

				$html .= '<table class="form-table-fields-items">';
				foreach ( $fields as $field ) {
					$args_t = $args;
					unset( $args_t['fields'] );
					unset( $args_t['allow_new'] );

					$args_t['section']           = isset( $args['section'] ) ? $args['section'] . '[' . $args['id'] . '][' . $index . ']' : '';
					$args_t['desc']              = isset( $field['desc'] ) ? $field['desc'] : '';
					$args_t['name']              = isset( $field['name'] ) ? $field['name'] : '';
					$args_t['label']             = isset( $field['label'] ) ? $field['label'] : '';
					$args_t['class']             = isset( $field['class'] ) ? $field['class'] : $args_t['name'];
					$args_t['id']                = $args_t['name'];
					$args_t['size']              = isset( $field['size'] ) ? $field['size'] : null;
					$args_t['min']               = isset( $field['min'] ) ? $field['min'] : '';
					$args_t['max']               = isset( $field['max'] ) ? $field['max'] : '';
					$args_t['step']              = isset( $field['step'] ) ? $field['step'] : '';
					$args_t['options']           = isset( $field['options'] ) ? $field['options'] : '';
					$args_t['default']           = isset( $field['default'] ) ? $field['default'] : '';
					$args_t['sanitize_callback'] = isset( $field['sanitize_callback'] ) ? $field['sanitize_callback'] : '';
					$args_t['placeholder']       = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
					$args_t['type']              = isset( $field['type'] ) ? $field['type'] : 'text';
					$args_t['optgroup']          = isset( $field['optgroup'] ) ? intval( $field['optgroup'] ) : 0;
					$args_t['sortable']          = isset( $field['sortable'] ) ? intval( $field['sortable'] ) : 0;
					$callback                    = isset( $field['callback'] ) ? $field['callback'] : [
						$this,
						'callback_' . $args_t['type']
					];


					//$val_t = isset( $val[ $field['name'] ] ) ? $val[ $field['name'] ] : ( is_array( $args_t['default'] ) ? [] : '' );
					$val_t = isset( $val[ $field['name'] ] ) ? $val[ $field['name'] ] : $args_t['default'];

					$html    .= '<tr class="form-table-fields-item"><td>';
					$html_id = "{$args_t['section']}_{$args_t['id']}";
					$html_id = $this->settings_clean_label_for( $html_id );
					$html    .= sprintf( '<label class="main-label" for="%1$s">%2$s</label>', $html_id,
						$args_t['label'] );
					$html    .= '</td></tr>';

					$html .= '<tr class="form-table-fields-item"><td>';
					ob_start();
					call_user_func( $callback, $args_t, $val_t );
					$html .= ob_get_contents();
					ob_end_clean();
					$html .= '</td></tr>';
				}
				$html .= '</table>';
				$html .= '</div>';
				$html .= '</div>';
				$index ++;
			}

		}

		$html .= '</div>';

		if ( $allow_new ) {
			$html .= '<p style="text-align: center;"><a data-index="' . absint( $index ) . '" data-busy="0" data-field_name="' . $args['name'] . '" data-section_name="' . $section_name . '" data-option_name="' . $option_name . '" class="button secondary form-table-fields-new ld-ext-right" href="#">' . esc_html__( 'Add New',
					'cbxwpemaillogger' ) . '<span class="ld ld-spin ld-ring"></span></a></p>';
		}

		$html .= '</div>';
		$html .= $this->get_field_description( $args );

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} //end callback_repeat

	/**
	 * Displays a password field for a settings field
	 *
	 * @param array $args
	 *
	 * @return void
	 */
	function callback_password( $args ) {

		$value = esc_attr( $this->get_field( $args['id'], $args['section'], $args['default'] ) );
		$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

		$html = sprintf( '<input type="password" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>',
			$size, $args['section'], $args['id'], $value );
		$html .= $this->get_field_description( $args );

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} //end method callback_password


	/**
	 * Displays a email field for a settings field
	 *
	 * @param array $args settings field args
	 */
	function callback_email( $args ) {
		$value = esc_attr( $this->get_field( $args['id'], $args['section'], $args['default'] ) );
		$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
		$type  = isset( $args['type'] ) ? $args['type'] : 'text';

		$html_id = "{$args['section']}_{$args['id']}";
		$html_id = $this->settings_clean_label_for( $html_id );

		$html = sprintf( '<input  autocomplete="none" onfocus="this.removeAttribute(\'readonly\');" readonly type="%1$s" class="%2$s-text" id="%6$s" name="%3$s[%4$s]" value="%5$s"/>',
			$type, $size, $args['section'], $args['id'], $value, $html_id );
		$html .= $this->get_field_description( $args );

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} //end method callback_email

	/**
	 * Sanitize callback for Settings API
	 */
	function sanitize_options( $options ) {
		foreach ( $options as $option_slug => $option_value ) {
			$sanitize_callback = $this->get_sanitize_callback( $option_slug );

			// If callback is set, call it
			if ( $sanitize_callback ) {
				$options[ $option_slug ] = call_user_func( $sanitize_callback, $option_value );
				continue;
			}
		}

		return $options;
	} //end sanitize_options

	/**
	 * Convert an array to associative if not
	 *
	 * @param $value
	 *
	 * @return array
	 */
	private function convert_associate( $value ) {
		if ( ! $this->is_associate( $value ) && sizeof( $value ) > 0 ) {
			$new_value = [];
			foreach ( $value as $val ) {
				$new_value[ $val ] = ucfirst( $val );
			}

			return $new_value;
		}


		return $value;
	} //end method convert_associate

	/**
	 * check if any array is associative
	 *
	 * @param array $array
	 *
	 * @return bool
	 */
	private function is_associate( array $array ) {
		return count( array_filter( array_keys( $array ), 'is_string' ) ) > 0;
	} //end method is_associate

	/**
	 * Get sanitization callback for given option slug
	 *
	 * @param string $slug option slug
	 *
	 * @return mixed string or bool false
	 */
	function get_sanitize_callback( $slug = '' ) {
		if ( empty( $slug ) ) {
			return false;
		}

		// Iterate over registered fields and see if we can find proper callback
		foreach ( $this->settings_fields as $section => $options ) {
			foreach ( $options as $option ) {
				if ( $option['name'] != $slug ) {
					continue;
				}

				if ( ( $option['type'] == 'select' && isset( $option['multi'] ) && $option['multi'] ) || $option['type'] == 'multicheck' ) {
					$option['sanitize_callback'] = [ $this, 'sanitize_multi_select_check' ];
				}

				// Return the callback name
				return isset( $option['sanitize_callback'] ) && is_callable( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : false;
			}
		}

		return false;
	} //end get_sanitize_callback

	/**
	 * Remove empty values from multi select fields (multi select and multi checkbox)
	 *
	 * @param $option_value
	 *
	 * @return array
	 */
	public function sanitize_multi_select_check( $option_value ) {
		if ( is_array( $option_value ) ) {
			return array_filter( $option_value );
		}

		return $option_value;
	} //end sanitize_multi_select_check

	/**
	 * Clean label_for or id tad
	 *
	 * @param $str
	 *
	 * @return string
	 */
	public function settings_clean_label_for( $str ) {
		$str = str_replace( '][', '_', $str );
		$str = str_replace( ']', '_', $str );

		return str_replace( '[', '_', $str );

		//return $str;
	} //end settings_clean_label_for

	/**
	 * Get the value of a settings field
	 *
	 * @param string $option settings field name
	 * @param string $section the section name this field belongs to
	 * @param string $default default text if it's not found
	 *
	 * @return string
	 */
	function get_option( $option, $section, $default = '' ) {
		$options = get_option( $section );

		//if ( isset( $options[ $option ] ) && $options[ $option ] ) {
		if ( isset( $options[ $option ] ) ) {
			return $options[ $option ];
		}

		return $default;
	} //end get_option

	/**
	 * alt method for get_option
	 *
	 * @param $option
	 * @param $section
	 * @param $default
	 *
	 * @return string
	 */
	function get_opt( $option, $section, $default = '' ) {
		return $this->get_option( $option, $section, $default );
	}//end method get_opt

	/**
	 * alt method for get_option
	 *
	 * @param $option
	 * @param $section
	 * @param $default
	 *
	 * @return string
	 */
	function get_field( $option, $section, $default = '' ) {
		return $this->get_option( $option, $section, $default );
	}//end method get_field

	/**
	 * Show navigations as tab
	 *
	 * Shows all the settings section labels as tab
	 */
	function show_navigation() {
		$html = '<nav class="tabs setting-tabs setting-tabs-nav mb-0">';

		$i = 0;

		$mobile_navs = '<div  class="selecttwo-select-wrapper setting-select-wrapper"><select data-minimum-results-for-search="Infinity" class="setting-select setting-select-nav selecttwo-select">';

		foreach ( $this->settings_sections as $tab ) {
			$active_class  = ( $i === 0 ) ? 'active' : '';
			$active_select = ( $i === 0 ) ? ' selected ' : '';


			$html .= sprintf( '<a data-tabid="' . $tab['id'] . '" href="#%1$s" class="%3$s" id="%1$s-tab">%2$s</a>',
				$tab['id'], $tab['title'], $active_class );

			$mobile_navs .= '<option ' . esc_attr( $active_select ) . ' value="' . $tab['id'] . '">' . esc_attr( $tab['title'] ) . '</option>';

			$i ++;
		}


		$mobile_navs .= '</select></div>';

		$html .= '</nav>';

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		echo $mobile_navs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} //end method show_navigation

	/**
	 * Show the section settings forms
	 *
	 * This function displays every sections in a different form
	 */
	function show_forms() {
		?>
        <div id="setting-tabs-contents">
            <div id="global_setting_group_actions" class="mb-0">
                <a class="button outline primary global_setting_group_action global_setting_group_action_open pull-right"
                   href="#">
					<?php esc_html_e( 'Toggle All Sections', 'cbxwpemaillogger' ); ?>
                </a>
                <div class="clear clearfix"></div>
            </div>
            <div class="metabox-holder">
				<?php
				$i = 0;
				foreach ( $this->settings_sections as $form ):
					$display_style = ( $i === 0 ) ? '' : 'display: none;';
					?>
                    <div id="<?php echo esc_attr( $form['id'] ); ?>" class="global_setting_group"
                         style="<?php echo esc_attr( $display_style ); ?>">
                        <form method="post" action="options.php" class="comfortsmtp_setting_form">
							<?php
							do_action( 'comfortsmtp_setting_form_top_' . $form['id'], $form );
							settings_fields( $form['id'] );
							do_settings_sections( $form['id'] );
							do_action( 'comfortsmtp_setting_form_bottom_' . $form['id'], $form );
							?>
                            <div class="global_setting_submit_buttons_wrap">
								<?php do_action( 'comfortsmtp_setting_submit_buttons_start', $form['id'] ); ?>
								<?php submit_button( esc_html__( 'Save Settings', 'cbxwpemaillogger' ),
									'button primary submit_setting', 'submit', true,
									[ 'id' => 'submit_' . esc_attr( $form['id'] ) ] ); ?>
								<?php do_action( 'comfortsmtp_setting_submit_buttons_end', $form['id'] ); ?>
                            </div>
                        </form>
                    </div>
					<?php
					$i ++;
				endforeach;
				?>
            </div>
        </div>
		<?php
	} //end show_forms
} //end class cbxpetitionSettings