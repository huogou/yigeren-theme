<?php
/**
 * Front Page Template
 * @package yigeren
 */
get_header();
?>

<!-- Hero Banner -->
<header class="page-header hero-banner" data-component="hero">
    <div class="hero-bg">
      <img src="https://yaoqiang.xin/wp-content/uploads/2026/06/homepage-banner.png" alt="">
      <div class="hero-overlay"></div>
    </div>
    <div class="hero-content container">
      <span class="hero-kanji">記</span>
      <h1 class="hero-title">一个人的<br>互联网笔记</h1>
      <div class="hero-divider"></div>
      <p class="hero-tagline">记录骑车、摄影、养猫，以及利用互联网和AI做的一些小东西。</p>
      <div class="hero-cta-wrap">
        <a href="<?php echo esc_url( home_url( '/projects/' ) ); ?>" class="hero-cta">看看我折腾过什么 <span>&rarr;</span></a>
      </div>
    </div>
    <div class="hero-scroll-hint">
      <span>向下探索</span>
      <svg width="16" height="24" viewBox="0 0 16 24" fill="none">
        <rect x="5.5" y="1" width="5" height="9" rx="2.5" stroke="currentColor" stroke-width="1"/>
        <circle cx="8" cy="4.5" r="0.8" fill="currentColor" class="scroll-dot"/>
        <path d="M3 16L8 21L13 16" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>
  </header>

<!-- Category Contents (seal directory) -->
<section class="section" data-component="contents">
  <div class="container--wide">
    <div class="section__header reveal">
      <div>
        <span class="section__label">Contents</span>
        <h2 style="margin-top:8px;">我记些什么</h2>
      </div>
    </div>
    <div class="seal-row reveal reveal-delay-1">
      <?php
      $home_cats = array(
        'life'     => array( '日', '生活', 'Life',     '最近在骑车、拍照和陪猫晒太阳。' ),
        'moto'     => array( '道', '摩托', 'Riding',   '两个轮子，一条路，够了。' ),
        'cat'      => array( '猫', '猫咪', 'Cat',      '年糕，一只橘猫，正在认真地长大。' ),
        'photo'    => array( '光', '摄影', 'Photography', '用相机记住那些不值得写文章但值得记住的瞬间。' ),
        'notes'    => array( '筆', '笔记', 'Notes',    '想到什么就记下来，不一定完整，但值得留下。' ),
        'projects' => array( '造', '折腾', 'Projects', '记录那些利用互联网、AI和兴趣做出来的小东西，有的是完整作品，有的是一次尝试。' ),
      );
      foreach ( $home_cats as $slug => $info ) :
        $cat = get_category_by_slug( $slug );
      ?>
      <a href="<?php echo esc_url( $cat ? get_category_link( $cat ) : home_url( '/' . $slug . '/' ) ); ?>" class="seal">
        <span class="seal__kanji"><?php echo esc_html( $info[0] ); ?></span>
        <span class="seal__name"><?php echo esc_html( $info[1] ); ?></span>
        <span class="seal__en"><?php echo esc_html( $info[2] ); ?></span>
        <span class="seal__arrow">&rarr;</span>
        <span class="seal__desc"><?php echo esc_html( $info[3] ); ?></span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Recent Timeline -->
<section class="section" data-component="timeline">
  <div class="container--wide">
    <div class="section__header reveal">
      <div>
        <span class="section__label">Recent</span>
        <h2 style="margin-top:8px;">最近的生活</h2>
      </div>
    </div>
    <?php
    $recent = new WP_Query( array( 'posts_per_page' => 5, 'post_status' => 'publish' ) );
    if ( $recent->have_posts() ) :
      $current_month = '';
      while ( $recent->have_posts() ) : $recent->the_post();
        $post_month = date_i18n( 'Y年n月', get_post_timestamp() );
        if ( $post_month !== $current_month ) :
          $current_month = $post_month;
    ?>
    <div class="diary__month"><?php echo esc_html( $current_month ); ?></div>
    <?php endif; ?>
    <div class="diary__entry reveal">
      <div class="diary__dot"></div>
      <div class="diary__date"><?php echo esc_html( yigeren_full_date() ); ?></div>
      <div class="diary__text">
        <a href="<?php the_permalink(); ?>" style="color:inherit;"><?php the_title(); ?></a>
        <br><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 30 ) ); ?>
      </div>
      <?php $mood = yigeren_post_mood(); if ( $mood ) : ?>
      <span class="diary__mood"><?php echo esc_html( $mood ); ?></span>
      <?php endif; ?>
    </div>
    <?php
      endwhile;
      wp_reset_postdata();
    endif;
    ?>
  </div>
</section>

<!-- Riding & Cat (two columns) -->
<section class="section" data-component="riding-cat">
  <div class="container--wide">
    <div class="section__header reveal">
      <div>
        <span class="section__label">Riding &amp; Cat</span>
        <h2 style="margin-top:8px;">最近的路与猫</h2>
      </div>
    </div>
    <div class="duo reveal reveal-delay-1">
      <?php
      $moto_query = yigeren_category_query( 'moto', 1 );
      if ( $moto_query->have_posts() ) :
        $moto_query->the_post();
      ?>
      <a href="<?php the_permalink(); ?>" class="duo__half">
        <span class="duo__tag">道 · 摩托</span>
        <div class="duo__title"><?php the_title(); ?></div>
        <div class="duo__meta"><?php echo esc_html( yigeren_full_date() ); ?></div>
        <p class="duo__excerpt"><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 30 ) ); ?></p>
        <span class="duo__link">阅读全文 <span>&rarr;</span></span>
      </a>
      <?php
        wp_reset_postdata();
      else :
      ?>
      <div class="duo__half">
        <span class="duo__tag">道 · 摩托</span>
        <p class="duo__excerpt">还没有骑行记录。</p>
      </div>
      <?php endif; ?>
      <?php
      $cat_query = yigeren_category_query( 'cat', 1 );
      if ( $cat_query->have_posts() ) :
        $cat_query->the_post();
      ?>
      <a href="<?php the_permalink(); ?>" class="duo__half">
        <span class="duo__tag">猫 · 猫咪</span>
        <div class="duo__title"><?php the_title(); ?></div>
        <div class="duo__meta"><?php $mood = yigeren_post_mood(); echo esc_html( $mood ? $mood . ' · ' : '' ); echo esc_html( yigeren_full_date() ); ?></div>
        <p class="duo__excerpt"><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 30 ) ); ?></p>
        <span class="duo__link">阅读全文 <span>&rarr;</span></span>
      </a>
      <?php
        wp_reset_postdata();
      else :
      ?>
      <div class="duo__half">
        <span class="duo__tag">猫 · 猫咪</span>
        <p class="duo__excerpt">还没有猫咪记录。</p>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Projects (dynamic from projects category) -->
<?php
$projects = yigeren_projects_sorted( 5 );
?>
<section class="section" data-component="projects">
  <div class="section__kanji" aria-hidden="true">造</div>
  <div class="container--wide">
    <div class="section__header reveal">
      <div>
        <span class="section__label">Projects</span>
        <h2 style="margin-top:8px;">我在折腾什么</h2>
      </div>
      <?php $projects_cat = get_category_by_slug( 'projects' ); if ( $projects_cat ) : ?>
      <a href="<?php echo esc_url( get_category_link( $projects_cat ) ); ?>" class="section__more">全部折腾 &rarr;</a>
      <?php endif; ?>
    </div>
    <?php if ( ! empty( $projects ) ) : ?>
    <div class="proj-list reveal reveal-delay-1">
      <?php foreach ( $projects as $project ) :
        $status = yigeren_project_status( $project );
        $excerpt = get_the_excerpt( $project );
      ?>
      <a href="<?php echo esc_url( get_permalink( $project ) ); ?>" class="proj-row">
        <span class="proj-row__dot<?php echo $status === '已完成' ? ' proj-row__dot--done' : ''; ?>"></span>
        <span class="proj-row__name"><?php echo esc_html( get_the_title( $project ) ); ?></span>
        <span class="proj-row__desc"><?php echo esc_html( wp_trim_words( $excerpt, 30 ) ); ?></span>
        <span class="proj-row__status"><?php echo esc_html( $status ); ?></span>
        <span class="proj-row__date"><?php echo esc_html( yigeren_project_date( $project ) ); ?></span>
      </a>
      <?php endforeach; ?>
    </div>
    <?php else : ?>
    <div class="proj-empty reveal reveal-delay-1">第一个项目还在路上。</div>
    <?php endif; ?>
  </div>
</section>

<!-- Latest Articles -->
<section class="section" data-component="articles">
  <div class="container--wide">
    <div class="section__header reveal">
      <div>
        <span class="section__label">Latest</span>
        <h2 style="margin-top:8px;">最新文章</h2>
      </div>
      <?php $archive_page = get_page_by_path( 'archive' ); if ( $archive_page ) : ?>
      <a href="<?php echo esc_url( get_permalink( $archive_page ) ); ?>" class="section__more">全部归档 &rarr;</a>
      <?php endif; ?>
    </div>
    <ul class="article-list reveal reveal-delay-1">
      <?php
      $latest = new WP_Query( array( 'posts_per_page' => 8, 'post_status' => 'publish' ) );
      if ( $latest->have_posts() ) :
        while ( $latest->have_posts() ) : $latest->the_post();
          $cats = get_the_category();
          $cat_label = ! empty( $cats ) ? yigeren_category_label( $cats[0]->slug ) : '';
      ?>
      <li class="article-item">
        <span class="article-item__date"><?php echo esc_html( yigeren_short_date() ); ?></span>
        <div class="article-item__body">
          <?php if ( $cat_label ) : ?>
          <span class="article-item__cat"><?php echo esc_html( $cat_label ); ?></span>
          <?php endif; ?>
          <h3 class="article-item__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
          <p class="article-item__excerpt"><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 25 ) ); ?></p>
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

<?php get_footer(); ?>
