<?php
/**
 * Template Name: 归档页
 * @package yigeren
 */
get_header();
?>

<header class="page-header" data-component="page-header">
  <div class="page-header__kanji" aria-hidden="true">歸</div>
  <div class="container">
    <p class="page-header__label reveal">Archive</p>
    <h1 class="reveal reveal-delay-1">归档</h1>
    <p class="page-header__status reveal reveal-delay-2">"那些不想忘记的日子。"</p>
  </div>
</header>

<section class="section" data-component="archive-timeline">
  <div class="container">
    <?php
    $archive = yigeren_archive_data();
    $month_names = array(
      1 => '1月', 2 => '2月', 3 => '3月', 4 => '4月',
      5 => '5月', 6 => '6月', 7 => '7月', 8 => '8月',
      9 => '9月', 10 => '10月', 11 => '11月', 12 => '12月',
    );

    foreach ( $archive as $year => $months ) :
    ?>
    <h2 class="archive-year reveal"><?php echo esc_html( $year ); ?></h2>
    <?php foreach ( $months as $month => $posts ) : ?>
      <div class="archive-month reveal"><?php echo esc_html( $month_names[ intval( $month ) ] ); ?></div>
      <?php foreach ( $posts as $post ) : setup_postdata( $post );
        $cats = get_the_category( $post );
        $cat_label = ! empty( $cats ) ? yigeren_category_label( $cats[0]->slug ) : '';
      ?>
      <div class="archive-entry reveal">
        <a href="<?php echo esc_url( get_permalink( $post ) ); ?>" class="archive-entry__title"><?php echo esc_html( $post->post_title ); ?></a>
        <?php if ( $cat_label ) : ?>
        <span class="archive-entry__cat"><?php echo esc_html( $cat_label ); ?></span>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    <?php endforeach; ?>
    <?php endforeach; ?>
  </div>
</section>

<?php get_footer(); ?>
