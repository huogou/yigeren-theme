<?php
/**
 * Category Template - adapts layout based on category slug
 * @package yigeren
 */
get_header();

$cat_slug = get_query_var( 'category_name' );
if ( ! $cat_slug ) {
  $cat = get_queried_object();
  $cat_slug = $cat ? $cat->slug : '';
}
$cat_label  = yigeren_category_label( $cat_slug );
$cat_en     = yigeren_category_en( $cat_slug );
$cat_kanji  = yigeren_category_kanji( $cat_slug );
$cat_desc   = yigeren_category_desc( $cat_slug );
?>

<!-- Page Header -->
<header class="page-header" data-component="page-header">
  <div class="page-header__kanji" aria-hidden="true"><?php echo esc_html( $cat_kanji ); ?></div>
  <div class="container">
    <p class="page-header__label reveal"><?php echo esc_html( $cat_en ); ?></p>
    <h1 class="reveal reveal-delay-1"><?php echo esc_html( $cat_label ); ?></h1>
    <?php if ( $cat_desc ) : ?>
    <p class="page-header__status reveal reveal-delay-2">"<?php echo esc_html( $cat_desc ); ?>"</p>
    <?php endif; ?>
  </div>
</header>

<!-- Category Content -->
<section class="section" data-component="category-content">
<div class="container--wide">

<?php
// === MOTO: Featured + Ride List ===
if ( $cat_slug === 'moto' ) :
  $moto_posts = yigeren_category_query( 'moto', 20 );
  $first = true;
  if ( $moto_posts->have_posts() ) :
    while ( $moto_posts->have_posts() ) : $moto_posts->the_post();
      if ( $first ) : $first = false;
?>
  <div class="featured reveal">
    <div class="featured__cover">
      <?php if ( has_post_thumbnail() ) the_post_thumbnail( 'article-cover' ); ?>
    </div>
    <div>
      <div class="featured__date"><?php echo esc_html( yigeren_full_date() ); ?></div>
      <h2><?php the_title(); ?></h2>
      <p class="featured__text"><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 40 ) ); ?></p>
      <a href="<?php the_permalink(); ?>" class="featured__link">阅读全文 <span>&rarr;</span></a>
    </div>
  </div>
<?php else : ?>
  <?php if ( $moto_posts->current_post === 1 ) : ?>
  <div class="section__header reveal" style="margin-top:48px;">
    <div><span class="section__label">Archive</span><h2 style="margin-top:8px;">骑行记录</h2></div>
  </div>
  <ul class="ride-list">
  <?php endif; ?>
  <li class="ride-item reveal">
    <div class="ride-item__cover">
      <?php if ( has_post_thumbnail() ) the_post_thumbnail( 'card-cover' ); ?>
    </div>
    <div class="ride-item__body">
      <div class="ride-item__date"><?php echo esc_html( yigeren_full_date() ); ?></div>
      <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
      <p class="ride-item__text"><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 30 ) ); ?></p>
    </div>
  </li>
<?php
      endif;
    endwhile;
    if ( ! $first ) echo '</ul>';
    wp_reset_postdata();
  endif;

// === CAT: Photo Wall ===
elseif ( $cat_slug === 'cat' ) :
  $cat_posts = yigeren_category_query( 'cat', 30 );
  if ( $cat_posts->have_posts() ) :
?>
  <div class="cat-wall">
  <?php while ( $cat_posts->have_posts() ) : $cat_posts->the_post(); ?>
    <a href="<?php the_permalink(); ?>" class="cat-card reveal" style="text-decoration:none;">
      <div class="cat-card__img">
        <?php if ( has_post_thumbnail() ) the_post_thumbnail( 'card-cover' ); ?>
      </div>
      <div class="cat-card__body">
        <p><?php the_title(); ?></p>
        <?php $mood = yigeren_post_mood(); if ( $mood ) : ?>
        <span class="cat-card__tag"><?php echo esc_html( $mood ); ?></span>
        <?php endif; ?>
      </div>
    </a>
  <?php endwhile; ?>
  </div>
<?php
    wp_reset_postdata();
  endif;

// === PHOTO: Grid Gallery ===
elseif ( $cat_slug === 'photo' ) :
  $photo_posts = yigeren_category_query( 'photo', 30 );
  if ( $photo_posts->have_posts() ) :
?>
  <div class="photo-grid">
  <?php
  $i = 0;
  while ( $photo_posts->have_posts() ) : $photo_posts->the_post();
    $is_large = ( $i % 5 === 0 );
  ?>
    <div class="photo-grid__item<?php echo $is_large ? ' photo-grid__item--large' : ''; ?> reveal">
      <a href="<?php the_permalink(); ?>">
        <?php if ( has_post_thumbnail() ) : ?>
          <?php the_post_thumbnail( $is_large ? 'photo-large' : 'card-cover' ); ?>
        <?php endif; ?>
      </a>
      <div class="photo-grid__caption">
        <div class="photo-grid__title"><?php the_title(); ?></div>
        <?php
        $location = get_post_meta( get_the_ID(), '_yigeren_location', true );
        $camera = get_post_meta( get_the_ID(), '_yigeren_camera', true );
        if ( $location || $camera ) :
        ?>
        <div class="photo-grid__meta"><?php echo esc_html( $location ); ?><?php echo $camera ? ' · ' . esc_html( $camera ) : ''; ?></div>
        <?php endif; ?>
      </div>
    </div>
  <?php $i++; endwhile; ?>
  </div>
<?php
    wp_reset_postdata();
  endif;

// === PROJECTS: Status-grouped project list ===
elseif ( $cat_slug === 'projects' ) :
  $project_posts = yigeren_projects_sorted();
  $ongoing = array();
  $done    = array();
  foreach ( $project_posts as $project ) {
    if ( yigeren_project_status( $project ) === '已完成' ) {
      $done[] = $project;
    } else {
      $ongoing[] = $project;
    }
  }
  if ( empty( $project_posts ) ) :
?>
  <div class="proj-empty reveal">第一个项目还在路上。</div>
<?php
  else :
    $groups = array( array( '— 进行中 —', $ongoing ), array( '— 已完成 —', $done ) );
    foreach ( $groups as $group ) :
      list( $group_label, $group_posts ) = $group;
      if ( empty( $group_posts ) ) { continue; }
?>
  <div class="proj-group reveal"><?php echo esc_html( $group_label ); ?></div>
  <?php foreach ( $group_posts as $project ) :
    $status    = yigeren_project_status( $project );
    $excerpt   = get_the_excerpt( $project );
    $cover_url = get_post_meta( $project->ID, 'project_cover', true );
  ?>
  <div class="proj-card reveal">
    <div class="proj-card__cover">
      <?php if ( $cover_url ) : ?>
        <img src="<?php echo esc_url( $cover_url ); ?>" alt="<?php echo esc_attr( get_the_title( $project ) ); ?>">
      <?php elseif ( has_post_thumbnail( $project ) ) : ?>
        <?php echo get_the_post_thumbnail( $project, 'card-cover' ); ?>
      <?php else : ?>
        <span class="proj-card__cover-kanji" aria-hidden="true">造</span>
      <?php endif; ?>
    </div>
    <div class="proj-card__body">
      <span class="proj-card__status<?php echo $status === '已完成' ? ' proj-card__status--done' : ''; ?>"><?php echo esc_html( $status ); ?></span>
      <h3 class="proj-card__title"><a href="<?php echo esc_url( get_permalink( $project ) ); ?>"><?php echo esc_html( get_the_title( $project ) ); ?></a></h3>
      <div class="proj-card__meta"><?php echo esc_html( yigeren_project_date( $project ) ); ?></div>
      <p class="proj-card__excerpt"><?php echo esc_html( wp_trim_words( $excerpt, 40 ) ); ?></p>
      <a href="<?php echo esc_url( get_permalink( $project ) ); ?>" class="proj-card__link">查看详情 <span>&rarr;</span></a>
    </div>
  </div>
  <?php endforeach; ?>
<?php
    endforeach;
  endif;

// === NOTES: Minimal List ===
elseif ( $cat_slug === 'notes' ) :
  $note_posts = yigeren_category_query( 'notes', 30 );
  if ( $note_posts->have_posts() ) :
?>
  <ul class="note-list">
  <?php while ( $note_posts->have_posts() ) : $note_posts->the_post(); ?>
    <li class="note-item reveal">
      <span class="note-item__date"><?php echo esc_html( yigeren_short_date() ); ?></span>
      <span class="note-item__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></span>
      <span class="note-item__cat"><?php echo esc_html( $cat_label ); ?></span>
    </li>
  <?php endwhile; ?>
  </ul>
<?php
    wp_reset_postdata();
  endif;

// === LIFE & DEFAULT: Diary Timeline ===
else :
  $timeline = yigeren_timeline_posts( $cat_slug );
  foreach ( $timeline as $month_key => $posts ) :
    $month_parts = explode( '-', $month_key );
    $month_label = date_i18n( 'Y年n月', mktime( 0, 0, 0, intval( $month_parts[1] ), 1, intval( $month_parts[0] ) ) );
?>
  <div class="diary__month reveal"><?php echo esc_html( $month_label ); ?></div>
  <?php foreach ( $posts as $post ) : setup_postdata( $post ); ?>
  <div class="diary__entry reveal">
    <div class="diary__dot"></div>
    <div class="diary__date"><?php echo esc_html( yigeren_full_date() ); ?></div>
    <div class="diary__text">
      <a href="<?php the_permalink(); ?>" style="color:inherit;"><?php the_title(); ?></a>
      <br><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 30 ) ); ?>
    </div>
    <?php if ( has_post_thumbnail() ) : ?>
    <div style="margin:12px 0;border-radius:var(--seed-radius);overflow:hidden;max-width:520px;">
      <?php the_post_thumbnail( 'card-cover' ); ?>
    </div>
    <?php endif; ?>
    <?php $mood = yigeren_post_mood(); if ( $mood ) : ?>
    <span class="diary__mood"><?php echo esc_html( $mood ); ?></span>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>
<?php
  endforeach;
endif;
?>

</div>
</section>

<!-- Pagination -->
<?php
the_posts_pagination( array(
  'mid_size'  => 2,
  'prev_text' => '&larr;',
  'next_text' => '&rarr;',
  'class'     => 'pagination',
) );
?>

<?php get_footer(); ?>
