<?php get_header(); ?>
	<div id="content">
		<article>
			<h1><?php printf( __('Author Archive: %s','vmb'),get_the_author()); ?></h1>
			<?php get_template_part('loop','author'); ?>
		</article>
        <aside><?php get_sidebar(); ?></aside>
    </div>
<?php get_footer(); ?>