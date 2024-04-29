<?php
/**
 * Bootstraps the Theme.
 *
 * @package TermsTaxonomyOrder
 */
namespace CTTO\inc;
use CTTO\inc\Traits\Singleton;

class Order_Terms {
	use Singleton;
	private $get_terms_calling;
	protected function __construct() {
		// Load class.
		$this->setup_hooks();
	}
	protected function setup_hooks() {
		$this->get_terms_calling = false;

		add_action('load-edit.php', [$this, 'load_edit_php'], 10, 0);

		add_action('admin_menu', [$this, 'custom_page_register_menu'], 10, 0);

		add_action('wp_footer', [$this, 'wp_footer'], 10, 0);

		/**
		 * Paused because term ordering by meta not working properly
		 * https://spring-architecten.concept-webactueel-7.com/wp-admin/tools.php?page=string-locator&edit-file=portfolio.php&file-reference=elementor-pro&file-type=plugin&string-locator-line=580&string-locator-linepos=19&string-locator-path=%2Fhome%2Fu370324020%2Fdomains%2Fconcept-webactueel-7.com%2Fpublic_html%2Fspring-architecten%2Fwp-content%2Fplugins%2Felementor-pro%2Fmodules%2Fposts%2Fwidgets%2Fportfolio.php
		 */
		// add_filter('get_terms', [$this, 'get_terms'], 10, 4);
	}
	public function custom_page_register_menu() {
		add_menu_page(__('Order Categories', 'ctto'), __('Order Categories', 'ctto'), 'manage_options', 'order-terms', [$this, 'order_terms'], CTTO_BUILD_URI . '/icons/sort.svg', 10);
	}
	public function load_edit_php() {
		if (get_current_screen()->id != 'edit-page') {return;}
	}
	public function order_terms() {
		?>
		<div class="term_order">
			<div class="term_order__container">
				<div class="term_order__wrap">
					<div class="term_order__head"></div>
					<div class="term_order__body">
						<div class="term_order__sections">
							<div class="term_order__section">
								<?php foreach ($this->get_taxonomies_terms() as $taxonomy => $terms) : ?>
								<?php if (count($terms) <= 0) {continue;} ?>
								<form class="term_order__form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
									<input type="hidden" name="action" value="ctto/project/ajax/taxonomy/order">
									<?php wp_nonce_field('ctto/project/ajax/taxonomy/order', 'texonomy_order', true, true); ?>
									<div class="term_order__nav">
										<h3 class="term_order__caption"><?php echo esc_html($taxonomy); ?></h3>
										<div class="term_order__submit">
											<input type="submit" value="Update">
										</div>
									</div>
									
									<ul class="term_order__list" data-group="<?php echo esc_attr($taxonomy); ?>">
										<?php foreach ($terms as $index => $term) : ?>
										<li class="term_order__listitem">
											<!-- [<?php echo esc_attr($term->taxonomy); ?>] -->
											<input type="hidden" name="terms[]" value="<?php echo esc_attr($term->term_id); ?>">
											<span class="term_order__listhandle">
												<span class="dashicons dashicons-move"></span>
											</span>
											<span class="term_order__listlabel">
												<span class="term_order__listlabel_name"><?php echo esc_html($term->name); ?></span>
												<span class="term_order__listlabel_quantity">(<?php echo esc_html($term->count); ?>)</span>
											</span>
										</li>
										<?php endforeach; ?>
										
									</ul>
								</form>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
					<div class="term_order__foot"></div>
				</div>
			</div>
		</div>
		<?php
	}
	public function get_taxonomies_terms() {
		$result = [];
		$args = [
			'public'   => true,
			// '_builtin' => false,
		]; 
		$output = 'objects'; // or objects
		$operator = 'and'; // 'and' or 'or'
		$taxonomies = get_taxonomies($args, $output, $operator);
		foreach ($taxonomies as $taxonomy) {
			$result[$taxonomy->label] = $this->get_sorted_terms($taxonomy->name);
		}
		return $result;
	}
	public function get_sorted_terms($taxonomy) {
		$terms = get_terms([
			'taxonomy' => $taxonomy,
			'hide_empty' => false,
			'order' => 'ASC',
			'orderby'	=> 'meta_value_num',
			'hierarchical' => false,
			'parent' => 0,
			'meta_query' => [
				'key' => '_order_accordingly',
				'type' => 'NUMERIC',
			]
		]);
		foreach ($terms as $key => $term) {
			$terms[$key]->order_by = get_term_meta($term->term_id, '_order_accordingly', true);
		}
		// 
		try {
			usort($terms, function($a, $b) {
				return $a->order_by - $b->order_by;
			});
		} catch (\Error $th) {
			//throw $th;
		}
		return $terms;
	}

	public function get_terms($terms, $taxonomy, $query_vars, $term_query) {
		if ($this->get_terms_calling) {
			// $this->get_terms_calling = false;
			// return $terms;
		}
		// $this->get_terms_calling = true;

		if (in_array('category', (array) $taxonomy)) {
			// print_r($query_vars);
			$this->get_terms_calling[] = [
				'term_id'		=> $term->term_id,
				'term_order'	=> get_term_meta($term->term_id, '_order_accordingly', true)
			];
			// $query_vars = wp_parse_args($query_vars, [
			// 	'order' => 'ASC',
			// 	'orderby'	=> 'meta_value_num',
			// 	'meta_query' => [
			// 		'key' => '_order_accordingly',
			// 		'type' => 'NUMERIC',
			// 	],
			// ]);
			// return get_terms($query_vars);
		}
		
		return $terms;
	}

	public function wp_footer() {
		if (is_admin()) {return;}
		$terms_order = $this->get_sorted_terms('category');
		foreach ($terms_order as $index => $term) {
			$terms_order[$index] = [
				'slug'			=> $term->slug,
				'name'			=> $term->name,
				'term_id'		=> $term->term_id,
				'order_by'		=> (int) $term->order_by,
			];
		}
		$ovaraly_texts = [];
		if (is_archive()) {
			$ovaraly_texts[] = '';
		}

		?>
		<script>var terms_orders = <?php echo json_encode($terms_order); ?>;</script>
		<script>var ovaraly_texts = <?php echo json_encode($ovaraly_texts); ?>;</script>
		<?php
	}
}
