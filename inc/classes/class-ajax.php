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

		add_action('wp_ajax_nopriv_ctto/ajax/image/content', [$this, 'image_content'], 10, 0);
		add_action('wp_ajax_ctto/ajax/image/content', [$this, 'image_content'], 10, 0);

		add_action('wp_ajax_nopriv_ctto/ajax/image/content/update', [$this, 'image_content_update'], 10, 0);
		add_action('wp_ajax_ctto/ajax/image/content/update', [$this, 'image_content_update'], 10, 0);

	}
	public function overalies_texts() {
		$json = ['hooks' => ['load-overalies']];
		if (isset($_POST['_posts'])) {
			$_posts = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', stripslashes(html_entity_decode(isset($_POST['_posts'])?$_POST['_posts']:'{}'))), true);
			$overalies = [];
			foreach ($_posts as $i => $row) {
				$overalies[] = [
					'type'		=> $row['type'],
					'image_id'	=> $row['image_id'],
					'post_id'	=> $row['post_id'],
					'text'		=> ($row['type'] == 'attachment')?get_option(
						'overaly-content-' . $row['post_id'] . '-' . $row['image_id'],
						false
					):get_post_meta((int) $row['post_id'], 'overaly-content', true)
				];
			}
		}
		$json['overalies'] = $overalies;
		wp_send_json_success($json, 200);
	}
	public function image_content() {
		$json = ['hooks' => ['image-content']];
		if (isset($_POST['post_id']) && isset($_POST['image_id'])) {
			$json['post_id'] = $_POST['post_id'];
			$json['image_id'] = $_POST['image_id'];
			$json = (object) $json;
			$json->text = get_option(
				'overaly-content-' . $json->post_id . '-' . $json->image_id,
				false
			);
		}
		// 
		wp_send_json_success($json, 200);
	}
	public function image_content_update() {
		$json = (object) ['hooks' => ['image-content-updated']];
		if (isset($_GET['post_id']) && isset($_GET['image_id']) && isset($_GET['text'])) {
			$json->post_id = $_GET['post_id'];
			$json->image_id = $_GET['image_id'];

			$args = (object) [
				'key'		=> 'overaly-content-' . $json->post_id . '-' . $json->image_id,
				'value'		=> sanitize_textarea_field($_GET['text'])
			];
			$args->updated = update_option($args->key, $args->value);
			$json->query = $args;
		}
		wp_send_json_success($json, 200);
	}
	
}
