<?php

// ============================================== 
//  読み込みアセット（CSS・Adobe Fonts）
// ==============================================
function haru_enqueue_assets() {
    /* 親テーマ CSS */
    wp_enqueue_style(
        'tt5-parent',
        get_template_directory_uri() . '/style.css'
    );

    /* 子テーマ CSS（更新日時をバージョンにしてキャッシュバスティング）*/
    $child_ver = filemtime( get_stylesheet_directory() . '/style.css' );
    wp_enqueue_style(
        'haru-child',
        get_stylesheet_directory_uri() . '/style.css',
        array( 'tt5-parent' ),
        $child_ver
    );

    /* Adobe Fonts (Typekit) – 源ノ角ゴシック */
    wp_enqueue_script( 'haru-typekit', 'https://use.typekit.net/bew5wgt.js', array(), null, false );
    wp_add_inline_script( 'haru-typekit', 'try{Typekit.load({async:true});}catch(e){}' );

    /* フリップカード JS */
    wp_enqueue_script(
        'haru-flip-card',
        get_stylesheet_directory_uri() . '/js/flip-card.js',
        array(),
        filemtime( get_stylesheet_directory() . '/js/flip-card.js' ),
        true
    );
}
add_action( 'wp_enqueue_scripts', 'haru_enqueue_assets' );

// ============================================== 
//  フロントページのギャラリー表示件数制限
// ==============================================
/**
 * フロントページの haru-gallery-grid だけ件数制限（wide投稿は2件扱い）
 */
add_filter(
    'query_loop_block_query_vars',
    function ( $query_vars, $block ) {

        // ① 表示中のページがフロントでなければ何もしない
        if ( ! is_front_page() ) {
            return $query_vars;
        }

        // ② ブロックの追加クラス名を取得（無い場合は空文字）
        $class = $block->attributes['className'] ?? '';

        // ③ haru-gallery-grid というクラスを持っているか確認
        if ( str_contains( $class, 'haru-gallery-grid' ) ) {
            // カテゴリ 'gallery' の投稿でwide投稿の数を取得
            $gallery_posts = get_posts([
                'category_name'  => 'gallery',
                'posts_per_page' => -1,
                'fields'         => 'ids'
            ]);
            
            $wide_count = 0;
            foreach ( $gallery_posts as $post_id ) {
                if ( has_tag( 'haru-gallery-item-wide', $post_id ) ) {
                    $wide_count++;
                }
            }
            
            // 目標グリッド数15に合わせて表示件数を調整
            // wide投稿は2マス使うので、その分通常投稿の表示数を減らす
            $target_grid_spaces = 15;
            $max_posts = $target_grid_spaces - $wide_count;
            
            // 最低でも10件は表示する
            $query_vars['posts_per_page'] = max( $max_posts, 10 );
        }

        return $query_vars;
    },
    10,
    2
);

// ============================================== 
//  ギャラリータグ一覧ショートコード
// ==============================================
/**
 * ギャラリーカテゴリの記事で使われているタグ一覧を表示
 */
add_shortcode( 'haru_gallery_tags', function () {

    /* ▼ 必要ならここの 'gallery' を自分のカテゴリースラッグに変えてな */
    $post_ids = get_posts( [
        'category_name'  => 'gallery',
        'fields'         => 'ids',
        'posts_per_page' => -1
    ] );

    if ( empty( $post_ids ) ) return '';

    /* 1時間だけキャッシュ（無駄クエリ削減）*/
    $tags = get_transient( 'gallery_tags_cache' );
    if ( false === $tags ) {
        $tags = wp_get_object_terms( $post_ids, 'post_tag', [ 'orderby' => 'name' ] );
        set_transient( 'gallery_tags_cache', $tags, HOUR_IN_SECONDS );
    }

    if ( is_wp_error( $tags ) || empty( $tags ) ) return '';

    $out = '<ul class="haru-gallery-tag-list">';
    foreach ( $tags as $tag ) {
        // haru-gallery-item-wide タグを除外
        if ( $tag->slug === 'haru-gallery-item-wide' ) {
            continue;
        }
        
        $out .= sprintf(
            '<li><a href="%s">%s</a></li>',
            esc_url( get_tag_link( $tag ) ),
            esc_html( $tag->name )
        );
    }
    $out .= '</ul>';
    return $out;
} );