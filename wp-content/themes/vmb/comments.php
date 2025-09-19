<div id="comments">
	<?php if (have_comments()): ?>
		<h3 class="comments-title"><?php printf( _n('One Response to %2$s','%1$s Responses to %2$s',get_comments_number(),'vmb'),number_format_i18n(get_comments_number()),'<em>'.get_the_title().'</em>'); ?></h3>
		<ol class="commentlist">
			<?php wp_list_comments(array('callback'=>'vmb_comment')); ?>
		</ol>
		<?php if (get_comment_pages_count()>1&&get_option('page_comments')): ?>
			<div class="navigation">
				<div class="nav-previous"><?php previous_comments_link( __('<span class="meta-nav">&larr;</span> Older Comments','vmb' )); ?></div>
				<div class="nav-next"><?php next_comments_link( __('Newer Comments <span class="meta-nav">&rarr;</span>','vmb')); ?></div>
			</div>
		<?php endif; ?>
	<?php else: ?>
		<?php if (!comments_open()): ?>
			<p class="nocomments"><?php _e('Comments are closed.','vmb'); ?></p>
		<?php else: ?>
			<p class="nocomments"><?php _e('There are no comments.','vmb'); ?></p>
		<?php endif; ?>
	<?php endif; ?>
	<?php comment_form(); ?>
</div>