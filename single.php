<?php
/**
 * Single Post Template
 * @package yigeren
 */
get_header();
while ( have_posts() ) : the_post();
  $cats = get_the_category();
  $cat_slug = ! empty( $cats ) ? $cats[0]->slug : '';
  $cat_label = $cat_slug ? yigeren_category_label( $cat_slug ) : '';
  $tags = get_the_tags();
?>

<!-- Article Header -->
<header class="article-header reveal" data-component="article-header">
  <?php if ( $cat_label ) : ?>
  <span class="article-header__cat"><?php echo esc_html( $cat_label ); ?></span>
  <?php endif; ?>
  <h1><?php the_title(); ?></h1>
  <div class="article-header__meta">
    <?php echo esc_html( yigeren_full_date() ); ?>
    <?php
    $distance = get_post_meta( get_the_ID(), '_yigeren_distance', true );
    if ( $distance ) echo ' · ' . esc_html( $distance );
    $location = get_post_meta( get_the_ID(), '_yigeren_location', true );
    if ( $location ) echo ' · ' . esc_html( $location );
    ?>
  </div>
</header>

<!-- Article Body -->
<article class="article-body" data-component="article-body">
  <?php if ( has_post_thumbnail() ) : ?>
  <figure style="max-width:880px;margin:0 auto 2em;">
    <?php the_post_thumbnail( 'article-cover', array( 'style' => 'border-radius:var(--seed-radius);width:100%;' ) ); ?>
  </figure>
  <?php endif; ?>

  <?php the_content(); ?>
</article>

<!-- Tags -->
<?php if ( $tags ) : ?>
<div class="article-tags">
  <?php foreach ( $tags as $tag ) : ?>
  <a href="<?php echo esc_url( get_tag_link( $tag ) ); ?>"><?php echo esc_html( $tag->name ); ?></a>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Post Navigation -->
<nav class="post-nav">
  <div>
    <?php
    $prev = get_previous_post();
    if ( $prev ) :
    ?>
    <a href="<?php echo esc_url( get_permalink( $prev ) ); ?>">&larr; <?php echo esc_html( $prev->post_title ); ?></a>
    <?php endif; ?>
  </div>
  <div>
    <?php
    $next = get_next_post();
    if ( $next ) :
    ?>
    <a href="<?php echo esc_url( get_permalink( $next ) ); ?>"><?php echo esc_html( $next->post_title ); ?> &rarr;</a>
    <?php endif; ?>
  </div>
</nav>

<!-- Related Posts -->
<?php if ( $cat_slug ) : ?>
<section class="section" data-component="related">
  <div class="container">
    <div class="section__header">
      <div>
        <span class="section__label">More</span>
        <h2 style="margin-top:8px;">更多<?php echo esc_html( $cat_label ); ?>记录</h2>
      </div>
    </div>
    <ul class="article-list">
      <?php
      $related = new WP_Query( array(
        'category_name'  => $cat_slug,
        'posts_per_page' => 3,
        'post__not_in'   => array( get_the_ID() ),
      ) );
      if ( $related->have_posts() ) :
        while ( $related->have_posts() ) : $related->the_post();
      ?>
      <li class="article-item">
        <span class="article-item__date"><?php echo esc_html( yigeren_short_date() ); ?></span>
        <div class="article-item__body">
          <h3 class="article-item__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
        </div>
      </li>
      <?php
        endwhile;
        wp_reset_postdata();
      endif;
      ?>
    </ul>
  </div>
</section>
<?php endif; ?>

<?php
// Comments (optional)
if ( comments_open() || get_comments_number() ) :
  comments_template();
endif;

endwhile;
get_footer();
?>
