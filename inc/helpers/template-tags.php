<?php
/**
 * Custom template tags for the theme.
 *
 * @package TermsTaxonomyOrder
 */
if (! function_exists('is_FwpActive')) {
  function is_FwpActive($opt) {
    if (! defined('CTTO_OPTIONS')) {return false;}
    return (isset(CTTO_OPTIONS[$opt]) && CTTO_OPTIONS[$opt] == 'on');
  }
}
if (! function_exists('get_FwpOption')) {
  function get_FwpOption($opt, $def = false) {
    if (! defined('CTTO_OPTIONS')) {return false;}
    return isset(CTTO_OPTIONS[$opt]) ? CTTO_OPTIONS[$opt] : $def;
  }
}