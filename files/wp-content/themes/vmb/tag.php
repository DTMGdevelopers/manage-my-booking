<?php get_header(); ?>
	<div id="content">
		<article>
			<h1><?php printf( __('Tag Archive: %s','vmb'),single_tag_title('',false)); ?></h1>
			<?php get_template_part('loop','tag'); ?>
		</article>
        <aside>
        	<?php get_sidebar(); ?>
        </aside>
    </div>
<?php get_footer(); ?>