<aside class="post-meta cf">
	<ul>
		<?php if(!is_page_template('template-blog-grid-style.php')) { ?>
		<li><i class="fa fa-user"></i> <?php the_author_posts_link(); ?></li>
		<?php } ?>
		<li><i class="fa fa-calendar"></i> <?php the_time(get_option('date_format')); ?></li>
		<li><?php comments_popup_link('<i class="fa fa-comment"></i> 0 Comments', '<i class="fa fa-comment"></i> 1 Comment', '<i class="fa fa-comment"></i> % Comments', 'postcommentcount', '<i class="fa fa-comment"></i> Comments Disabled'); ?></li>
		<?php if(!is_page_template('template-blog-grid-style.php') && has_category()) { ?>
		<li><i class="fa fa-folder-open"></i> <?php the_category(', '); ?></li>
		<?php } ?>
	</ul>
</aside>