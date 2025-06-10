<?php get_header(); ?>
	<div id="content">
		<article>
			<h1><?php printf( __('Category Archives: %s','vmb'),single_cat_title('',false)); ?></h1>
			<?php get_template_part('loop','category'); ?>
		</article>
        <aside><?php get_sidebar(); ?></aside>
    </div>
<?php get_footer(); ?>