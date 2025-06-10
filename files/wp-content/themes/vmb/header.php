<?php
    $thisToolbox = new Toolbox(get_the_ID());

	$secure = get_field('secure', get_the_ID());
		if ($secure === true && !$thisToolbox->Validated()) {
			nocache_headers();
			wp_safe_redirect(esc_url(admin_url('admin-post.php')).'?action=SignOut');
			exit;
		}

    $thisBrand = new Brand();
    // $telephone_number = $thisBrand->TelephoneNumber();
    $email_address = $thisBrand->EmailAddress();
    $colours = $thisBrand->Colours();
	$featured_image = $thisToolbox->FeaturedImage('full');
?>
<!doctype html>
<html class="no-js" <?php language_attributes(); ?>>
    <head>
        <title><?php wp_title(''); ?></title>
		<meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=2,user-scalable=yes,viewport-fit=cover">

        <?php echo $thisBrand->Favicons(); ?>

        <?php if (!empty($colours)) { ?>
	        <style type="text/css">
	        	:root {
	        		--primary: <?php echo $colours['primary']; ?>;

	        		--primary_dark: <?php echo $thisToolbox->Darken($colours['primary'], 10); ?>;
	        		--primary_darker: <?php echo $thisToolbox->Darken($colours['primary'], 25); ?>;
	        		--primary_darkest: <?php echo $thisToolbox->Darken($colours['primary'], 50); ?>;

	        		--primary_light: <?php echo $thisToolbox->Lighten($colours['primary'], 10); ?>;
	        		--primary_lighter: <?php echo $thisToolbox->Lighten($colours['primary'], 25); ?>;
	        		--primary_lightest: <?php echo $thisToolbox->Lighten($colours['primary'], 50); ?>;

	        		--secondary: <?php echo $colours['secondary']; ?>;

	        		--secondary_dark: <?php echo $thisToolbox->Darken($colours['secondary'], 10); ?>;
	        		--secondary_darker: <?php echo $thisToolbox->Darken($colours['secondary'], 25); ?>;
	        		--secondary_darkest: <?php echo $thisToolbox->Darken($colours['secondary'], 50); ?>;

	        		--secondary_light: <?php echo $thisToolbox->Lighten($colours['secondary'], 10); ?>;
	        		--secondary_lighter: <?php echo $thisToolbox->Lighten($colours['secondary'], 25); ?>;
	        		--secondary_lightest: <?php echo $thisToolbox->Lighten($colours['secondary'], 50); ?>;

	        		--tertiary: <?php echo $colours['tertiary']; ?>;

	        		--tertiary_dark: <?php echo $thisToolbox->Darken($colours['tertiary'], 10); ?>;
	        		--tertiary_darker: <?php echo $thisToolbox->Darken($colours['tertiary'], 25); ?>;
	        		--tertiary_darkest: <?php echo $thisToolbox->Darken($colours['tertiary'], 50); ?>;

	        		--tertiary_light: <?php echo $thisToolbox->Lighten($colours['tertiary'], 10); ?>;
	        		--tertiary_lighter: <?php echo $thisToolbox->Lighten($colours['tertiary'], 25); ?>;
	        		--tertiary_lightest: <?php echo $thisToolbox->Lighten($colours['tertiary'], 50); ?>;

	        		--text: <?php echo $colours['text']; ?>;
	        		--text_dark: <?php echo $thisToolbox->Darken($colours['text'], 10); ?>;
	        		--text_light: <?php echo $thisToolbox->Lighten($colours['text'], 10); ?>;

	        		--font-family: 'Source Sans Pro';
	        	}
	        </style>
	    <?php } ?>

        <?php wp_head();  ?>
	</head>
	<body <?php body_class(); ?>>

        <header>
			<!-- <div id="top-bar">
				<div class="inner flex flex-align-center flex-justify-between">
					<div></div>

					<?php #'' wp_nav_menu(['menu' => 'Top Navigation', 'container' => 'nav', 'container_class' => '', 'menu_class' => 'd-flex align-items-center justify-content-end']); ?>
				</div>
			</div> -->

			<div id="btm-bar">
				<div class="header-wrap d-flex align-items-stretch justify-content-between inner">
			        <div id="logo">
			            <!-- <a href="https://www.mycruises.com.au" title="<?php bloginfo('name'); ?>"> -->
			                 <img src="<?php echo $thisBrand->Logo('main_logo', 'large'); ?>" alt="<?php bloginfo('name'); ?>">
			             <!-- </a> -->
			        </div>
			    </div>
			</div>
        </header>

		<?php /*
        <div class="mega-menu">
        	<div class="menu-setup">
	        	<div class="menu-container">
	        		<div class="menu-row">
			        	<?php if (have_rows('main_columns','options')): while(have_rows('main_columns','options')) : the_row(); ?>
			        		<div class="main-column">
			        		<?php $columnTitle = get_sub_field('column_title');?>

			        			<?php if (have_rows('sub_columns','options')): ?>
			        				<div class="sub-column">
			        					<div class="column-title-container">
			        					<?php if ($columnTitle){?>
			        						<a class="column-title" href="<?php echo $columnTitle['url'];?>" target="<?php echo $columnTitle['target'];?>"><?php echo $columnTitle['title'];?></a>
			        					<?php } ?>
			        					</div>
			        					<ul>
			        						<?php
			        			 			while(have_rows('sub_columns','options')) : the_row();
			        			 				$pageLink = get_sub_field('page_link');
			        			 			 ?>

			        			 				<li>
			        			 					<a href="<?php echo $pageLink['url'];?>" target="<?php echo $pageLink['target'];?>"><?php echo $pageLink['title'];?></a>
			        			 				</li>
			        						<?php endwhile; ?>
			        					</ul>
			        				</div> <!-- sub-column -->
			        			<?php endif;?>

			        			</div> <!-- main-column -->
			        		<?php endwhile;
			        		endif;
			        		?>
			    	</div><!-- menu-row -->
			    	<?php if (have_rows('featured_links','options')): ?>
				    	<div class="menu-row">
				    		<div class="featured-links">
				    		<?php while(have_rows('featured_links','options')): the_row();?>
				    			<div class="featured-link">
				    				<?php $featuredLink = get_sub_field('link');
				    					$linkImage = get_sub_field('image');?>
				    					<a href="<?php echo $featuredLink['url'];?>" target="<?php echo $featuredLink['target'];?>">
				    						<img src="<?php echo $linkImage['url'];?>" />
				    						<span class="img-link-text"><?php echo $featuredLink['title'];?></span>
				    					</a>
				    			</div>
				    		<?php endwhile;?>
				    		</div>
				    	</div>
			    <?php endif;?>
	    		</div>
	    	</div>
        </div>
		*/ ?>

		<div id="featured-image" class="d-flex align-items-center" style="background-image:url(<?php echo $featured_image['url']; ?>); ?>">
			<h1 class="inner">
				<?php the_title(); ?>
			</h1>
		</div>

		<?php if ($thisToolbox->Validated()) { ?>
			<div id="user-menu">
				<div class="inner">
					<?php wp_nav_menu(['menu' => 'User Navigation', 'container' => 'nav', 'container_class' => '', 'menu_class' => 'd-flex align-items-center justify-content-end  flex-column flex-lg-row']); ?>
				</div>
			</div>
		<?php } ?>