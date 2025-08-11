# segawa-haru.jp WordPress テーマ

## 基本情報
- **サイト**: https://segawa-haru.jp/
- **テーマ**: Twenty Twenty-Five 子テーマ
- **デプロイ**: WP pusher プラグインで GitHub から自動反映

## 開発方針
- **保守性重視**: シンプルで理解しやすいコード
- **モバイルファースト**: レスポンシブ設計
- **WordPress ネイティブ**: ブロックエディタ優先
- **パフォーマンス**: 軽量で高速な動作
- **アクセシビリティ**: WCAG 準拠

## 技術スタック

### フォント
- **Noto Sans JP**: セルフホスト配信（400, 500, 700）
- **GDPR 対応**: 外部 CDN 不使用、完全プライバシー保護
- **フォントサイズ**: theme.json で clamp() による完全レスポンシブ
- **パフォーマンス**: font-display: swap + 自動preload対応

### CSS 設計
- **CSS 変数**: 一貫性のあるデザインシステム
- **clamp()**: レスポンシブな値設定
- **モダン CSS**: Grid、Flexbox、CSS Variables

### JavaScript
- **最小限**: フリップカード機能のみ
- **アクセシビリティ**: キーボードナビゲーション対応

## 主要コンポーネント

### カードシステム
- **サービスカード**: 最大5列、ホバーアニメーション
- **スクラップカード**: 最大6列、2行省略テキスト
- **ギャラリーカード**: 最大7列、wide タグで2列分表示

### レスポンシブスペーサー
- **3サイズ**: large、medium、small
- **用途**: WordPress スペーサーブロックの拡張

### 縦型ヘッダー
- **縦書きナビゲーション**: writing-mode 使用
- **sticky 配置**: スクロール追従

### フリップカード
- **3D 回転**: CSS transforms
- **WCAG 4.1.2 準拠**: role="button", aria-pressed, 動的 aria-label
- **キーボード操作**: Enter/Space キー対応
- **高さ統一**: ブロックエディタの「寸法」→「最小の高さ」で530px程度に設定（CSS不要）

## 最適化実装

### パフォーマンス
- **軽量クエリ**: fields=ids + no_found_rows で最適化
- **WebP 画像配信**: EWWW IO連携で30-50%軽量化
- **画像運用方針**:
  - 重要画像（サービス/プロフィール）: JPG/PNGでアップロード → EWWWが自動WebP変換
  - ブログ記事画像: 最初からWebPでアップロード（2025年8月以降）
  - EWWWのメリット: メタデータ自動削除、.htaccess自動設定、画像最適化
- **フォントキャッシュ**: .htaccess で1年間長期キャッシュ
- **エラーハンドリング**: ファイル存在チェック

### セキュリティ
- **エスケープ処理**: esc_html、esc_url 使用
- **外部依存最小化**: セルフホスト方針

### アクセシビリティ
- **WCAG 4.1.2**: フリップカードで Name, Role, Value 実装
- **動的 aria-label**: 状態に応じて「裏面を表示」「表面を表示」
- **フォーカス管理**: focus-visible 対応
- **キーボード**: 全機能操作可能

## ファイル構成
```
theme-root/
├── functions.php      # 機能拡張
├── style.css         # メインスタイル
├── theme.json        # テーマ設定（フォント定義含む）
├── CLAUDE.md         # プロジェクト説明書（実際はclaude.md）
├── .htaccess         # WebP配信・フォントキャッシュ設定
├── .gitattributes    # フォントファイルのバイナリ指定
├── .gitignore        # Git除外設定
├── fonts/            # Noto Sans JP セルフホスト（2.8MB）
│   ├── noto-sans-jp-400.woff2
│   ├── noto-sans-jp-500.woff2
│   └── noto-sans-jp-700.woff2
├── images/           # 画像ファイル
│   └── harusegawa_icon_2.png  # Person構造化データ用アイコン（800×800px）
├── js/
│   └── flip-card.js  # フリップカード機能
└── custom-html/      # カスタムHTML・テーブル・SVGファイル
    ├── haru-legal-notice_html.txt            # リーガルノーティス
    ├── haru-service-card_link_customHTML.txt # サービスカード
    ├── haru-privacy_html.txt                 # プライバシーポリシー
    ├── service_page/                         # サービスページ用テーブル
    │   ├── haru-table_illustration_customHTML.txt
    │   ├── haru-table_lecture-payment_customHTML.txt
    │   ├── haru-table_lecture_customHTML.txt
    │   ├── haru-table_photo-retouching_customHTML.txt
    │   ├── haru-table_print-design_customHTML.txt
    │   └── haru-table_webimage_customHTML.txt
    └── svg/                                  # SVGファイル
        ├── harusegawa-footer-logo-svg_customHTML.html
        ├── harusegawa-footer-scrap-logo-svg_customHTML.html
        ├── harusegawa-scrapbook-logo-svg_customHTML.txt
        └── harusegawa-title-logo-svg_customHTML.txt
```

## 重要な実装詳細

### 内部用タグシステム
- **haru-gallery-item-wide**: ギャラリーで2列分表示
- **CSS で非表示**: 一般ユーザーには見えない
- **PHP で除外**: タグ一覧からも除外

### GDPR 対応
- **Google Fonts**: システムフォント方式
- **外部リクエスト**: 完全に除去
- **プライバシー**: ユーザー IP 送信なし

### 料金表コンポーネント（.hr-table）
**技術実装の核心:**
- `border-collapse: separate` + `border-spacing: 0` でボーダー重なり問題を根本解決
- `grid-auto-rows: 1fr` で将来的な行数拡張に対応
- CSS変数 `--hr-table-border` でボーダー定義を一元管理
- colgroup による列幅制御とセル装飾の完全分離

**HTML構造設計:**
- セマンティックテーブル（table + caption + colgroup + tbody）
- `scope="row"` でアクセシビリティ配慮
- インラインstyle完全除去（CSS分離徹底）
- thead「項目/内容」は冗長と判断し採用せず

**均等分割システム:**
- `.hr-split`（Grid）で高さの均等分割
- `.hr-pane`（Flexbox）で縦中央配置と内容整理
- `border-top` による視覚的境界線（構造とデザインの役割分離）

**重要な設計判断:**
- ❌ border-collapse: collapse → 線が重なって太くなる
- ✅ border-collapse: separate → 各セル独立、隙間0で統一感
- ❌ rowspan使用 → 空行必要、構造複雑
- ✅ Grid内部分割 → シンプル、拡張容易

**絶対禁止事項（テーブル特有）:**
- 勝手なfont-size指定（large、small等）
- 勝手なfont-weight調整（500等）
- HTMLへのstyle属性混在
- 冗長なCSS記述（.hr-table p で包括可能なmargin指定等）

**拡張ガイドライン:**
- 行数増加：HTML の .hr-pane 追加のみ（CSS変更不要）
- ボーダー変更：--hr-table-border 変数のみ修正
- 列幅調整：colgroup の CSS のみ変更

**失敗から学んだ教訓:**
- thead視覚隠しCSS → 無駄な複雑化（theadそのものが不要だった）
- 横スクロール対策 → body min-width で既に対応済みだった
- 複数の冗長CSS → .hr-table p { margin: 0; } で包括対応可能

**技術的詳細メモ:**
- `overflow-wrap: break-word` で日本語長文の自然な改行
- `table-layout: fixed` でレンダリング最適化
- CSS変数は必ず hr- プレフィックス（--hr-table-border等）
- セル内Grid+Flexboxは異なる責務（高さ分割 vs 縦中央配置）

**アクセシビリティ配慮:**
- caption でテーブル目的明示
- scope="row" で行見出し識別
- thead は必要十分性で判断（自明な場合は省略可）

## 絶対禁止事項：その場しのぎ解決策

**問題解決の原則:**
- 問題の根本原因を特定してから解決策を考える
- 「とりあえず動く方法」は一切提案しない
- CSS問題をHTML style属性で解決するような設計破綻は厳禁
- 設計の一貫性を最優先し、途中で方針転換しない
- 推測で答えず、不明な場合は「調査が必要」と正直に伝える

**具体例:**
- ❌ CSSが効かない → HTMLのstyle属性で解決
- ✅ CSSが効かない → なぜ効かないのか根本原因を特定
- ❌ レイアウト崩れ → 別の方法で見た目だけ修正
- ✅ レイアウト崩れ → 構造的な問題を見直して根本解決

## パフォーマンス最適化の判断基準

### 実装済みの最適化
- ✅ WebP画像変換（EWWW IO）
- ✅ フォントのセルフホスト
- ✅ font-display: swap
- ✅ キャッシュバスティング
- ✅ 軽量クエリ（fields=ids）
- ✅ Contact Form 7の条件付き読み込み（2025年8月実装）
  - フロントページ以外でCF7のCSS/JSを完全除外
  - reCAPTCHAスクリプトも同様に制御
  - ページ読み込み速度の改善

### 実装を見送った最適化
**フォントのプリロード（2025年8月検討）**
- Noto Sans JPは各500KB〜1MBと重い
- 使わないウェイトをプリロードすると逆効果
- 現状の`font-display: swap`で十分最適化されている
- 結論：必要ない実装はしない

## SEO/OGP実装（2025年1月実装）

### 概要
SEOプラグインに依存せず、functions.phpで基本的なSEO機能を実装。軽量かつ保守性の高い設計。

### 実装機能
1. **meta description**
   - トップページ: サイトのキャッチフレーズ
   - 投稿/固定ページ: 抜粋優先、なければ本文から120文字自動生成
   - アーカイブページ: 出力しない

2. **OGPタグ**
   - og:title, og:description, og:image, og:url, og:type, og:site_name
   - 画像の優先順位: アイキャッチ → サイトアイコン → デフォルト画像
   - アーカイブページでも出力（SNSシェア対応）

3. **Twitterカード**
   - summary_large_imageで大きな画像表示
   - OGPと同じ値を使用して一貫性確保

### 技術的ポイント
- **`haru_seo_enabled()`フィルター**: 将来的にSEOプラグイン導入時は`add_filter('haru_seo_enabled', '__return_false')`で無効化可能
- **`has_excerpt()`で手動抜粋判定**: `get_the_excerpt()`は自動生成するため不適切
- **`get_post_field('post_content')`**: フィルターが走らない生データ取得
- **再定義ガード**: 定数の二重定義エラー防止
- **不要コンテキスト除外**: 管理画面、フィード、埋め込み、JSON APIでは出力しない
- **固定ページの抜粋有効化**: `add_post_type_support('page', 'excerpt')`
- **og:url生成**: `wp_get_canonical_url()`でカテゴリ・タグ・検索・ページ送りも正確に対応

### デフォルトOGP画像
- パス: `images/harusegawa_ogp.png`（1200×630px）
- テーマ内に配置してGit管理

### 拡張可能性
- 構造化データ（JSON-LD）
- og:image:width/height
- article:published_time/modified_time

## noindex設定について

### 現在の状況（2025年8月）
WordPressが以下を自動で処理しているため、テーマ側での実装は不要：

1. **検索結果ページ**
   - WordPress 5.7以降、`wp_robots_noindex_search()`が自動でnoindex出力
   - URLパターン: `?s=keyword`
   - テーマでの対応: 不要

2. **添付ファイルページ**
   - 現在404で無効化されている（プラグインまたは設定による）
   - URLパターン: `?attachment_id=123`
   - noindex不要（そもそもページが存在しない）

3. **日付アーカイブページ**
   - Twenty Twenty-Fiveテーマでテンプレートが提供されていない
   - URLパターン: `/2025/`、`/2025/08/` など
   - 404となるためnoindex不要

4. **404ページ**
   - HTTPステータス404により自動的に検索エンジンから除外
   - 明示的なnoindex不要

### 結論
現状でSEO的に理想的な状態。追加のnoindex実装は不要。

### タグページのnoindex設定（2025年8月実装）
- `wp_robots`フィルターでタグアーカイブをnoindex設定
- XMLサイトマップからpost_tagとusersを除外
- 個人サイトに適した最適化

## XMLサイトマップ（WordPress標準）

### トラブルシューティング
`/wp-sitemap.xml`がHTMLを返す場合：
1. **パーマリンク設定を再保存**（設定 > パーマリンク > 変更せず保存）
2. キャッシュプラグインの除外設定を確認
3. セキュリティプラグイン（SiteGuard等）の設定確認

### 正常動作の確認
- `/wp-sitemap.xml`にアクセスして`<?xml`で始まるXMLが表示される
- Google Search Consoleでサイトマップが正常に認識される

## パンくずリスト（2025年8月実装）

### 概要
構造化データ対応のパンくずリストをショートコードで実装。視覚的ナビゲーションとSEO対策を両立。

### 実装内容
1. **ショートコード**: `[haru_breadcrumbs]` で任意位置に設置
2. **HTML構造**: セマンティックな `<nav><ol>` 構造
3. **構造化データ**: 
   - Microdata属性（itemprop, itemscope）
   - JSON-LD の `BreadcrumbList` を同時出力
4. **アクセシビリティ**: aria-label、aria-current属性

### 階層設計
```
トップページ: 非表示

固定ページ:
Home > ページ名
Home > 親ページ > ページ名（階層がある場合）

投稿（スクラップブック）:
Home > Scrapbook > GALLERY > 投稿名

カテゴリ/タグアーカイブ:
Home > カテゴリ名/タグ名

検索結果:
Home > 検索結果

404:
Home > 404 Not Found
```

### 技術的実装
- **カテゴリーベース対応**: `get_option('category_base')`が'scrapbook'の場合、固定ページへのリンクを挿入
- **パフォーマンス**: `get_page_by_path()`による軽量クエリ
- **レスポンシブ**: flexboxで自動折り返し
- **区切り文字**: CSS疑似要素で「›」を表示

### 使用方法
WordPressエディタで「ショートコード」ブロックを追加し、`[haru_breadcrumbs]` を入力。
推奨位置：
- ヘッダー直下
- ページタイトルの上部
- コンテンツエリアの最上部

### 実装の詳細
- **SCRAP BOOK階層**: URLに`/scrapbook/`が含まれる投稿で自動的に固定ページを挿入
- **カテゴリ優先順位**: 複数カテゴリの場合、階層が深い（より具体的な）カテゴリを選択
- **CSS最適化**: `font-size: inherit`で親テーマの上書きを防止
- **注意**: 段落ブロックではなく「ショートコード」ブロックを使用すること（`<p>`タグ回避）

## Person構造化データ（2025年8月実装）

### 概要
個人ブランディング強化のため、瀬川晴の人物情報を構造化データとして実装。

### 実装内容
1. **基本情報**
   - 名前: 瀬川 晴
   - 職種: クリエイター
   - 画像: images/harusegawa_icon_2.png（800×800px）

2. **表記ゆれ対応（alternateName）**
   - 瀬川晴、せがわはる、セガワハル
   - Haru Segawa、segawa haru、HARUsegawa

3. **SNSアカウント連携（sameAs）**
   - Instagram: https://www.instagram.com/haru_segawa
   - Pinterest: https://jp.pinterest.com/haru_segawa/
   - Behance: https://www.behance.net/haru_segawa

4. **技術的実装**
   - @id: `https://segawa-haru.jp/#person` で他の構造化データから参照可能
   - トップページのみ出力（優先度6）
   - JSON-LD形式で実装

### 期待される効果
- Google検索での人物認識向上
- 指名検索での優位性
- 将来的なナレッジパネル表示の可能性

## 吹き出しシステム（2025年8月実装）

### 概要
会話形式のコンテンツ表現のための吹き出しシステム。ショートコードで簡単に実装可能。

### 使用方法
```
[bubble name="user1" pos="left"]こんにちは！[/bubble]
[bubble name="user2" pos="right"]はじめまして[/bubble]
```

### キャラクター設定
- **user1** (晴): 左配置のみ、緑色の枠線
- **user2**: 左右配置可能、ピンク色の枠線
- **user3**: 左右配置可能、水色の枠線

### 技術的実装
- **画像**: WebP形式（80×80px）でimages/chat/に配置
- **CSS変数**: サイズ、三角形、ボーダー幅を変数管理
- **アクセシビリティ**: aria-labelで発言者を明示
- **レイアウト**: CSS Gridで柔軟な配置
- **三角形**: 二重の疑似要素でボーダー付き吹き出しを実現

### デザイン詳細
- 名前はアイコン下に配置（絶対配置）
- 吹き出しはアイコンセンターに揃う
- テキストが長い場合は吹き出しのみ下方向に伸びる
- `overflow-wrap: anywhere`で長いURLも適切に折り返し

## リダイレクト設定（2025年8月実装）

### 添付ファイルページのリダイレクト
- 添付ファイルページ（画像単体ページ）を親記事へ301リダイレクト
- 親記事がない場合はトップページへリダイレクト
- SEO的に無意味なページのインデックスを防止

## Contact Form 7最適化（2025年8月実装）

### 概要
Contact Form 7のアセット（CSS/JavaScript）をフォームが存在するページのみで読み込むように最適化。

### 実装内容
1. **デフォルト無効化**
   - `wpcf7_load_js`と`wpcf7_load_css`フィルターで全ページでの読み込みを停止
   
2. **条件付き読み込み**
   - フロントページ（トップページ）でのみCF7のアセットを読み込み
   - フォーム位置：`https://segawa-haru.jp/#contact`
   
3. **reCAPTCHA制御**
   - フロントページ以外では`google-recaptcha`と`wpcf7-recaptcha`スクリプトを除外
   - 優先度100で確実に除外

### 効果
- ブログ記事、ギャラリー、固定ページなどでCF7関連ファイルが一切読み込まれない
- ページ読み込み速度の向上
- ネットワークリクエストの削減

### 将来の拡張
現在はシンプル版（`is_front_page()`判定）で実装。将来フォームを他のページに追加する場合は、自動検出版への切り替えが必要：

```php
// 自動検出版：ショートコードまたはブロックの存在を判定
function haru_cf7_should_load(): bool {
    if ( ! is_singular() ) return false;
    $id = get_queried_object_id();
    $has_sc = has_shortcode(get_post_field('post_content', $id), 'contact-form-7');
    $has_block = function_exists('has_block') && has_block('contact-form-7/contact-form-selector', $id);
    return $has_sc || $has_block;
}
```

この関数を使って`is_front_page()`の代わりに`haru_cf7_should_load()`で判定すれば、フォームがあるページでのみ自動的にCF7アセットが読み込まれる。