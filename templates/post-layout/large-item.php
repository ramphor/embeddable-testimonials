<div <?php post_class(array('loop-item', 'post-large-image')); ?>>
    <?php do_action('jankx_testimonial_layout_before_loop_item', $post, $data_index); ?>
    <div class="post-thumbnail">
        <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
            <?php jankx_the_post_thumbnail('medium_large'); ?>
        </a>
    </div>
    <div class="post-infos">
        <h3 class="post-title">
            <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
        </h3>

        <div class="description"><?php the_excerpt(); ?></div>
    </div>
    <?php do_action('jankx_testimonial_layout_after_loop_item', $post, $data_index); ?>
</div>
