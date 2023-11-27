<?php

/**
 * The template for displaying archive pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package MXDFR_base
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <div class="top-wrapper"></div>
        <div class="under-triangle">
            <?php if (have_posts()) : ?>
                <h1 class="page-header in-grid">Videopodcast</h1>
                <div class="entry-content in-grid mb-big custom-link">
                    <?php
                    $intro_image = get_field('intro_image', 'option');
                    if ($intro_image) : ?>
                        <div class="entry-image" style="background-image: url('<?php echo esc_url($intro_image); ?>')"></div>
                    <?php endif; ?>

                    <?php
                    $intro_text = get_field('intro_text', 'option');
                    if ($intro_text) : ?>
                        <div class="blog-text">
                            <?php echo $intro_text;
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="videopodcast-grid in-grid">
                    <?php while (have_posts()) : the_post();
                        $video_url = get_field('yt_podcast_link', get_the_ID());
                    ?>

                        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                            <div class="video-wrapper">
                                <?php if ($video_url) : ?>
                                    <?php echo $video_url; ?>
                                <?php endif; ?>
                            </div>
                            <div class="blog-text content-wrapper">
                                <h3 class="post-title"><?php the_title(); ?></h3>
                                <div class="post-description">
                                    <?php the_excerpt(); ?>
                                </div>
                            </div>

                        </article>

                <?php endwhile;
                    the_posts_navigation();
                else :
                    get_template_part('template-parts/content', 'none');
                endif;

                wp_reset_postdata();
                ?>
                </div>
        </div>
    </main><!-- #main -->
</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
