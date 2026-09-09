<?php
/**
 * Main Index Template (fallback)
 * @package yigeren
 */
get_header();
?>

<header class="page-header" data-component="page-header">
  <div class="page-header__kanji" aria-hidden="true">記</div>
  <div class="container">
    <p class="page-header__label reveal">Blog</p>
    <h1 class="reveal reveal-delay-1"><?php echo is_search() ? '搜索结果' : '一个人的互联网笔记'; ?></h1>
  </div>
</header>

<section class="section" data-component="post-list">
  <div class="container--wide">
    <ul class="article-list">
      <?php if ( have_posts() ) : while ( have_posts() ) : the_post();
        $cats = get_the_category();
        $cat_label = ! empty( $cats ) ? yigeren_category_label( $cats[0]->slug ) : '';
      ?>
      <li class="article-item reveal">
        <span class="article-item__date"><?php echo esc_html( yigeren_short_date() ); ?></span>
        <div class="article-item__body">
          <?php if ( $cat_label ) : ?>
          <span class="article-item__cat"><?php echo esc_html( $cat_label ); ?></span>
          <?php endif; ?>
          <h3 class="article-item__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
          <p class="article-item__excerpt"><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 25 ) ); ?></p>
        </div>
      </li>
      <?php endwhile; else : ?>
      <li class="article-item">
        <div class="article-item__body">
          <p style="color:var(--seed-muted);">暂无内容。</p>
        </div>
      </li>
      <?php endif; ?>
    </ul>
  </div>
</section>

<?php
the_posts_pagination( array(
  'mid_size'  => 2,
  'prev_text' => '&larr;',
  'next_text' => '&rarr;',
  'class'     => 'pagination',
) );
?>

<?php get_footer(); ?>
