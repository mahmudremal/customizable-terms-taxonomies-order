<?php
/**
 * This plugin ordered by a client and done by Remal Mahmud (fiverr.com/mahmud_remal). Authority dedicated to that cient.
 *
 * @wordpress-plugin
 * Plugin Name:       Terms taxonomies order
 * Plugin URI:        https://github.com/mahmudremal/customizable-terms-taxonomies-order/
 * Description:       Customizable filtering functionalities with backend meta boxes on single post edit screen and single term edit screen.
 * Version:           1.0.0
 * Requires at least: 6.2
 * Requires PHP:      7.4
 * Author:            Remal Mahmud
 * Author URI:        https://github.com/mahmudremal/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ctto
 * Domain Path:       /languages
 * 
 * @package QuizAndFilterSearch
 * @author  Remal Mahmud (https://github.com/mahmudremal)
 * @version 1.0.2
 * @link https://github.com/mahmudremal/customizable-terms-taxonomies-order/
 * @category	WordPress Plugin
 * @copyright	Copyright (c) 2024-26
 * 
 */

/**
 * Bootstrap the plugin.
 */



defined('CTTO_FILE__') || define('CTTO_FILE__', untrailingslashit(__FILE__));
defined('CTTO_DIR_PATH') || define('CTTO_DIR_PATH', untrailingslashit(plugin_dir_path(CTTO_FILE__)));
defined('CTTO_DIR_URI') || define('CTTO_DIR_URI', untrailingslashit(plugin_dir_url(CTTO_FILE__)));
defined('CTTO_BUILD_URI') || define('CTTO_BUILD_URI', untrailingslashit(CTTO_DIR_URI) . '/assets/build');
defined('CTTO_BUILD_PATH') || define('CTTO_BUILD_PATH', untrailingslashit(CTTO_DIR_PATH) . '/assets/build');
defined('CTTO_BUILD_JS_URI') || define('CTTO_BUILD_JS_URI', untrailingslashit(CTTO_DIR_URI) . '/assets/build/js');
defined('CTTO_BUILD_JS_DIR_PATH') || define('CTTO_BUILD_JS_DIR_PATH', untrailingslashit(CTTO_DIR_PATH) . '/assets/build/js');
defined('CTTO_BUILD_IMG_URI') || define('CTTO_BUILD_IMG_URI', untrailingslashit(CTTO_DIR_URI) . '/assets/build/src/img');
defined('CTTO_BUILD_CSS_URI') || define('CTTO_BUILD_CSS_URI', untrailingslashit(CTTO_DIR_URI) . '/assets/build/css');
defined('CTTO_BUILD_CSS_DIR_PATH') || define('CTTO_BUILD_CSS_DIR_PATH', untrailingslashit(CTTO_DIR_PATH) . '/assets/build/css');
defined('CTTO_BUILD_LIB_URI') || define('CTTO_BUILD_LIB_URI', untrailingslashit(CTTO_DIR_URI) . '/assets/build/library');
defined('CTTO_ARCHIVE_POST_PER_PAGE') || define('CTTO_ARCHIVE_POST_PER_PAGE', 9);
defined('CTTO_SEARCH_RESULTS_POST_PER_PAGE') || define('CTTO_SEARCH_RESULTS_POST_PER_PAGE', 9);
defined('CTTO_OPTIONS') || define('CTTO_OPTIONS', get_option('ctto'));
defined('CTTO_UPLOAD_DIR') || define('CTTO_UPLOAD_DIR', wp_upload_dir()['basedir'].'/custom_popup/');
defined('CTTO_AUDIO_DURATION') || define('CTTO_AUDIO_DURATION', 20);

require_once CTTO_DIR_PATH . '/inc/helpers/autoloader.php';
// require_once CTTO_DIR_PATH . '/inc/helpers/template-tags.php';


try {
	if (!function_exists('CTTO_get_instance')) {
		function CTTO_get_instance() {\CTTO\inc\Project::get_instance();}
		CTTO_get_instance();
	}
} catch (\Exception $e) {
	// echo "Exception: " . $e->getMessage();
} catch (\Error $e) {
	// echo "Error: " . $e->getMessage();
} finally {
	// Optional code that always runs
	// echo "Finally block executed.";
}
