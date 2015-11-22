<?php
namespace Aelia\WC;
if(!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * An extended SimpleXMLElement class, which includes some convenience methods
 * for easier manipulation of XML.
 */
class AeliaSimpleXMLElement extends \SimpleXMLElement {
	/**
	 * Appends a SimpleXMLElement XML to a SimpleXMLElement.
	 *
	 * @param SimpleXMLElement $append The XML content to append.
	 */
	public function append_element(\SimpleXMLElement $append) {
		$destination_node = dom_import_simplexml($this);
		$child = dom_import_simplexml($append);
		$destination_node->appendChild($destination_node->ownerDocument->importNode($child, true));
	}
}
