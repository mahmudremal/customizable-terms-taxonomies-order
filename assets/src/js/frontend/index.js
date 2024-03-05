/**
 * Frontend Script.
 * 
 * @package TermsTaxonomyOrder
 */
import Swal from "sweetalert2";
// import Sortable from 'sortablejs';
import Sortable from 'sortablejs/modular/sortable.core.esm.js';


(function ($) {
	class FutureWordPress_Frontend {
		constructor() {
			this.config = fwpSiteConfig;
			var i18n = fwpSiteConfig?.i18n??{};
			this.ajaxUrl = fwpSiteConfig?.ajaxUrl??'';
			this.ajaxNonce = fwpSiteConfig?.ajax_nonce??'';
			this.i18n = {confirming: 'Confirming', ...i18n};
			this.setup_hooks();
		}
		setup_hooks() {
			window.thisClass = this;
			this.order_terms();
		}
		order_terms() {
			if (window?.terms_orders) {
				document.querySelectorAll('.elementor-portfolio__filters').forEach(filters => {
					filters.querySelectorAll('.elementor-portfolio__filter:not([data-filter="__all"])').forEach(item => {
						var term = window.terms_orders.find(row => row.term_id == parseInt(item.dataset.filter));
						if (term) {
							// term.element = item;
							item.style.order = term.order_by
						}
					});
					filters.classList.add('elementor-portfolio__sorted');
				});
			}
		}

	}
	new FutureWordPress_Frontend();
})(jQuery);
