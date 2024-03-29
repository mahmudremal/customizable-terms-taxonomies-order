<?php
/**
 * Enqueue theme assets
 *
 * @package TeddyBearCustomizeAddon
 */

namespace CTTO\inc;
use CTTO\inc\Traits\Singleton;

class Assets {
	use Singleton;
	protected function __construct() {
		// load class.
		$this->setup_hooks();
	}
	protected function setup_hooks() {
		add_action('wp_enqueue_scripts', [$this, 'register_styles']);
		add_action('wp_enqueue_scripts', [$this, 'register_scripts']);
		
		add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts'], 10, 1);
		add_filter('futurewordpress/project/ctto/javascript/siteconfig', [$this, 'siteConfig'], 1, 2);
	}
	public function register_styles() {
		// Register styles.
		$version = $this->filemtime(CTTO_BUILD_CSS_DIR_PATH . '/public.css');
		wp_register_style('ctto-public', CTTO_BUILD_CSS_URI . '/public.css', [], $version, 'all');
		// Enqueue Styles.
		wp_enqueue_style('ctto-public');
		// if($this->allow_enqueue()) {}
	}
	public function register_scripts() {
		// Register scripts.
		$version = $this->filemtime(CTTO_BUILD_JS_DIR_PATH.'/public.js');
		wp_register_script('ctto-public', CTTO_BUILD_JS_URI . '/public.js', ['jquery'], $version.'.'.rand(0, 999), true);
		wp_enqueue_script('ctto-public');
		wp_localize_script('ctto-public', 'fwpSiteConfig', apply_filters('futurewordpress/project/ctto/javascript/siteconfig', []));
	}
	private function allow_enqueue() {
		return (function_exists('is_checkout') && (is_checkout() || is_order_received_page() || is_wc_endpoint_url('order-received')));
	}
	/**
	 * Enqueue editor scripts and styles.
	 */
	public function enqueue_editor_assets() {
		$asset_config_file = sprintf('%s/assets.php', CTTO_BUILD_PATH);
		if (! file_exists($asset_config_file)) {
			return;
		}
		$asset_config = require_once $asset_config_file;
		if (empty($asset_config['js/editor.js'])) {
			return;
		}
		$editor_asset    = $asset_config['js/editor.js'];
		$js_dependencies = (! empty($editor_asset['dependencies'])) ? $editor_asset['dependencies'] : [];
		$version         = (! empty($editor_asset['version'])) ? $editor_asset['version'] : $this->filemtime($asset_config_file);
		// Theme Gutenberg blocks JS.
		if (is_admin()) {
			wp_enqueue_script(
				'aquila-blocks-js',
				CTTO_BUILD_JS_URI . '/blocks.js',
				$js_dependencies,
				$version,
				true
			);
		}
		// Theme Gutenberg blocks CSS.
		$css_dependencies = [
			'wp-block-library-theme',
			'wp-block-library',
		];
		wp_enqueue_style(
			'aquila-blocks-css',
			CTTO_BUILD_CSS_URI . '/blocks.css',
			$css_dependencies,
			$this->filemtime(CTTO_BUILD_CSS_DIR_PATH . '/blocks.css'),
			'all'
		);
	}
	public function admin_enqueue_scripts($curr_page) {
		global $post;
		// if(!in_array($curr_page, ['post-new.php', 'post.php', 'edit.php', 'order-terms'])) {return;}
		wp_register_style('ctto-admin', CTTO_BUILD_CSS_URI . '/admin.css', [], $this->filemtime(CTTO_BUILD_CSS_DIR_PATH . '/admin.css'), 'all');
		wp_register_script('ctto-admin', CTTO_BUILD_JS_URI . '/admin.js', ['jquery'], $this->filemtime(CTTO_BUILD_JS_DIR_PATH . '/admin.js'), true);
		
		// if(!in_array($curr_page, ['settings_page_ctto'])) {}
		wp_enqueue_style('ctto-admin');
		wp_enqueue_script('ctto-admin');
		wp_enqueue_style('ctto-public');wp_enqueue_script('ctto-admin');
		wp_localize_script('ctto-admin', 'fwpSiteConfig', apply_filters('futurewordpress/project/ctto/javascript/siteconfig', [
			'config' => [
				'product_id' => isset($_GET['post'])?(int) $_GET['post']:get_query_var('post',false)
			]
		], true));
	}
	private function filemtime($path) {
		return (file_exists($path)&&!is_dir($path))?filemtime($path):false;
	}
	public function siteConfig($args, $is_admin = false) {
		$args = wp_parse_args([
			'ajaxUrl'    		=> admin_url('admin-ajax.php'),
			'ajax_nonce' 		=> wp_create_nonce('futurewordpress/project/ctto/verify/nonce'),
			'is_admin' 			=> is_admin(),
			'buildPath'  		=> CTTO_BUILD_URI,
			'audioDuration'  	=> CTTO_AUDIO_DURATION,
			'siteLogo'			=> apply_filters('ctto/project/system/getoption', 'standard-sitelogo', false),
			'i18n'				=> ['pls_wait' => __('Please wait...', 'ctto')],
			'local'				=> apply_filters('ctto/project/system/get_locale', get_user_locale()),
			'post_id'			=> get_the_ID()
			
		], (array) $args);
		
		if ($is_admin) {
			// admin scripts here
		} else {
			// public scripts here.
			$args['notifications'] = apply_filters('ctto/project/assets/notifications', false, []);
		}
		
		return $args;
	}
	public function wp_denqueue_scripts() {}
	public function admin_denqueue_scripts() {
		if(! isset($_GET['page']) ||  $_GET['page'] !='crm_dashboard') {return;}
		wp_dequeue_script('qode-tax-js');
	}
	public function style_loader_src($src, $handle) {
		if ($handle === 'ctto-public') {
			$version = $this->filemtime(str_replace(site_url('/'),ABSPATH,$src));
			// $src = add_query_arg('ver', $version, $src);
			$src = $src.'v'.$version;
		}
		return $src;
	}
}
