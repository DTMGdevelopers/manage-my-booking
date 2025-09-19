<?php get_header(); ?>

	<div class="container">
		<article>

			<?php $thisToolbox = new Toolbox(); ?>
			<?php /*if ($thisToolbox->Validated()) { ?>
				<p>Booking ID: <?php echo $thisToolbox->GetBookingID(); ?></p>
			<?php }*/ ?>

			<?php the_content(); ?>

		</article>
	</div>
<?php get_footer(); ?>