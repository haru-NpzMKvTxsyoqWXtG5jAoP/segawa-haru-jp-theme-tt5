<?php

// ============================================== 
//  読み込みアセット（CSS・JS）
// ==============================================
function haru_enqueue_assets() {
    /* 子テーマ CSS（更新日時をバージョンにしてキャッシュバスティング）*/
    $css_path = get_stylesheet_directory() . '/style.css';
    $child_ver = file_exists( $css_path ) ? filemtime( $css_path ) : '1.0.0';
    
    wp_enqueue_style(
        'haru-child',
        get_stylesheet_directory_uri() . '/style.css',
        array( 'twentytwentyfive-style' ), // 親テーマの正式ハンドル名に依存
        $child_ver
    );

    /* フリップカード JS */
    $js_path = get_stylesheet_directory() . '/js/flip-card.js';
    $js_ver = file_exists( $js_path ) ? filemtime( $js_path ) : '1.0.0';
    
    wp_enqueue_script(
        'haru-flip-card',
        get_stylesheet_directory_uri() . '/js/flip-card.js',
        array(),
        $js_ver,
        true
    );
}
add_action( 'wp_enqueue_scripts', 'haru_enqueue_assets' );


// ============================================== 
//  ギャラリータグ一覧
// ==============================================
/**
 * ギャラリーカテゴリの記事で使われているタグ一覧を表示
 * ショートコード [haru_gallery_tags] で使用
 */
function haru_gallery_tags_render() {
    /* 1時間だけキャッシュ（無駄クエリ削減）*/
    $tags = get_transient( 'gallery_tags_cache' );
    if ( false === $tags ) {
        /* WordPressらしい安全な方法でタグを取得 */
        $tags = get_terms( array(
            'taxonomy'   => 'post_tag',
            'hide_empty' => true,
            'object_ids' => get_posts( array(
                'category_name'  => 'gallery',
                'fields'         => 'ids',
                'posts_per_page' => 1000, // 実用的な上限
                'post_status'    => 'publish',
                'no_found_rows'  => true,
            ) ),
            'orderby'    => 'name',
            'order'      => 'ASC',
        ) );
        
        if ( is_wp_error( $tags ) ) {
            $tags = array();
        }
        
        set_transient( 'gallery_tags_cache', $tags, HOUR_IN_SECONDS );
    }

    if ( is_wp_error( $tags ) || empty( $tags ) ) return '';

    $out = '<ul class="haru-gallery-tag-list">';
    foreach ( $tags as $tag ) {
        // 内部用タグを除外
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
//  ショートコード登録
// ==============================================
add_shortcode( 'haru_gallery_tags', 'haru_gallery_tags_render' );

// ============================================== 
//  キャッシュクリア機能
// ==============================================
/**
 * 投稿更新時にギャラリータグキャッシュをクリア
 */
function haru_clear_gallery_tags_cache() {
    delete_transient( 'gallery_tags_cache' );
}
add_action( 'save_post', 'haru_clear_gallery_tags_cache' );
add_action( 'deleted_post', 'haru_clear_gallery_tags_cache' );
add_action( 'set_object_terms', 'haru_clear_gallery_tags_cache' );