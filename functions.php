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
//  ギャラリータグ一覧
// ==============================================
/**
 * ギャラリーカテゴリの記事で使われているタグ一覧を表示
 * ショートコード [haru_gallery_tags] およびテンプレートパーツで使用
 */
function haru_gallery_tags_render() {
    /* ▼ 必要ならここの 'gallery' を自分のカテゴリースラッグに変えてな */
    $post_ids = get_posts( [
        'category_name'  => 'gallery',
        'fields'         => 'ids',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'no_found_rows'  => true,
        'suppress_filters' => true,
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
}

// ============================================== 
//  ショートコード（後方互換）
// ==============================================
add_shortcode( 'haru_gallery_tags', 'haru_gallery_tags_render' );


