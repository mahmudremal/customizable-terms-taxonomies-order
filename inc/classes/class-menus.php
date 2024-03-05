<?php
/**
 * Register Menus
 *
 * @package ESignBindingAddons
 */
namespace CTTO\inc;
use CTTO\inc\Traits\Singleton;
class Menus {
	use Singleton;
	protected function __construct() {
		// load class.
		$this->setup_hooks();
	}
	protected function setup_hooks() {
		/**
		 * Actions.
		 */
		// add_action('init', [$this, 'register_menus']);
		add_filter('ctto/project/settings/general', [$this, 'general'], 10, 1);
		add_filter('ctto/project/settings/fields', [$this, 'menus'], 10, 1);
		add_action('in_admin_header', [$this, 'in_admin_header'], 100, 0);
	}
	public function register_menus() {
		register_nav_menus([
			'aquila-header-menu' => esc_html__('Header Menu', 'ctto'),
			'aquila-footer-menu' => esc_html__('Footer Menu', 'ctto'),
		]);
	}
	/**
	 * Get the menu id by menu location.
	 *
	 * @param string $location
	 *
	 * @return integer
	 */
	public function get_menu_id($location) {
		// Get all locations
		$locations = get_nav_menu_locations();
		// Get object id by location.
		$menu_id = ! empty($locations[$location]) ? $locations[$location] : '';
		return ! empty($menu_id) ? $menu_id : '';
	}
	/**
	 * Get all child menus that has given parent menu id.
	 *
	 * @param array   $menu_array Menu array.
	 * @param integer $parent_id Parent menu id.
	 *
	 * @return array Child menu array.
	 */
	public function get_child_menu_items($menu_array, $parent_id) {
		$child_menus = [];
		if (! empty($menu_array) && is_array($menu_array)) {
			foreach ($menu_array as $menu) {
				if (intval($menu->menu_item_parent) === $parent_id) {
					array_push($child_menus, $menu);
				}
			}
		}
		return $child_menus;
	}
	public function in_admin_header() {
		if (! isset($_GET['page']) || $_GET['page'] != 'crm_dashboard') {return;}
		
		remove_all_actions('admin_notices');
		remove_all_actions('all_admin_notices');
		// add_action('admin_notices', function () {echo 'My notice';});
	}
	/**
	 * Supply necessry tags that could be replace on frontend.
	 * 
	 * @return string
	 * @return array
	 */
	public function commontags($html = false) {
		$arg = [];$tags = [
			'username', 'sitename', 
		];
		if ($html === false) {return $tags;}
		foreach($tags as $tag) {
			$arg[] = sprintf("%s{$tag}%s", '<code>{', '}</code>');
		}
		return implode(', ', $arg);
	}
	public function contractTags($tags) {
		$arg = [];
		foreach($tags as $tag) {
			$arg[] = sprintf("%s{$tag}%s", '<code>{', '}</code>');
		}
		return implode(', ', $arg);
	}
	/**
	 * WordPress Option page.
	 * 
	 * @return array
	 */
	public function general($args) {
		return $args;
	}
	public function menus($args) {
		// apply_filters('ctto/project/system/isactive', 'standard-enable')
		$args['standard']	= [
			'title'							=> __('General', 'ctto'),
			'description'					=> __('General settings for teddy-bear customization popup.', 'ctto'),
			'fields'						=> [
				[
					'id' 					=> 'standard-enable',
					'label'					=> __('Enable', 'ctto'),
					'description'			=> __('Mark to enable teddy-bear customization popup.', 'ctto'),
					'type'					=> 'checkbox',
					'default'				=> false
				],
				// [
				// 	'id' 					=> 'standard-global',
				// 	'label'					=> __('Global product', 'ctto'),
				// 	'description'			=> __('Select a global product that will be replaced if requsted product doesn\'t have any customization popup set.', 'ctto'),
				// 	'type'					=> 'select',
				// 	'default'				=> '',
				// 	'options'				=> $this->get_query(['post_type' => 'product', 'type' => 'option', 'limit' => 500])
				//],
				[
					'id' 					=> 'standing-global',
					'label'					=> __('Global standing product', 'ctto'),
					'description'			=> __('Select a global standing product that will be replaced if requsted product doesn\'t have any customization popup set.', 'ctto'),
					'type'					=> 'select',
					'default'				=> '',
					'options'				=> $this->get_query(['post_type' => 'product', 'type' => 'option', 'limit' => 500, 'noaccessory' => true])
				],
				[
					'id' 					=> 'sitting-global',
					'label'					=> __('Global sitting product', 'ctto'),
					'description'			=> __('Select a global sitting product that will be replaced if requsted product doesn\'t have any customization popup set.', 'ctto'),
					'type'					=> 'select',
					'default'				=> '',
					'options'				=> $this->get_query(['post_type' => 'product', 'type' => 'option', 'limit' => 500, 'noaccessory' => true])
				],
				[
					'id' 					=> 'standard-forceglobal',
					'label'					=> __('Force global', 'ctto'),
					'description'			=> __('Forcefully globalize this product for all products whether there are customization exists or not.', 'ctto'),
					'type'					=> 'checkbox',
					'default'				=> false
				],
				[
					'id' 					=> 'standard-category',
					'label'					=> __('Cross-sale category', 'ctto'),
					'description'			=> __('Select a cross sale category to suggest on added to cart confirmation popup. Each product under your selected category will be displayed to confirmation popup.', 'ctto'),
					'type'					=> 'select',
					'options'				=> $this->get_query(['post_type' => 'product', 'type' => 'option', 'limit' => 500, 'queryType' => 'term']),
					'default'				=> false
				],
				[
					'id' 					=> 'standard-sitelogo',
					'label'					=> __('Header logo', 'ctto'),
					'description'			=> __('Full url of your site popup logo for popup header. This could be any kind of image formate and optimized resulation.', 'ctto'),
					'type'					=> 'url',
					'default'				=> false
				],
				[
					'id' 					=> 'standard-standingdoll',
					'label'					=> __('Standing teddy image', 'ctto'),
					'description'			=> __('Full url of a standing teddy bear image for first step selecting. This could be any kind of image formate and optimized resulation.', 'ctto'),
					'type'					=> 'url',
					'default'				=> false
				],
				[
					'id' 					=> 'standard-sittingdoll',
					'label'					=> __('Sitting teddy image', 'ctto'),
					'description'			=> __('Full url of a sitting teddy bear image for first step selecting. This could be any kind of image formate and optimized resulation.', 'ctto'),
					'type'					=> 'url',
					'default'				=> false
				],
				[
					'id' 					=> 'standard-accessory',
					'label'					=> __('Default accessory', 'ctto'),
					'description'			=> __('Select a default accessoty that will be effective on customization confirmation.', 'ctto'),
					'type'					=> 'select',
					'options'				=> $this->get_query(['post_type' => 'page', 'type' => 'option', 'limit' => 500]),
					'default'				=> false
				],
			]
		];
		$args['default']		= [
			'title'							=> __('Teddy Meta', 'ctto'),
			'description'					=> __('Teddy bear\'s default data that will be replaced if meta on specific product not exists or empty exists. Existing data won\'t be replaced.', 'ctto'),
			'fields'						=> [
				[
					'id' 						=> 'default-eye',
					'label'					=> __('Eye color', 'ctto'),
					'description'			=> __('Teddy\'s default eye color that will be replaced if meta not exists on birth certificates.', 'ctto'),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'default-brow',
					'label'					=> __('Fur color', 'ctto'),
					'description'			=> __('Teddy\'s default brow color that will be replaced if meta not exists on birth certificates.', 'ctto'),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'default-weight',
					'label'					=> __('Teddy\'s weight', 'ctto'),
					'description'			=> __('Teddy\'s default weight that will be replaced if meta not exists on birth certificates.', 'ctto'),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'default-height',
					'label'					=> __('Teddy\'s height', 'ctto'),
					'description'			=> __('Teddy\'s default height that will be replaced if meta not exists on birth certificates.', 'ctto'),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 						=> 'default-accessoriesUrl',
					'label'					=> __('Accessories url', 'ctto'),
					'description'			=> __('Accessories url that will be applied after user added an item on cart through customization process. It will redirect user to this url when user choose to purches accessories.', 'ctto'),
					'type'					=> 'text',
					'default'				=> ''
				],
			]
		];
		$args['names']			= [
			'title'							=> __('Teddy name', 'ctto'),
			'description'					=> __('List of teddy names that will include in a lottery when user choose to suggest a teddy name.', 'ctto'),
			'fields'						=> [
				[
					'id' 					=> 'names-randomize',
					'label'					=> __('Randomize names', 'ctto'),
					'description'			=> __('Mark to randomize these names before sending to client.', 'ctto'),
					'type'					=> 'checkbox',
					'default'				=> false
				],
				...$this->optionaize_teddy_names(),
				[
					'id' 					=> 'do_repeater_name',
					'label'					=> '',
					'description'			=> false,
					'type'					=> 'button',
					'default'				=> __('Add another', 'ctto')
				],
			]
		];
		$args['addons']			= [
			'title'							=> __('Addons', 'ctto'),
			'description'					=> __('Necessary addons for after customization process. Including packaging wrappings.', 'ctto'),
			'fields'						=> [
				[
					'id' 					=> 'addons-enable',
					'label'					=> __('Enable', 'ctto'),
					'description'			=> __('Mark to enable wrapping addons.', 'ctto'),
					'type'					=> 'checkbox',
					'default'				=> false
				],
				[
					'id' 					=> 'addons-thumbnail',
					'label'					=> __('Thumbnail URL', 'ctto'),
					'description'			=> __('Full thumbnail URL that will be shown on checkout screen. By default or if you leave blank, it\'ll replace with site icon.', 'ctto'),
					'type'					=> 'url',
					'default'				=> ''
				],
				[
					'id' 					=> 'addons-title',
					'label'					=> __('Title', 'ctto'),
					'description'			=> __('Give here a title not more then 50 chars. Will incude on H3 tag.', 'ctto'),
					'type'					=> 'text',
					'default'				=> false
				],
				[
					'id' 					=> 'addons-subtitle',
					'label'					=> __('Sub-title', 'ctto'),
					'description'			=> __('Give here a short subtitle not more then 30 chars. Will include in H4 tag.', 'ctto'),
					'type'					=> 'text',
					'default'				=> false
				],
				[
					'id' 					=> 'addons-subtitle',
					'label'					=> __('Sub-title', 'ctto'),
					'description'			=> __('Give here a short subtitle not more then 30 chars. Will include in H4 tag.', 'ctto'),
					'type'					=> 'text',
					'default'				=> false
				],
				[
					'id' 					=> 'addons-text',
					'label'					=> __('Description', 'ctto'),
					'description'			=> __('Give here a full descrition that suppose to be short and could be able to give a clear idea about what it is.', 'ctto'),
					'type'					=> 'text',
					'default'				=> false
				],
				[
					'id' 					=> 'addons-feetitle',
					'label'					=> __('Fee label', 'ctto'),
					'description'			=> __('wrapping package feee label for invice, checkout etc.', 'ctto'),
					'type'					=> 'text',
					'default'				=> false
				],
				[
					'id' 					=> 'addons-feeamount',
					'label'					=> __('Fee Amount', 'ctto'),
					'description'			=> __('The amount for the label or wrapping box.', 'ctto'),
					'type'					=> 'text',
					'default'				=> false
				],
			]
		];
		$args['badges']			= [
			'title'							=> __('Badges', 'ctto'),
			'description'					=> __('Products shop grid featured & Best Seller badges', 'ctto'),
			'fields__'						=> [
				[
					'id' 						=> 'badges-enable',
					'label'					=> __('Enable', 'ctto'),
					'description'			=> __('Mark to enable badges on products grid.', 'ctto'),
					'type'					=> 'checkbox',
					'default'				=> false
				],
				[
					'id' 						=> 'badges-featured',
					'label'					=> __('Enable Featured', 'ctto'),
					'description'			=> __('Mark to enable individual featured badge.', 'ctto'),
					'type'					=> 'checkbox',
					'default'				=> false
				],
				[
					'id' 						=> 'badges-bestseller',
					'label'					=> __('Enable Best Seller', 'ctto'),
					'description'			=> __('Mark to enable individual best seller badge.', 'ctto'),
					'type'					=> 'checkbox',
					'default'				=> false
				],
				[
					'id' 						=> 'badges-featured-bgcolor',
					'label'					=> __('Featured BG color', 'ctto'),
					'description'			=> __('Define a color as background color for featured image badge.', 'ctto'),
					'type'					=> 'color',
					'default'				=> '#e63f51'
				],
				[
					'id' 						=> 'badges-featured-color',
					'label'					=> __('Featured Text color', 'ctto'),
					'description'			=> __('Define a color as text color for featured image badge.', 'ctto'),
					'type'					=> 'color',
					'default'				=> '#ffffff'
				],
				[
					'id' 						=> 'badges-bestseller-bgcolor',
					'label'					=> __('Bestseller BG color', 'ctto'),
					'description'			=> __('Define a color as background color for bestseller image badge.', 'ctto'),
					'type'					=> 'color',
					'default'				=> '#FFCF02'
				],
				[
					'id' 						=> 'badges-bestseller-color',
					'label'					=> __('Bestseller Text color', 'ctto'),
					'description'			=> __('Define a color as text color for bestseller image badge.', 'ctto'),
					'type'					=> 'color',
					'default'				=> '#333'
				],
				[
					'id' 						=> 'badges-bestseller',
					'label'					=> __('Enable Best Seller', 'ctto'),
					'description'			=> __('Mark to enable individual best seller badge.', 'ctto'),
					'type'					=> 'checkbox',
					'default'				=> false
				],
				[
					'id' 						=> 'badges-onsale',
					'label'					=> __('Enable On-Sale', 'ctto'),
					'description'			=> __('Mark to enable offer/On Sale badge.', 'ctto'),
					'type'					=> 'checkbox',
					'default'				=> false
				],
				[
					'id' 						=> 'badges-onsale-bgcolor',
					'label'					=> __('On-sale BG color', 'ctto'),
					'description'			=> __('Define a color as background color for onsale badge.', 'ctto'),
					'type'					=> 'color',
					'default'				=> '#FFCF02'
				],
				[
					'id' 						=> 'badges-onsale-color',
					'label'					=> __('On-sale Text color', 'ctto'),
					'description'			=> __('Define a color as text color for onsale badge.', 'ctto'),
					'type'					=> 'color',
					'default'				=> '#333'
				],
			],
			'fields'						=> [
				...$this->optionaize_teddy_badges(),
				[
					'id' 					=> 'do_repeater_badge',
					'label'					=> '',
					'description'			=> false,
					'type'					=> 'button',
					'default'				=> __('Add another', 'ctto')
				],
			]
		];
		$args['email']			= [
			'title'							=> __('Email', 'ctto'),
			'description'					=> __('Email template & necessey informations.', 'ctto'),
			'fields'						=> [
				[
					'id' 					=> 'email-enable_shipped',
					'label'					=> __('Enable shipped email', 'ctto'),
					'description'			=> __('Mark to enable shipped event email confirmation.', 'ctto'),
					'type'					=> 'checkbox',
					'default'				=> false
				],
				[
					'id' 					=> 'email-shipped_cc',
					'label'					=> __('Shipped email CC', 'ctto'),
					'description'			=> __('Give here an email address if you wish to send a carbon copy.', 'ctto'),
					'type'					=> 'text',
					'default'				=> ''
				],
				[
					'id' 					=> 'email-shipped_subject',
					'label'					=> __('Enable shipped email', 'ctto'),
					'description'			=> __('Mark to enable shipped event email confirmation.', 'ctto'),
					'type'					=> 'text',
					'default'				=> __('Your Order shipped successfully', 'ctto')
				],
				[
					'id' 					=> 'email-shipped_template',
					'label'					=> __('Shipped template', 'ctto'),
					'description'			=> __('Give here shipping text or html email template with inlined css & no js.', 'ctto'),
					'type'					=> 'textarea',
					'default'				=> sprintf(
						__("Hey {{customer}},\nWe're glad to say that your order has been shipped successfully.\nBest Wishes", 'ctto'),
						// 
					)
				],
				[
					'id' 					=> 'email-shipped_htmlmode',
					'label'					=> __('Shipped email formate', 'ctto'),
					'description'			=> __('Select html if you give html contents on the above field.', 'ctto'),
					'type'					=> 'radio',
					'default'				=> 'text',
					'options'				=> [
						'text'				=> 'Text mode',
						'html'				=> 'HTML mode'
					]
				],
			]
		];
		$args['order']			= [
			'title'							=> __('Order', 'ctto'),
			'description'					=> __('Order information an necessery data.', 'ctto'),
			'fields'						=> [
				[
					'id' 					=> 'order-attach_status',
					'label'					=> __('Attach certificates', 'ctto'),
					'description'			=> __('Give here all WC Order status slug, on that status changed event, certificates will be attached with confirmation.', 'ctto'),
					'type'					=> 'text',
					'default'				=> 'shipped, completed'
				],
				[
					'id' 					=> 'order-certificate_email',
					'label'					=> __('Certificates email', 'ctto'),
					'description'			=> __('Give here order status slug so that when order thatus changed to this slug, it will send a seperate certificate email template with certificates attached.', 'ctto'),
					'type'					=> 'text',
					'default'				=> 'completed'
				],
				[
					'id' 					=> 'order-avoid_askvoice',
					'label'					=> __('Avoid voice button', 'ctto'),
					'description'			=> __('Give here those order status slug that will act like when order status changed to that slug and send a confirmation email, it won\'t put an Send voice file button.', 'ctto'),
					'type'					=> 'text',
					'default'				=> 'shipped, completed'
				],
			]
		];
		$args['voice']			= [
			'title'							=> __('Voice', 'ctto'),
			'description'					=> __('Voice template & necessey informations.', 'ctto'),
			'fields'						=> [

				[
					'id' 					=> 'voice-reminder_enable',
					'label'					=> __('Enable Voice Reminding', 'ctto'),
					'description'			=> __('Marking this checkbox will apear a link button after single order item.', 'ctto'),
					'type'					=> 'checkbox',
					'default'				=> true
				],
				[
					'id' 					=> 'voice-reminder_label',
					'label'					=> __('Button text', 'ctto'),
					'description'			=> __('Give here a button text for the "Send Recorded file" button.', 'ctto'),
					'type'					=> 'text',
					'default'				=> __('Send Recorded voice', 'ctto')
				],
				[
					'id' 					=> 'voice-reminder_bg',
					'label'					=> __('Button Background', 'ctto'),
					'description'			=> __('Pick a color for the button.', 'ctto'),
					'type'					=> 'color',
					'default'				=> '#e63f51'
				],
				[
					'id' 					=> 'voice-reminder_color',
					'label'					=> __('Text Color', 'ctto'),
					'description'			=> __('Pick a color for the button text.', 'ctto'),
					'type'					=> 'color',
					'default'				=> '#ffffff'
				],
				[
					'id' 					=> 'voice-reminder_reciever',
					'label'					=> __('Voice Reciever', 'ctto'),
					'description'			=> sprintf(
						__('Give here an Email address where clients would be replied with. Site admin address is %s', 'ctto'),
						get_option('admin_email')
					),
					'type'					=> 'email',
					'default'				=> get_option('admin_email')
				],
				[
					'id' 					=> 'voice-reminder_subject',
					'label'					=> __('Email Subject', 'ctto'),
					'description'			=> sprintf(
						__('Give here an Email subject format that would be replaced with these magic word below. For Order ID: %s, for Item ID: %s.', 'ctto'),
						'<strong>{{order_id}}</strong>', '<strong>{{item_id}}</strong>'
					),
					'type'					=> 'text',
					'default'				=> 'Order #{{order_id}}'
				],
				// [
				// 	'id' 					=> 'voice-reminder_orderstatuses',
				// 	'label'					=> __('Visible on Statuses', 'ctto'),
				// 	'description'			=> __('Give here all of the order statuses those are allowed to show voice reminder button visibility.', 'ctto'),
				// 	'type'					=> 'text',
				// 	'default'				=> str_replace('wc-', '', implode(', ', function_exists('wc_get_order_statuses')?array_keys((array) wc_get_order_statuses()):['processing']))
				//],
				
			]
		];
		$args['certificate']	= [
			'title'							=> __('Certificate', 'ctto'),
			'description'					=> __('Certificate template & necessey informations.', 'ctto'),
			'fields'						=> [
				[
					'id' 					=> 'certificate-enable',
					'label'					=> __('Enable Certification', 'ctto'),
					'description'			=> __('Mark this option to enable or disable certification on order line.', 'ctto'),
					'type'					=> 'checkbox',
					'default'				=> true
				],
				[
					'id' 					=> 'certificate-onstatuses',
					'label'					=> __('Order Statuses', 'ctto'),
					'description'			=> __('Give here those order statuses where you want to allow certificates.', 'ctto'),
					'type'					=> 'text',
					'default'				=> 'completed, shipped'
				],
				[
					'id' 					=> 'certificate-myacc-enable',
					'label'					=> __('Abailable on My-Account', 'ctto'),
					'description'			=> __('Mark this option to show available certificates on user bashboard called my-account order details screen.', 'ctto'),
					'type'					=> 'checkbox',
					'default'				=> true
				],
				[
					'id' 					=> 'certificate-404template',
					'label'					=> __('404 Template', 'ctto'),
					'description'			=> __('Select a template for certificate not found issue.', 'ctto'),
					'type'					=> 'select',
					'default'				=> false,
					'options'				=> $this->get_query(['post_type' => 'elementor_library', 'type' => 'option', 'limit' => 500])
				],
				
			]
		];
		$args['translate']		= [
			'title'							=> __('Translate', 'ctto'),
			'description'					=> __('Setup your translations related informations here.', 'ctto'),
			'fields'						=> [
				[
					'id' 					=> 'translate-enable',
					'label'					=> __('Enable translation', 'ctto'),
					'description'			=> __('Enable live translations those are setting from here Required API key.', 'ctto'),
					'type'					=> 'checkbox',
					'default'				=> true
				],
				[
					'id' 					=> 'translate-toonly',
					'label'					=> __('Translate to', 'ctto'),
					'description'			=> false, // __('', 'ctto'),
					'type'					=> 'radio',
					'options'				=> [
						'user'				=> __('User Profile', 'ctto'),
						'site'				=> __('Site Language', 'ctto'),
					],
					// 'default'				=> 'site'
				],
				// [
				// 	'id' 					=> 'translate-api',
				// 	'label'					=> __('API key', 'ctto'),
				// 	'description'			=> __('Provice lecto.ai api key to translate text. Text will store after translation to avoid api limit exceed.', 'ctto'),
				// 	'type'					=> 'text',
				// 	'default'				=> ''
				//],
				/**
				 * Repeater
				 */
				[
					'id' 					=> 'repeater_translate',
					'label'					=> '',
					'description'			=> false,
					'type'					=> 'button',
					'default'				=> __('Translation list', 'ctto')
				],
			]
		];
		$args['cusrev']			= [
			'title'							=> __('Review', 'ctto'),
			'description'					=> __('Setup your custom settings for woocommerce customer review plugin.', 'ctto'),
			'fields'						=> [

				[
					'id' 					=> 'cusrev-completedorder-link',
					'label'					=> __('Completed order Link', 'ctto'),
					'description'			=> __('Mark to enable link pushing on completed order notification.', 'ctto'),
					'type'					=> 'checkbox',
					'default'				=> true
				],
				[
					'id' 					=> 'cusrev-completedorder-css',
					'label'					=> __('Completed order CSS', 'ctto'),
					'description'			=> __('"Write a Review" button inline CSS. Button is and HTML <a> element.', 'ctto'),
					'type'					=> 'text',
					'default'				=> 'font-weight:normal;background:#0085ba;border-color:#0073aa;color:#fff;text-decoration:none;padding:10px;border-radius:10px;'
				],
				[
					'id' 					=> 'cusrev-completedorder-text',
					'label'					=> __('Completed order Text', 'ctto'),
					'description'			=> __('Setup custom button text here for completed order review link.', 'ctto'),
					'type'					=> 'text',
					'default'				=> 'Write a Review'
				],
				[
					'id' 					=> 'cusrev-completedorder-template',
					'label'					=> __('Completed order HTML template', 'ctto'),
					'description'			=> sprintf(__('Give here HTML template for the section of Review Link. Use (%s) for the place of the Button. Use (%s) for the link only.', 'ctto'), '{{button}}', '{{link}}'),
					'type'					=> 'text',
					'default'				=> '{{button}}'
				],
				
			]
		];

		unset($args['email']);
		return $args;
	}
	public function get_query($args) {
		global $ctto_Plushies;
		$args = (object) wp_parse_args($args, [
			'post_type'		=> 'product',
			'type'			=> 'option',
			'limit'			=> 500,
			'queryType'		=> 'post',
			'noaccessory'	=> false
		]);
		$options = [];
		if ($args->queryType == 'post') {
			$query = get_posts([
				'numberposts'		=> $args->limit,
				'post_type'			=> $args->post_type,
				'order'				=> 'DESC',
				'orderby'			=> 'date',
				'post_status'		=> 'publish',
				
			]);
			foreach($query as $_post) {
				if ($args->noaccessory && $ctto_Plushies->is_accessory($_post->ID)) {continue;}
				$options[$_post->ID] = get_the_title($_post->ID);

				// Function to remove popup customization meta.
				// _product_custom_popup || _teddy_custom_data
				// $meta = get_post_meta($_post->ID, '_product_custom_popup', true);
				// $exists = get_post_meta($_post->ID, '_product_custom_popup_stagged', true);
				// if (! $meta && $exists) {
				// 	update_post_meta($_post->ID, '_product_custom_popup', $exists);
				// 	$updated = delete_post_meta($_post->ID, '_product_custom_popup_stagged');
				// 	if (!$updated) {echo 'post meta failed to removed';}
				// }
				
			}
		} else if ($args->queryType == 'term') {
			$query = get_categories('taxonomy=product_cat&post_type=product');
			foreach($query as $_post) {
				$options[$_post->cat_ID] = $_post->cat_name;
			}
		} else {}
		return $options;
	}
	public function optionaize_teddy_names() {
		$args = [];$filteredData = [];
		foreach((array) CTTO_OPTIONS as $key => $value) {
			if (strpos($key, 'teddy-name-') !== false) {
				$filteredData[] = $value;
			}
		}
		foreach($filteredData as $i => $name) {
			$args[] = [
				'id' 					=> 'teddy-name-' . $i,
				'label'					=> sprintf('%s%s', __('#', 'ctto'), number_format_i18n($i, 0)),
				'description'			=> false,
				'type'					=> 'text',
				'default'				=> $name
			];
		}
		return $args;
	}
	public function optionaize_teddy_badges() {
		$args = [];$filteredData = [];$filteredRow = [];
		foreach((array) CTTO_OPTIONS as $key => $value) {
			if (strpos($key, 'teddy-badge-') !== false) {
				$filteredData[$key] = $value;
			}
		}
		// try {
			foreach($filteredData as $key => $value) {
				$key = substr($key, 12);$split = explode('-', $key);
				$filteredRow[$split[1]] = isset($filteredRow[$split[1]])?$filteredRow[$split[1]]:[];
				$filteredRow[$split[1]][$split[0]] = $value;
			}
		// } catch (\Exception $th) {
		// 	echo 'Message: ' . $e->getMessage();
		// }
		foreach($filteredRow as $i => $badge) {
			$args[] = [
				'id' 					=> 'teddy-badge-enable-' . $i,
				'label'					=> sprintf('#%s %s', number_format_i18n($i, 0), __('Enable', 'ctto')),
				'description'			=> __('Mark to enable this badge everyehere', 'ctto'),
				'type'					=> 'checkbox',
				'default'				=> isset($badge['enable'])?$badge['enable']:false
			];
			$args[] = [
				'id' 					=> 'teddy-badge-label-' . $i,
				'label'					=> sprintf('#%s %s', number_format_i18n($i, 0), __('Label text', 'ctto')),
				'description'			=> __('Label name will be displayed on the badge on product card.', 'ctto'),
				'type'					=> 'text',
				'default'				=> isset($badge['label'])?$badge['label']:false
			];
			$args[] = [
				'id' 					=> 'teddy-badge-backgound-' . $i,
				'label'					=> sprintf('#%s %s', number_format_i18n($i, 0), __('Backgound Color', 'ctto')),
				'description'			=> __('Backgound color of this badge.', 'ctto'),
				'type'					=> 'color',
				'default'				=> isset($badge['backgound'])?$badge['backgound']:false
			];
			$args[] = [
				'id' 					=> 'teddy-badge-textcolor-' . $i,
				'label'					=> sprintf('#%s %s', number_format_i18n($i, 0), __('Text color', 'ctto')),
				'description'			=> __('Text color of this badge.', 'ctto'),
				'type'					=> 'color',
				'default'				=> isset($badge['textcolor'])?$badge['textcolor']:false
			];
		}
		return $args;
	}
}

/**
 * {{client_name}}, {{client_address}}, {{todays_date}}, {{retainer_amount}}
 */
