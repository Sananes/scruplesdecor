<div class="sgwrap">
	<div class="box">
		<h2>SuperCacher for WordPress by SiteGround</h2>		
		<p>The SuperCacher is a system that allows you to use the SitGround dynamic cache and Memcached to optimize the performance of your WordPress. In order to take advantage of the system you should have the SuperCacher enabled at your web host plus the required cache options turned on below. For more information on the different caching options refer to the <a href="http://www.siteground.com/tutorials/supercacher/" target="_blank">SuperCacher Tutorial</a>! </p>
	</div>

	<div class="box clear">
		<h2>Dynamic Cache Settings</h2>
	
		<div class="three clear">
			<div class="greybox">
				<h3>Dynamic Cache</h3>
				<a href="" id="sg-cachepress-dynamic-cache-toggle" class="<?php  if ( $this->options_handler->get_option('enable_cache') ==1 ) echo 'toggleon'; else echo 'toggleoff'; ?>"></a>
				<p id="sg-cachepress-dynamic-cache-text">Enable the Dynamic caching system</p>
				<p id="sg-cachepress-dynamic-cache-error" class="error"></p>
			</div>
		
			<div class="greybox">
				<h3>AutoFlush Cache</h3>
				<a href="" id="sg-cachepress-autoflush-cache-toggle" class="<?php  if ( $this->options_handler->get_option('autoflush_cache') ==1 ) echo 'toggleon'; else echo 'toggleoff'; ?>"></a>
				<p id="nginxcacheoptimizer-autoflush-cache-text">Automatically flush the Dynamic cache when you edit your content.</p>
				<p id="nginxcacheoptimizer-autoflush-cache-error" class="error"></p>
			</div>
		
			<div class="greybox">
				<h3>Purge Cache</h3>
				<form class="purgebtn" method="post" action="<?php menu_page_url( 'sg-cachepress-purge' ); ?>">
					<?php submit_button( __( 'Purge the Cache', 'sg-cachepress' ), 'primary', 'sg-cachepress-purge', false );?>
				</form>
				<p>Purge all the data cached by the Dynamic cache.</p>
			</div>
			
		</div>
		<div class="greybox">
			<h3>Exclude URLs From Dynamic Caching</h3>
			<p>Provide a list of your website's URLs you would like to exclude from the cache. For example if you'd like to exclude: <strong>http://domain.com/path/to/url</strong><br>
			You can provide only a part of the URL. If you input the "path" string part of the URL, then each URL that consists of it will be excluded. Divide each URL by a new line.</p>
			
			<form method="post" action="<?php menu_page_url( 'sg-cachepress-purge' ); ?>">
				<textarea id="sg-cachepress-blacklist-textarea"><?php  echo esc_textarea($this->options_handler->get_blacklist()); ?></textarea>
				<?php submit_button( __( 'Update the Exclude List', 'sg-cachepress' ), 'primary', 'sg-cachepress-blacklist', false );?>
			</form>
		</div>
	</div>                                     
	<div class="box">
		<h2>Memcached Settings</h2>
		<div class="greybox">
				
			<a href="" id="sg-cachepress-memcached-toggle" class="<?php  if ( $this->options_handler->get_option('enable_memcached') ==1 ) echo 'toggleon'; else echo 'toggleoff'; ?>"></a>
			
			<p id="sg-cachepress-memcached-text">Enable Memcached</p>
			<p class="error" id="sg-cachepress-memcached-error"></p>
				
			<div class="clr"></div>
			<p>Store in the server's memory frequently executed queries to the database for a faster access on a later use.</p>
			<div class="clr"></div>		
		</div>
	</div>
	
	<div class="box clear">
		<h2>Dynamic Cache Status</h2>
		<div class="greybox">
			
			<form class="purgebtn" method="post" action="<?php menu_page_url( 'sg-cachepress-test' ); ?>" id="cachetest">
				<?php echo get_site_url()?>/&nbsp;<input id="testurl" type="" name="" value="" />
				<?php submit_button( __( 'Test URL', 'sg-cachepress' ), 'primary', 'sg-cachepress-test', false );?>
			</form>
			
			<div class="status_test" style="display:none;">Status: <span id="status_test_value"></span></div>
				
			<div class="clr"></div>
			<p>Check if this URL is dynamic or cached. Leave empty for your index or <strong>/example/</strong> for another page.</p>
			<div class="clr"></div>		
		</div>
	</div>
</div>