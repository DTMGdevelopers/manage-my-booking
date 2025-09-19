<?php get_header(); ?>
	<div id="content">
		<article>
			<?php get_template_part('loop','index'); ?>
		</article>
        <aside><?php get_sidebar(); ?></aside>
    </div>
<?php get_footer(); ?>