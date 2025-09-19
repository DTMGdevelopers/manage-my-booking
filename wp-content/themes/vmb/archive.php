<?php get_header(); ?>
	<div id="content">
		<article>
			<h1>
				<?php if (is_day()): ?>
					<?php printf( __('Daily Archive: %s','vmb'),get_the_date()); ?>
				<?php elseif (is_month()): ?>
					<?php printf( __('Monthly Archive: %s','vmb'),get_the_date( _x('F Y','monthly archives date format','vmb'))); ?>
				<?php elseif (is_year()): ?>
					<?php printf( __('Yearly Archive: %s','vmb'),get_the_date( _x('Y','yearly archives date format','vmb'))); ?>
				<?php else : ?>
					<?php _e('Blog Archive','vmb'); ?>
				<?php endif; ?>
			</h1>
			<?php get_template_part('loop','archive'); ?>
		</article>
        <aside><?php get_sidebar(); ?></aside>
    </div>
<?php get_footer(); ?>