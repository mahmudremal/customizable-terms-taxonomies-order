<?php
/**
 * LoadmorePosts
 *
 * @package TermsTaxonomyOrder
 */

namespace CTTO\inc;
use CTTO\inc\Traits\Singleton;

class Metabox {
	use Singleton;
	protected function __construct() {
		$this->setup_hooks();
	}
	public function setup_hooks() {
		add_action('save_post', [$this, 'save_post'], 10, 1);
		add_action('add_meta_boxes', [$this, 'add_meta_boxes'], 10, 0);
	}
	public function save_post($post_id) {
		if (isset($_POST['overaly-content'])) {
			update_post_meta($post_id, 'overaly-content', sanitize_text_field($_POST['overaly-content']));
		}
	}
	public function add_meta_boxes() {
		$screens = ['post', 'page', 'book'];
		foreach ($screens as $screen) {
			add_meta_box('overaly-content', __('Overaly content', 'ctto'), [$this, 'meta_box_content'], $screen, 'normal', 'high');
		}
	}
	public function meta_box_content() {
		global $post;
		$text = get_post_meta($post->ID, 'overaly-content', true);
		?>
		<textarea name="overaly-content" id="overaly-content-textarea" cols="30" rows="10" class="form-control"><?php echo esc_textarea($text); ?></textarea>
		<?php
	}
	
}
