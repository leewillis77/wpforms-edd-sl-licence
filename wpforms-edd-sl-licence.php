<?php
/*
Plugin Name: WPForms / EDD Software Licence field
Plugin URI: http://www.ademti-software.co.uk
Description: Provides a "licence" input field which allows customers to enter a
             licence. If this matches a licence in EDD Software Licensing then
			 additional information will be provided in the email.
Version: 0.1
Author: Ademti Software Ltd.
Author URI: http://www.ademti-software.co.uk
*/

/**
 * Copyright (c) 2017 Ademti Software Ltd. All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * **********************************************************************
 */

add_action( 'init', function() {
	require_once( dirname( __FILE__ ) . '/wpforms-edd-sl-licence-field.php' );
	new WPForms_EDD_SL_Licence_Field();
}, 99);

add_action( 'init', function() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'wpforms-edd-sl-licence' );
	load_textdomain( 'wpforms-edd-sl-licence', WP_LANG_DIR . '/wpforms-edd-sl-licence/wpforms-edd-sl-licence-' . $locale . '.mo' );
	load_plugin_textdomain( 'wpforms-edd-sl-licence', false, basename( dirname( __FILE__ ) ) . '/languages/' );
});
