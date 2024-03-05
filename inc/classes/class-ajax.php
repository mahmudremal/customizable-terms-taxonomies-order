<?php
/**
 * Block Patterns
 *
 * @package TermsTaxonomyOrder
 */

namespace CTTO\inc;

use CTTO\inc\Traits\Singleton;

class Ajax {
	use Singleton;
	protected function __construct() {
		$this->setup_hooks();
	}
	protected function setup_hooks() {
		add_action('wp_ajax_nopriv_ctto/ajax/post/content', [$this, 'overalies_texts'], 10, 0);
		add_action('wp_ajax_ctto/ajax/post/content', [$this, 'overalies_texts'], 10, 0);

	}
	public function overalies_texts() {
		$json = ['hooks' => ['load-overalies']];
		if (isset($_POST['_posts'])) {
			$_posts = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', stripslashes(html_entity_decode(isset($_POST['_posts'])?$_POST['_posts']:'{}'))), true);
			$overalies = [];
			foreach ($_posts as $post_id) {
				$overalies[] = [
					'post_id'	=> $post_id,
					'text'		=> get_post_meta((int) $post_id, 'overaly-content', true)
				];
			}
		}
		$json['overalies'] = $overalies;
		wp_send_json_success($json, 200);
	}
	
}
