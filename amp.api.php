<?php

/**
 * @file
 * Documents hooks provided by this module.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Allow other modules to change the status of the AMP page. This allow you to
 * turn AMP on/off for any node or page.
 *
 * @param bool $result
 *   TRUE if its an AMP page, otherwise FALSE
 */
function hook_is_amp_request(&$result) {

}

/**
 * Allow other modules to change the metadata before it's outputted to the page.
 *
 * @param $metadata_json
 *    The json array with key/values
 * @param $node
 *    The node object
 * @param $type
 *    The type
 */
function hook_amp_metadata(&$metadata_json, $node, $type) {

}
