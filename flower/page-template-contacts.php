<?php
/* Template Name: Contacts */

get_header(); ?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main contact-page-aflowers" role="main">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <?php
                        while (have_posts()) :
                            the_post();

                            do_action('storefront_page_before');

                            get_template_part('content', 'page');

                        endwhile; // End of the loop.
                        ?>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row mb-5">
                    <div class="col-xl-6">
                        <ul>
                            <li class="li-header"><?php the_field('header_before_shops'); ?></li>
                            <?php if (have_rows('content_before_shops_first')): ?>
                                <?php while (have_rows('content_before_shops_first')) : the_row(); ?>
                                    <?php $aflowers_header_first = get_sub_field('content_before_shops_first_block');
                                    if (!empty($aflowers_header_first)): ?>
                                        <li><?php echo $aflowers_header_first; ?></li>
                                    <?php endif; ?>
                                <?php endwhile; ?>
                            <?php else : ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="col-xl-6">
                        <ul>
                            <?php if (have_rows('content_before_shops_second')): ?>
                                <?php while (have_rows('content_before_shops_second')) : the_row(); ?>
                                    <?php $aflowers_header_second = get_sub_field('content_before_shops_second_block');
                                    if (!empty($aflowers_header_second)): ?>
                                        <li><?php echo $aflowers_header_second; ?></li>
                                    <?php endif; ?>
                                <?php endwhile; ?>
                            <?php else : ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="container">
                <?php if (have_rows('shops_aflowers')): ?>
                    <?php while (have_rows('shops_aflowers')) : the_row(); ?>
                        <div class="row mb-5">
                        <div class="col-xl-6 col-12">
                            <ul>
                                <?php $aflowers_header = get_sub_field('shops_aflowers_header');
                                if (!empty($aflowers_header)): ?>
                                    <li class="li-header"><?php echo $aflowers_header; ?></li>
                                <?php endif; ?>
                                <?php if (have_rows('shops_header_info')): ?>
                                    <?php while (have_rows('shops_header_info')) : the_row(); ?>
                                        <?php $aflowers_header_info = get_sub_field('shop_header_infobox');
                                        if (!empty($aflowers_header_info)): ?>
                                            <li><?php echo $aflowers_header_info; ?></li>
                                        <?php endif; ?>
                                    <?php endwhile; ?>
                                <?php else : ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div class="col-xl-6 col-12">
                        <?php $aflowers_map = get_sub_field('google_map_aflowers');
                        if (!empty($aflowers_map)): ?>
                            <div><?php echo $aflowers_map; ?></div>
                            </div>
                            </div><!-- row -->
                        <?php endif; ?>
                    <?php endwhile; ?>
                <?php else : ?>
                <?php endif; ?>
            </div><!-- container -->
            <div class="container">
                <div class="row">
                    <div class="col">
                        <?php the_field('content_after_maps'); ?>
                    </div>
                </div>
            </div>

            <?php
            /**
             * Functions hooked in to storefront_page_after action
             *
             * @hooked storefront_display_comments - 10
             */
            do_action('storefront_page_after');
            ?>
        </main><!-- #main -->
    </div><!-- #primary -->

<?php
do_action('storefront_sidebar');
get_footer();
