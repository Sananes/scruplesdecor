<?php
/**
 *	Oxygen WordPress Theme
 *	
 *	Laborator.co
 *	www.laborator.co 
 */

$contact_page_blocks = get_field('contact_page_blocks');

get_template_part('tpls/contact-map');

?>
<div class="contact-blocks-env">
<?php

switch($contact_page_blocks)
{
	case "address_contact":
		get_template_part('tpls/contact-address');
		get_template_part('tpls/contact-form');
		break;
		
	case "address":
		get_template_part('tpls/contact-address');
		break;
		
	case "contact":
		get_template_part('tpls/contact-form');
		break;
	
	default:
		get_template_part('tpls/contact-form');
		get_template_part('tpls/contact-address');
}
?>
</div>