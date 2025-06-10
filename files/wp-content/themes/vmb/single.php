<?php get_header(); ?>
	<div id="content">
		<article>
			<?php if (have_posts())while(have_posts()):the_post(); ?>
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h1 class="entry-title"><?php the_title(); ?></h1>
					<div class="entry-meta"><?php vmb_posted_on(); ?></div>
					<div class="entry-content">
						<?php the_content(); ?>
						<?php wp_link_pages(array('before'=>'<div class="page-link">'.__('Pages:','vmb'),'after'=>'</div>')); ?>
					</div>
					<div class="entry-utility"><?php vmb_posted_in(); ?></div>
				</div>
				<div id="nav-below" class="navigation">
					<div class="nav-previous"><?php previous_post_link('%link','<span class="meta-nav">'._x('&larr;','Previous post link','vmb').'</span> %title'); ?></div>
					<div class="nav-next"><?php next_post_link('%link','%title <span class="meta-nav">'._x('&rarr;','Next post link','vmb').'</span>'); ?></div>
				</div>
				<?php comments_template('',true); ?>
			<?php endwhile; ?>
		</article>
		<aside><?php get_sidebar(); ?></aside>
	</div>
<?php get_footer(); ?>