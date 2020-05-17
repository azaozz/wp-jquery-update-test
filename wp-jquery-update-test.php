<?php
/*
 * Plugin Name: WordPress jQuery Update Test
 * Plugin URI: https://wordpress.org/plugins/
 * Description: A feature plugin to help with testing updates of the jQuery and jQuery UI JavaScript libraries.
 * Version: 1.0.0
 * Requires at least: 5.4
 * Tested up to: 5.5
 * Requires PHP: 5.6
 * Author: The WordPress Team
 * Author URI: https://wordpress.org
 * Contributors: wordpressdotorg, azaozz
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-jquery-test
 * Network: true
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Invalid request.' );
}

if ( ! class_exists( 'WP_Jquery_Update_Test' ) ) :
class WP_Jquery_Update_Test {

	private function __construct() {}

	public static function init_actions() {
		// To be able to replace the src, scripts should not be concatenated.
		if ( ! defined( 'CONCATENATE_SCRIPTS' ) ) {
			define( 'CONCATENATE_SCRIPTS', false );
		} else {
			$GLOBALS['concatenate_scripts'] = false;
		}

		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'register_scripts' ), -1 );
		add_action( 'login_enqueue_scripts', array( __CLASS__, 'register_scripts' ), -1 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_scripts' ), -1 );

		add_action( 'admin_menu', array( __CLASS__, 'add_menu_item' ) );
		add_action( 'network_admin_menu', array( __CLASS__, 'add_menu_item' ) );

		add_action( 'admin_init', array( __CLASS__, 'save_settings' ) );
	}

	public static function register_scripts() {
		$settings = get_site_option( 'wp-jquery-test-settings', array() );
		$defaults = array(
			'version'   => 'default',
			'migrate'   => 'disable',
			'uiversion' => 'default',
		);

		$settings = wp_parse_args( $settings, $defaults );

		if ( 'default' === $settings['version'] ) {
			// If Migrate is disabled
			if ( 'disable' === $settings['migrate'] ) {
				// Re-register jQuery without jquery-migrate.js
				wp_deregister_script( array( 'jquery', 'jquery-migrate' ) );
				wp_register_script( 'jquery', '/wp-includes/js/jquery/jquery.js', array(), '1.12.4-wp' );
			}
		} elseif ( '3.5.1' === $settings['version'] ) {
			$assets_url = plugins_url( 'assets/', __FILE__ );

			$remove = array(
				'jquery',
				'jquery-core',
				'jquery-migrate',
			);

			wp_deregister_script( $remove );

			if ( 'disable' === $settings['migrate'] ) {
				wp_register_script( 'jquery', $assets_url . 'jquery-3.5.1.min.js', array(), '3.5.1' );
			} else {
				wp_register_script( 'jquery', false, array( 'jquery-core', 'jquery-migrate' ), '3.5.1' );
				wp_register_script( 'jquery-core', $assets_url . 'jquery-3.5.1.min.js', array(), '3.5.1' );
				wp_register_script( 'jquery-migrate', $assets_url . 'jquery-migrate-3.3.0.min.js', array(), '3.3.0' );
			}

			if ( '1.12.1' === $settings['uiversion'] ) {
				self::jquery_ui_1121();
			}
		}
	}

	private static function jquery_ui_1121() {
		$assets_url = plugins_url( 'assets/ui', __FILE__ );
		$dev_suffix = wp_scripts_get_suffix( 'dev' );

		$handles = array(
			'jquery-ui-core',
			'jquery-effects-core',
			'jquery-effects-blind',
			'jquery-effects-bounce',
			'jquery-effects-clip',
			'jquery-effects-drop',
			'jquery-effects-explode',
			'jquery-effects-fade',
			'jquery-effects-fold',
			'jquery-effects-highlight',
			'jquery-effects-puff',
			'jquery-effects-pulsate',
			'jquery-effects-scale',
			'jquery-effects-shake',
			'jquery-effects-size',
			'jquery-effects-slide',
			'jquery-effects-transfer',
			'jquery-ui-accordion',
			'jquery-ui-autocomplete',
			'jquery-ui-button',
			'jquery-ui-datepicker',
			'jquery-ui-dialog',
			'jquery-ui-draggable',
			'jquery-ui-droppable',
			'jquery-ui-menu',
			'jquery-ui-mouse',
			'jquery-ui-position',
			'jquery-ui-progressbar',
			'jquery-ui-resizable',
			'jquery-ui-selectable',
			'jquery-ui-selectmenu',
			'jquery-ui-slider',
			'jquery-ui-sortable',
			'jquery-ui-spinner',
			'jquery-ui-tabs',
			'jquery-ui-tooltip',
			'jquery-ui-widget',
		);

		wp_deregister_script( $handles );

		// The core.js in 1.12.1 only defines dependencies.
		// Here is it concatenated using another build task in WP core's Grunt.
		// The separate jQuery UI core parts are still present for AMD compatibility (is this needed?),
		// but are not registered in script-loader as they are included in ui/core.js.
		wp_register_script( 'jquery-ui-core', "{$assets_url}/core{$dev_suffix}.js", array( 'jquery' ), '1.12.1', true );
		wp_register_script( 'jquery-effects-core', "{$assets_url}/effect{$dev_suffix}.js", array( 'jquery' ), '1.12.1', true );

		wp_register_script( 'jquery-effects-blind', "{$assets_url}/effect-blind{$dev_suffix}.js", array( 'jquery-effects-core' ), '1.12.1', true );
		wp_register_script( 'jquery-effects-bounce', "{$assets_url}/effect-bounce{$dev_suffix}.js", array( 'jquery-effects-core' ), '1.12.1', true );
		wp_register_script( 'jquery-effects-clip', "{$assets_url}/effect-clip{$dev_suffix}.js", array( 'jquery-effects-core' ), '1.12.1', true );
		wp_register_script( 'jquery-effects-drop', "{$assets_url}/effect-drop{$dev_suffix}.js", array( 'jquery-effects-core' ), '1.12.1', true );
		wp_register_script( 'jquery-effects-explode', "{$assets_url}/effect-explode{$dev_suffix}.js", array( 'jquery-effects-core' ), '1.12.1', true );
		wp_register_script( 'jquery-effects-fade', "{$assets_url}/effect-fade{$dev_suffix}.js", array( 'jquery-effects-core' ), '1.12.1', true );
		wp_register_script( 'jquery-effects-fold', "{$assets_url}/effect-fold{$dev_suffix}.js", array( 'jquery-effects-core' ), '1.12.1', true );
		wp_register_script( 'jquery-effects-highlight', "{$assets_url}/effect-highlight{$dev_suffix}.js", array( 'jquery-effects-core' ), '1.12.1', true );
		wp_register_script( 'jquery-effects-puff', "{$assets_url}/effect-puff{$dev_suffix}.js", array( 'jquery-effects-core', 'jquery-effects-scale' ), '1.12.1', true );
		wp_register_script( 'jquery-effects-pulsate', "{$assets_url}/effect-pulsate{$dev_suffix}.js", array( 'jquery-effects-core' ), '1.12.1', true );
		wp_register_script( 'jquery-effects-scale', "{$assets_url}/effect-scale{$dev_suffix}.js", array( 'jquery-effects-core', 'jquery-effects-size' ), '1.12.1', true );
		wp_register_script( 'jquery-effects-shake', "{$assets_url}/effect-shake{$dev_suffix}.js", array( 'jquery-effects-core' ), '1.12.1', true );
		wp_register_script( 'jquery-effects-size', "{$assets_url}/effect-size{$dev_suffix}.js", array( 'jquery-effects-core' ), '1.12.1', true );
		wp_register_script( 'jquery-effects-slide', "{$assets_url}/effect-slide{$dev_suffix}.js", array( 'jquery-effects-core' ), '1.12.1', true );
		wp_register_script( 'jquery-effects-transfer', "{$assets_url}/effect-transfer{$dev_suffix}.js", array( 'jquery-effects-core' ), '1.12.1', true );

		wp_register_script( 'jquery-ui-accordion', "{$assets_url}/accordion{$dev_suffix}.js", array( 'jquery-ui-core', 'jquery-ui-widget' ), '1.12.1', true );
		wp_register_script( 'jquery-ui-autocomplete', "{$assets_url}/autocomplete{$dev_suffix}.js", array( 'jquery-ui-menu', 'wp-a11y' ), '1.12.1', true );
		wp_register_script( 'jquery-ui-button', "{$assets_url}/button{$dev_suffix}.js", array( 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-controlgroup', 'jquery-ui-checkboxradio' ), '1.12.1', true );
		wp_register_script( 'jquery-ui-datepicker', "{$assets_url}/datepicker{$dev_suffix}.js", array( 'jquery-ui-core' ), '1.12.1', true );
		wp_register_script( 'jquery-ui-dialog', "{$assets_url}/dialog{$dev_suffix}.js", array( 'jquery-ui-resizable', 'jquery-ui-draggable', 'jquery-ui-button', 'jquery-ui-position' ), '1.12.1', true );
		wp_register_script( 'jquery-ui-draggable', "{$assets_url}/draggable{$dev_suffix}.js", array( 'jquery-ui-mouse' ), '1.12.1', true );
		wp_register_script( 'jquery-ui-droppable', "{$assets_url}/droppable{$dev_suffix}.js", array( 'jquery-ui-draggable' ), '1.12.1', true );
		wp_register_script( 'jquery-ui-menu', "{$assets_url}/menu{$dev_suffix}.js", array( 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position' ), '1.12.1', true );
		wp_register_script( 'jquery-ui-mouse', "{$assets_url}/mouse{$dev_suffix}.js", array( 'jquery-ui-core', 'jquery-ui-widget' ), '1.12.1', true );
		wp_register_script( 'jquery-ui-position', "{$assets_url}/position{$dev_suffix}.js", array( 'jquery' ), '1.12.1', true );
		wp_register_script( 'jquery-ui-progressbar', "{$assets_url}/progressbar{$dev_suffix}.js", array( 'jquery-ui-core', 'jquery-ui-widget' ), '1.12.1', true );
		wp_register_script( 'jquery-ui-resizable', "{$assets_url}/resizable{$dev_suffix}.js", array( 'jquery-ui-mouse' ), '1.12.1', true );
		wp_register_script( 'jquery-ui-selectable', "{$assets_url}/selectable{$dev_suffix}.js", array( 'jquery-ui-mouse' ), '1.12.1', true );
		wp_register_script( 'jquery-ui-selectmenu', "{$assets_url}/selectmenu{$dev_suffix}.js", array( 'jquery-ui-menu' ), '1.12.1', true );
		wp_register_script( 'jquery-ui-slider', "{$assets_url}/slider{$dev_suffix}.js", array( 'jquery-ui-mouse' ), '1.12.1', true );
		wp_register_script( 'jquery-ui-sortable', "{$assets_url}/sortable{$dev_suffix}.js", array( 'jquery-ui-mouse' ), '1.12.1', true );
		wp_register_script( 'jquery-ui-spinner', "{$assets_url}/spinner{$dev_suffix}.js", array( 'jquery-ui-button' ), '1.12.1', true );
		wp_register_script( 'jquery-ui-tabs', "{$assets_url}/tabs{$dev_suffix}.js", array( 'jquery-ui-core', 'jquery-ui-widget' ), '1.12.1', true );
		wp_register_script( 'jquery-ui-tooltip', "{$assets_url}/tooltip{$dev_suffix}.js", array( 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position' ), '1.12.1', true );
		wp_register_script( 'jquery-ui-widget', "{$assets_url}/widget{$dev_suffix}.js", array( 'jquery' ), '1.12.1', true );

		// New in 1.12.1
		wp_register_script( 'jquery-ui-checkboxradio', "{$assets_url}/checkboxradio{$dev_suffix}.js", array( 'jquery-ui-core', 'jquery-ui-widget' ), '1.12.1', true );
		wp_register_script( 'jquery-ui-controlgroup', "{$assets_url}/controlgroup{$dev_suffix}.js", array( 'jquery-ui-widget' ), '1.12.1', true );

		/*
		// If using full concatenated jQuery UI (it's big...)
		wp_register_script( 'jquery-ui', $assets_url . 'jquery-ui.min.js', array( 'jquery' ), '1.12.1', true );

		foreach( $handles as $handle ) {
			wp_register_script( $handle, false, array( 'jquery-ui' ), '1.12.1', true );
		}
		*/

		// Strings for 'jquery-ui-autocomplete' live region messages.
		$strings = array(
			'noResults'    => __( 'No results found.' ),
			/* translators: Number of results found when using jQuery UI Autocomplete. */
			'oneResult'    => __( '1 result found. Use up and down arrow keys to navigate.' ),
			/* translators: %d: Number of results found when using jQuery UI Autocomplete. */
			'manyResults'  => __( '%d results found. Use up and down arrow keys to navigate.' ),
			'itemSelected' => __( 'Item selected.' ),
		);

		wp_localize_script( 'jquery-ui-autocomplete', 'uiAutocompleteL10n', $strings );
	}

	public static function save_settings() {
		if ( ! isset( $_POST['wp-jquery-test-save'] ) ) {
			return;
		}

		if (
			! current_user_can( 'install_plugins' ) ||
			! wp_verify_nonce( $_POST['wp-jquery-test-save'], 'wp-jquery-test-settings' )
		) {
			wp_die( 'Invalid URL.' );
		}

		$settings = array();
		$expected = array(
			'default',
			'enable',
			'disable',
			'3.5.1',
			'1.12.1',
		);

		$names = array(
			'version',
			'migrate',
			'uiversion',
		);

		foreach( $names as $name ) {
			$key = "jquery-test-{$name}";

			if ( ! empty( $_POST[ $key ] ) && in_array( $_POST[ $key ], $expected, true ) ) {
				$settings[ $name ] = $_POST[ $key ];
			} else {
				$settings[ $name ] = 'default';
			}
		}

		update_site_option( 'wp-jquery-test-settings', $settings );

		$redirect = self_admin_url( 'tools.php?page=wp-jquery-update-test&jqtest-settings-saved' );
		wp_safe_redirect( $redirect );
		exit;
	}

	// Plugin UI
	public static function settings_ui() {
		$settings = get_site_option( 'wp-jquery-test-settings', array() );
		$defaults = array(
			'version'   => 'default',
			'migrate'   => 'disable',
			'uiversion' => 'default',
		);

		$settings = wp_parse_args( $settings, $defaults );

		?>
		<div class="wrap" style="max-width: 42rem;">

		<h1><?php _e( 'jQuery Update Test Settings', 'wp-jquery-test' ); ?></h1>

		<?php if ( isset( $_GET['jqtest-settings-saved'] ) ) { ?>
		<div class="notice notice-success is-dismissible">
			<p><strong><?php _e( 'Settings saved.', 'wp-jquery-test' ); ?></strong></p>
		</div>
		<?php } ?>

		<p>
			<?php _e( 'This plugin is intended for testing of different versions of jQuery, jQuery Migrate, and jQuery UI.', 'wp-jquery-test' ); ?>
			<?php _e( 'There are several intended tests:', 'wp-jquery-test' ); ?>
		</p>

		<ol>
			<li><?php _e( 'Use the current version of jQuery but disable jQuery Migrate. This is planned for WordPress 5.5 and is the default setting.', 'wp-jquery-test' ); ?></li>
			<li>
				<?php _e( 'Latest jQuery, currently 3.5.1, with the latest jQuery Migrate, currently 3.3.0. This is planned for WordPress 5.6.', 'wp-jquery-test' ); ?>
				<?php _e( 'More information:', 'wp-jquery-test' ); ?>
				<?php
					printf(
						__( '<a href="%s">jQuery Core 3.0 Upgrade Guide</a>,', 'wp-jquery-test' ),
						'https://jquery.com/upgrade-guide/3.0/'
					);
				?>
				<?php
					printf(
						__( '<a href="%s">jQuery Core 3.5 Upgrade Guide</a>.', 'wp-jquery-test' ),
						'https://jquery.com/upgrade-guide/3.5/'
					);
				?>
			</li>
			<li><?php _e( 'Latest jQuery without jQuery Migrate. This is tentatively planned for WordPress 5.7 depending on test results.', 'wp-jquery-test' ); ?></li>
		</ol>

		<p>
			<?php _e( 'The plugin also includes the latest version of jQuery UI, 1.12.1.', 'wp-jquery-test' ); ?>
			<?php _e( 'It has been re-built for full backwards compatibility with WordPress.', 'wp-jquery-test' ); ?>
			<?php _e( 'The jQuery UI update does not depend on the jQuery update and is tentatively planned for WordPress 5.6 depending on test results.', 'wp-jquery-test' ); ?>
		</p>

		<p>
			<?php
				printf(
					__( 'If you find a bug in WordPress Admin or in a jQuery plugin while testing, please report it at <a href="%s">(TBD)</a>.', 'wp-jquery-test' ),
					'https://github.com/WordPress'
				);
			?>
			<?php
				printf(
					__( 'If the bug is in a jQuery plugin please also check for <a href="%s">a new version on NPM that fixes the issue</a>.', 'wp-jquery-test' ),
					'https://www.npmjs.com/search?q=keywords:jquery-plugin'
				);
			?>
		</p>

		<form method="post">
		<?php wp_nonce_field( 'wp-jquery-test-settings', 'wp-jquery-test-save' ); ?>
		<table class="form-table">
			<tr class="classic-editor-user-options">
				<th scope="row"><?php _e( 'jQuery version', 'wp-jquery-test' ); ?></th>
				<td>
					<p>
						<input type="radio" name="jquery-test-version" id="version-default" value="default"
							<?php checked( $settings['version'] === 'default' ); ?>
						/>
						<label for="version-default"><?php _e( 'Default', 'wp-jquery-test' ); ?></label>
					</p>
					<p>
						<input type="radio" name="jquery-test-version" id="version-351" value="3.5.1"
							<?php checked( $settings['version'] === '3.5.1' ); ?>
						/>
						<label for="version-351">3.5.1</label>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php _e( 'Enable jQuery Migrate', 'wp-jquery-test' ); ?></th>
				<td>
					<p>
						<input type="radio" name="jquery-test-migrate" id="migrate-enable" value="enable"
							<?php checked( $settings['migrate'] === 'enable' ); ?>
						/>
						<label for="migrate-enable"><?php _e( 'Enable', 'wp-jquery-test' ); ?></label>
					</p>
					<p>
						<input type="radio" name="jquery-test-migrate" id="migrate-disable" value="disable"
							<?php checked( $settings['migrate'] === 'disable' ); ?>
						/>
						<label for="migrate-disable"><?php _e( 'Disable', 'wp-jquery-test' ); ?></label>
					</p>
				</td>
			</tr>

			<tr class="classic-editor-user-options">
				<th scope="row"><?php _e( 'jQuery UI version', 'wp-jquery-test' ); ?></th>
				<td>
					<p>
						<input type="radio" name="jquery-test-uiversion" id="uiversion-default" value="default"
							<?php checked( $settings['uiversion'] === 'default' ); ?>
						/>
						<label for="uiversion-default"><?php _e( 'Default', 'wp-jquery-test' ); ?></label>
					</p>
					<p>
						<input type="radio" name="jquery-test-uiversion" id="uiversion-1121" value="1.12.1"
							<?php checked( $settings['uiversion'] === '1.12.1' ); ?>
						/>
						<label for="uiversion-1121">1.12.1</label>
					</p>
				</td>
			</tr>
		</table>
		<?php submit_button(); ?>
		</form>
		</div>
		<?php
	}

	public static function add_menu_item() {
		$menu_title = __( 'jQuery Update Test', 'wp-jquery-test' );
		$page_title = __( 'jQuery update test settings', 'wp-jquery-test' );

		add_plugins_page( $page_title, $menu_title, 'install_plugins', 'wp-jquery-update-test', array( __CLASS__, 'settings_ui' ) );
	}

	/**
	 * Set defaults on activation.
	 */
	public static function activate() {
		register_uninstall_hook( __FILE__, array( __CLASS__, 'uninstall' ) );

		$defaults = array(
			'version'   => 'default',
			'migrate'   => 'disable',
			'uiversion' => 'default',
		);

		add_site_option( 'wp-jquery-test-settings', $defaults );
	}

	/**
	 * Delete the options on uninstall.
	 */
	public static function uninstall() {
		delete_site_option( 'wp-jquery-test-settings' );
	}
}

add_action( 'plugins_loaded', array( 'WP_Jquery_Update_Test', 'init_actions' ) );
endif;
