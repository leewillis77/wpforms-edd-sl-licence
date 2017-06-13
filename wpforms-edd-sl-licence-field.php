<?php
/**
 * EDD software licence entry field.
 */
class WPForms_EDD_SL_Licence_Field extends WPForms_Field_Text {

	public function init() {
		// Define field type information.
		parent::init();
		$this->name  = __( 'Software licence', 'wpforms-edd-sl-licence' );
		$this->type  = 'wpforms-edd-sl-licence-field';
		$this->icon  = 'fa-text-width';
		$this->order = 99;
	}

	/**
	 * Field options panel inside the builder.
	 *
	 * @since 1.0.0
	 * @param array $field
	 */
	public function field_options( $field ) {

		// -------------------------------------------------------------------//
		// Basic field options.
		// -------------------------------------------------------------------//

		// Options open markup.
		$args = array(
			'markup' => 'open',
		);
		$this->field_option( 'basic-options', $field, $args );

		// Label.
		$this->field_option( 'label', $field );

		// Description.
		$this->field_option( 'description', $field );

		// Required toggle.
		$this->field_option( 'required', $field );

		// Options close markup.
		$args = array(
			'markup' => 'close',
		);
		$this->field_option( 'basic-options', $field, $args );

		// --------------------------------------------------------------------//
		// Advanced field options.
		// --------------------------------------------------------------------//

		// Options open markup.
		$args = array(
			'markup' => 'open',
		);
		$this->field_option( 'advanced-options', $field, $args );

		// Size.
		$this->field_option( 'size', $field );

		// Placeholder.
		$this->field_option( 'placeholder', $field );

		// Hide label.
		$this->field_option( 'label_hide', $field );

		// Default value.
		$this->field_option( 'default_value', $field );

		// Custom CSS classes.
		$this->field_option( 'css', $field );

		// Options close markup.
		$args = array(
			'markup' => 'close',
		);
		$this->field_option( 'advanced-options', $field, $args );
	}

	/**
	 * Field preview inside the builder.
	 *
	 * @since 1.0.0
	 * @param array $field
	 */
	public function field_preview( $field ) {

		// Define data.
		$placeholder = ! empty( $field['placeholder'] ) ? esc_attr( $field['placeholder'] ) : '';

		// Label.
		$this->field_preview_option( 'label', $field );

		// Primary input.
		echo '<input type="text" placeholder="' . $placeholder . '" class="primary-input" disabled>';

		// Description.
		$this->field_preview_option( 'description', $field );
	}

	/**
	 * Field display on the form front-end.
	 *
	 * @since 1.0.0
	 * @param array $field
	 * @param array $deprecated
	 * @param array $form_data
	 */
	public function field_display( $field, $deprecated, $form_data ) {

		// Define data.
		$primary = $field['properties']['inputs']['primary'];

		// Primary field.
		printf( '<input type="text" %s %s>',
			wpforms_html_attributes( $primary['id'], $primary['class'], $primary['data'], $primary['attr'] ),
			$primary['required']
		);
	}

	public function format( $field_id, $field_submit, $form_data ) {
		// Grab the info we need.
		$field     = $form_data['fields'][ $field_id ];
		$value_raw = sanitize_text_field( $field_submit );

		// Flesh out the return object.
		$data = array(
			'name'      => sanitize_text_field( $field['label'] ),
			'value'     => '',
			'value_raw' => $value_raw,
			'id'        => absint( $field_id ),
			'type'      => $this->type,
		);
		// Set a default value to pass through on failure.
		$data['value'] = sprintf(
			__( 'Unrecognized: %s', 'wp-forms-edd-sl-licence' ),
			sanitize_text_field( $value_raw )
		);

		// Check we have a licence key to check. If not - return as-is.
		if ( empty( $value_raw ) || ! class_exists( 'EDD_Software_Licensing' ) ) {
			wpforms()->process->fields[ $field_id ] = $data;
			return;
		}

		$edd_sl = EDD_Software_Licensing::instance();
		$licence_id = $edd_sl->get_license_by_key( $value_raw );
		if ( ! $licence_id ) {
			wpforms()->process->fields[ $field_id ] = $data;
			return;
		}
		$licence_meta     = get_post_custom( $licence_id );
		$licence_status   = ucfirst( strtolower( $licence_meta['_edd_sl_status'][0] ) );
		$sites_registered = unserialize( $licence_meta['_edd_sl_sites'][0] );
		$expiry           = gmdate( 'd F Y', $licence_meta['_edd_sl_expiration'][0] );
		if ( $licence_meta['_edd_sl_expiration'][0] < time() ) {
			$expired_or_expires = __( 'expired', 'wpforms-edd-sl-licence' );
		} else {
			$expired_or_expires = __( 'expires', 'wpforms-edd-sl-licence' );
		}
		$status_string = __( "%1\$s\n %2\$s, %3\$s %4\$s\n%5\$d sites registered:\n%6\$s", 'wpforms-edd-sl-licence' );
		$status_string = sprintf(
			$status_string,
			$value_raw,
			$licence_status,
			$expired_or_expires,
			$expiry,
			count( $sites_registered ),
			implode( "\n", array_map(
				function( $item ) {
					return '- ' . $item;
				},
				$sites_registered
			) )
		);
		$data['value'] = $status_string;
		wpforms()->process->fields[ $field_id ] = $data;
	}
}
