<?php

namespace Yoast\WP\SEO\Helpers;

use WPSEO_Meta;
use WPSEO_Taxonomy_Meta;

/**
 * A helper object for meta.
 */
class Meta_Helper {

	/**
	 * Get a custom post meta value.
	 *
	 * Returns the default value if the meta value has not been set.
	 *
	 * {@internal Unfortunately there isn't a filter available to hook into before returning
	 *            the results for get_post_meta(), get_post_custom() and the likes. That
	 *            would have been the preferred solution.}}
	 *
	 * @param string $key    Internal key of the value to get (without prefix).
	 * @param int    $postid Post ID of the post to get the value for.
	 *
	 * @codeCoverageIgnore We have to write test when this method contains own code.
	 *
	 * @return string All 'normal' values returned from get_post_meta() are strings.
	 *                Objects and arrays are possible, but not used by this plugin
	 *                and therefore discarted (except when the special 'serialized' field def
	 *                value is set to true - only used by add-on plugins for now).
	 *                Will return the default value if no value was found.
	 *                Will return empty string if no default was found (not one of our keys) or
	 *                if the post does not exist.
	 */
	public function get_value( $key, $postid = 0 ) {
		return WPSEO_Meta::get_value( $key, $postid );
	}

	/**
	 * Retrieve a taxonomy term's meta value(s).
	 *
	 * @param mixed  $term     Term to get the meta value for
	 *                         either (string) term name, (int) term id or (object) term.
	 * @param string $taxonomy Name of the taxonomy to which the term is attached.
	 * @param string $meta     Optional. Meta value to get (without prefix).
	 *
	 * @return mixed|bool Value for the $meta if one is given, might be the default.
	 *                    If no meta is given, an array of all the meta data for the term.
	 *                    False if the term does not exist or the $meta provided is invalid.
	 */
	public function get_term_value( $term, $taxonomy, $meta = null ) {
		return WPSEO_Taxonomy_Meta::get_term_meta( $term, $taxonomy, $meta );
	}
}
