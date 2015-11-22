</div>
<?php $blank = is_page_template('template-blank.php'); ?>
<?php if (!$blank) { ?>
<?php if (isset($_GET['footer_style'])) { $footer_style = htmlspecialchars($_GET['footer_style']); } else { $footer_style = ot_get_option('footer_style'); }  ?>
<?php if( $footer_style == 'style2' ) {  ?>
<!-- Start Style2 Footer Container -->
<div id="footer_container">
<?php } ?>
	<?php if (ot_get_option('footer') != 'no') { ?>
	<!-- Start Footer -->
	<footer id="footer" class="<?php echo $footer_style; ?>">
	  	<div class="row">
	  		<?php if (ot_get_option('footer_columns') == 'fourcolumns') { ?>
		    <div class="three columns">
		    	<?php dynamic_sidebar('footer1'); ?>
		    </div>
		    <div class="three columns">
		    	<?php dynamic_sidebar('footer2'); ?>
		    </div>
		    <div class="three columns">
			    <?php dynamic_sidebar('footer3'); ?>
		    </div>
		    <div class="three columns">
			    <?php dynamic_sidebar('footer4'); ?>
		    </div>
		    <?php } elseif (ot_get_option('footer_columns') == 'threecolumns') { ?>
		    <div class="four columns">
		    	<?php dynamic_sidebar('footer1'); ?>
		    </div>
		    <div class="four columns">
		    	<?php dynamic_sidebar('footer2'); ?>
		    </div>
		    <div class="four columns">
		        <?php dynamic_sidebar('footer3'); ?>
		    </div>
		    <?php } elseif (ot_get_option('footer_columns') == 'twocolumns') { ?>
		    <div class="six columns">
		    	<?php dynamic_sidebar('footer1'); ?>
		    </div>
		    <div class="six columns">
		    	<?php dynamic_sidebar('footer2'); ?>
		    </div>
		    <?php } elseif (ot_get_option('footer_columns') == 'doubleleft') { ?>
		    <div class="six columns">
		    	<?php dynamic_sidebar('footer1'); ?>
		    </div>
		    <div class="three columns">
		    	<?php dynamic_sidebar('footer2'); ?>
		    </div>
		    <div class="three columns">
		        <?php dynamic_sidebar('footer3'); ?>
		    </div>
		    <?php } elseif (ot_get_option('footer_columns') == 'doubleright') { ?>
		    <div class="three columns">
		    	<?php dynamic_sidebar('footer1'); ?>
		    </div>
		    <div class="three columns">
		    	<?php dynamic_sidebar('footer2'); ?>
		    </div>
		    <div class="six columns">
		        <?php dynamic_sidebar('footer3'); ?>
		    </div>
		    <?php } elseif (ot_get_option('footer_columns') == 'fivecolumns') { ?>
		    <div class="two columns">
		    	<?php dynamic_sidebar('footer1'); ?>
		    </div>
		    <div class="three columns">
		    	<?php dynamic_sidebar('footer2'); ?>
		    </div>
		    <div class="two columns">
		    	<?php dynamic_sidebar('footer3'); ?>
		    </div>
		    <div class="three columns">
		    	<?php dynamic_sidebar('footer4'); ?>
		    </div>
		    <div class="two columns">
		    	<?php dynamic_sidebar('footer5'); ?>
		    </div>
		    <?php }?>
	    </div>
	</footer>
	<!-- End Footer -->
	<?php } ?>
	<?php if (ot_get_option('subfooter') != 'no') { ?>
	<!-- Start Sub-Footer -->
	<section id="subfooter">
		<div class="row">
			<div class="four columns">
				<p><?php echo ot_get_option('copyright','COPYRIGHT 2014 FUEL THEMES. All RIGHTS RESERVED.'); ?> </p>
			</div>
			<div class="eight columns paymenttypes-container">
				<?php if (ot_get_option('payment_visa') != 'off') { ?>
					<figure class="paymenttypes visa"></figure>
				<?php } ?>
				<?php if (ot_get_option('payment_mc') != 'off') { ?>
					<figure class="paymenttypes mc"></figure>
				<?php } ?>
				<?php if (ot_get_option('payment_pp') != 'off') { ?>
					<figure class="paymenttypes paypal"></figure>
				<?php } ?>
				<?php if (ot_get_option('payment_discover') != 'off') { ?>
					<figure class="paymenttypes discover"></figure>
				<?php } ?>
				<?php if (ot_get_option('payment_amazon') != 'off') { ?>
					<figure class="paymenttypes amazon"></figure>
				<?php } ?>
				<?php if (ot_get_option('payment_stripe') != 'off') { ?>
					<figure class="paymenttypes stripe"></figure>
				<?php } ?>
				<?php if (ot_get_option('payment_amex') != 'off') { ?>
					<figure class="paymenttypes amex"></figure>
				<?php } ?>
			</div>
		</div>
	</section>
	<!-- End Sub-Footer -->
	<?php } ?>
<?php if( $footer_style == 'style2' ) {  ?>
<!-- End #footer_container-->
</div>
<?php } ?>

<?php } // Blank page check?>
</div> <!-- End #wrapper -->

<aside id="searchpopup" class="mfp-hide">
	<div class="row">
		<div class="twelve columns">
			<?php get_search_form(); ?>
		</div>
	</div>
</aside>
<?php echo ot_get_option('ga'); ?>
<?php 
	/* Always have wp_footer() just before the closing </body>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to reference JavaScript files.
	 */
	 wp_footer(); 
?>
</body>
</html>