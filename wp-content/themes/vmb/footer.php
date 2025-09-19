<?php
    $thisBrand = new Brand();
    $newsletter = get_field('newsletter', 'option');
    $footer_logos = get_field('footer_logos', 'option');
    $footer_brands = get_field('footer_brands', 'option');
    $social_media = $thisBrand->SocialMedia();
?>



        <footer>
            <div class="footer-top">
                <div class="flex">
                    <div class="newsletter">
                        <div class="wrapper">
                <?php //if (!empty($newsletter['content'])) { ?>

                        <!-- <div class="content"><?php echo wptexturize(do_shortcode($newsletter['content'])); ?></div>
                        <div class="form"><?php echo wptexturize(do_shortcode($newsletter['form'])); ?></div> -->
                <?php //} ?>
                    </div>
                </div>
                    <div class="footer-nav">
                    <?php //wp_nav_menu(['menu' => 'Footer Navigation', 'container' => 'nav', 'container_class' => '', 'menu_class' => 'd-flex justify-content-between flex-wrap']); ?>
                    </div>
                </div>
            </div>

        	<div class="footer-bottom">
                <div class="flex">
                    <div class="footer-info">
                        <!-- <a href="https://www.mycruises.com.au" title="<?php bloginfo('name'); ?>"> -->
                             <img src="<?php echo $thisBrand->Logo('main_logo', 'large'); ?>" alt="<?php bloginfo('name'); ?>">
                         <!-- </a> -->
                         <div class="copyright">
                             <p class="copyright-text">&copy; <?php echo date("Y"); ?> A subsidiary of Ignite Travel Group. All Rights Reserved.</p>
                             <!-- <div class="terms">
                                <a href="https://www.mycruises.com.au/privacy-policy/">Privacy Policy</a>
                                <a href="https://www.mycruises.com.au/terms-conditions/">Terms and Conditions</a>
                                <div class="footer-info-item">
                                   <p data-toggle="tooltip" data-placement="top" title="Ignite Holidays Pty Ltd</br>
IATA 023 6354 6</br>
ABN 86 119 314 377</br>
A subsidiary of Ignite Travel Group" data-html="true"  >

                                            ABN
                                        </p>
                             </div> -->
                         </div>
                    </div>
                </div>
                    <div class="footer-socials">
                        <!-- <a href="https://www.facebook.com/mycruises.com.au/"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/mycruises.com.au/"><i class="fa-brands fa-instagram"></i></a>
                        <a href="https://www.youtube.com/channel/UCQqXdZGOwfB0ju-EviP39ag"><i class="fa-brands fa-youtube"></i></a> -->
                    </div>
                </div>
            </div>
        </footer>



        <div id="alert-container" class="d-flex align-items-center justify-content-center">
            <div id="alert-content">
                <h3></h3>

                <div id="alert-inner">
                    <div class="row align-items-center">
                        <div class="col-2 d-flex align-items-start justify-content-center">
                            <p class="mb-0 icon d-flex align-items-center justify-content-center"></p>
                        </div>
                        <div class="col-10">
                            <p class="m-0 content"></p>
                        </div>
                    </div>
                </div>

                <p class="buttons text-center">
                    <button type="button" class="cancel btn btn-secondary"></button>
                    &nbsp;
                    <button type="button" class="continue btn btn-primary"></button>
                    <a href="" class="continue btn btn-primary"></a>
                </p>
            </div>
        </div>
        <div id="alert-background"></div>

        <div id="mobile-navigation"><?php wp_nav_menu(array('menu'=>'Main Navigation','container'=>'nav','container_class'=>'','menu_class'=>'')); ?></div>
        <div id="mobile-background"></div>

        <?php wp_footer(); ?>
	</body>
</html>