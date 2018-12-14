<?php
/**
 * List block part for displaying page content in page.php
 *
 * @package CoverNews
 */

?>
<header class="entry-header">

    <div class="header-details-wrapper">
        <div class="entry-header-details">
            <?php if ('post' === get_post_type()) : ?>
                <div class="figure-categories figure-categories-bg">
                    <?php echo covernews_post_format(get_the_ID()); ?>
                    <?php covernews_post_categories(); ?>
                </div>
            <?php endif; ?>
            <?php the_title('<h1 class="entry-title">', '</h1>'); ?>

            <?php if ('post' === get_post_type()) : ?>

                <?php covernews_post_item_meta(); ?>
                <?php if (has_excerpt()): ?>
                    <div class="post-excerpt">
                        <?php the_excerpt(); ?>
                    </div>
                <?php endif; ?>


            <?php endif; ?>
        </div>
    </div>
    <?php covernews_post_thumbnail(); ?>
</header><!-- .entry-header -->