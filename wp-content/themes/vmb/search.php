<?php get_header(); ?>
	<div id="content">
		<article>
			<?php if (have_posts()): ?>
				<h1 class="entry-title"><?php printf( __('Search Results for: %s','vmb'),get_search_query()); ?></h1>
				<?php get_template_part('loop','search'); ?>
			<?php else: ?>
				<h1 class="entry-title"><?php _e( 'Nothing Found', 'vmb' ); ?></h1>
				<p><?php _e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'vmb' ); ?></p>
			<?php endif; ?>
		</article>
		<aside><?php get_sidebar(); ?></aside>
	</div>
<?php get_footer(); ?>