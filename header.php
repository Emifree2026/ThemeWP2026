<?php
/**
 * Minimal <head> shell for Piece 1. The full Header (logo, nav, mobile menu,
 * sticky-on-scroll) lands in Piece 2.
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>