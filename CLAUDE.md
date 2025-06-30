# CLAUDE.md

このファイルは、このリポジトリでコードを扱う際のClaude Code (claude.ai/code) 向けのガイダンスです。

## プロジェクト概要

これは「TT5 Child」(segawa-haru-jp-theme-tt5) という名前のTwenty Twenty-Five用WordPressチャイルドテーマです。日本語サイト向けに設計されており、カスタムグリーンカラースキーム（#00d782 "Haru Green"）とAdobe Fonts（源ノ角ゴシック）を使った日本語タイポグラフィが特徴です。

## アーキテクチャ

### コアファイル構成
- `functions.php` - アセット読み込み（CSS、Adobe Fonts/Typekit統合）
- `style.css` - レスポンシブパディングユーティリティとビジュアル/サービス幅システムを含むチャイルドテーマCSS
- `theme.json` - WordPressテーマ設定（カラー、タイポグラフィ、レイアウト設定）
- `js/` - Masonryギャラリー機能を含むJavaScriptディレクトリ

### 主要デザインシステム

**レスポンシブ幅システム:**
- CSSカスタムプロパティで定義された2つの幅システム:
  - ビジュアル幅: `--haru-padding-visual` (15px-50pxレスポンシブ)
  - サービス幅: `--haru-padding-service` (1000pxブレークポイント以降で自動拡張、最大188px)
- ユーティリティクラス: `.u-px-visual`, `.u-px-service`

**カラーパレット:**
- プライマリ: Haru Green (#00d782)
- テキスト: Black (#000000)
- 背景: White/Gray (#ffffff, #e7e7e7)

**タイポグラフィ:**
- フォントファミリ: 源ノ角ゴシック (source-han-sans-japanese) Adobe Typekit経由
- フォントサイズ: SS (0.8125rem), S (0.875rem), M (0.9375rem)

### JavaScriptコンポーネント

**Masonryギャラリー (`js/custom-masonry.js`):**
- Masonry.jsとimagesLoadedライブラリを使用
- `.haru-masonry-gallery`コンテナに適用
- アイテムセレクタ: `.haru-masonry-gallery-item`, `.haru-masonry-gallery-item--wide`
- ResizeObserverとデバウンスされたレイアウト更新によるレスポンシブ対応
- グリッドサイザー: `.haru-grid-sizer`
- ガターはCSSカスタムプロパティ `--gallery-gutter` で制御

## 開発コマンド

これはビルドプロセスのないWordPressチャイルドテーマです。開発は直接ファイル編集で行います。

**デプロイ方式:** WP Pusherプラグインを使用してGitHubからWordPressサイトに自動反映。変更を確認するには必ずGitHubにプッシュする必要がある。

**ファイル監視:** 自動ビルドシステムなし - CSS/JSの変更には手動でブラウザリフレッシュが必要です。

**アセット管理:** CSSキャッシュバスティングは `functions.php:14` の `filemtime()` で自動処理されます。

## 実装済み機能

### カスタム縦型サイドヘッダー
- WordPressパターン: `haru-vertical-header`
- 右側固定位置にグラデーション背景
- 回転テキストナビゲーション
- レスポンシブ対応（クラス: `.haru-vertical-header`）

### レスポンシブサービスカード
- シンプルなCSS Gridレイアウト
- 3列（デフォルト）→ 2列（1130px以下）→ 1列（717px以下）
- ホバーエフェクト付き（scale 0.95）
- クラス: `.haru-service-cards-container`, `.haru-service-card`

### プロフィールカード（3Dフリップ）
- クリック/キーボードでフリップ機能
- モバイル（3:5）とPC（5:3）でレスポンシブレイアウト
- アクセシビリティ対応（tabindex, role, aria-label）
- クラス: `.haru-profile-card`, `.haru-profile-card__front`, `.haru-profile-card__back`

### Masonryギャラリー
- 既存のMasonry.jsとimagesLoadedライブラリ使用
- メディアクエリでレスポンシブ列数制御（3列→2列→1列）
- ホバーエフェクト付き
- クラス: `.haru-masonry-gallery`, `.haru-masonry-gallery-item`
- **理想的なレイアウト:** https://marph.com/ の `<section class="oct-section">` 部分を参考
- 左右パディングは`--haru-padding-visual`を保持、上下左右は隙間なく積まれる
- `haru-masonry-gallery-item--wide`は2列分の幅

### メッセージフォーム
- Contact Form 7カスタムスタイリング
- フォーカス状態の視覚的フィードバック
- レスポンシブ余白システム
- クラス: `.haru-message-form-container`, `.wpcf7-form`

## CSS設計パターン

**名前空間保護（haru-プレフィックス）:**
- 全カスタムクラス名に `haru-` プレフィックス付与
- WordPress/プラグインとの競合を防ぐための識別
- 例: `.service-card` → `.haru-service-card`
- CSS変数は既に `--haru-` プレフィックス使用済み

**CSS変数システム:**
- カラーパレット: `--wp--preset--color--*` (theme.json管理)
- レスポンシブ幅: `--haru-padding-visual`, `--haru-padding-service`
- コンポーネント固有変数: `--card-border-radius`, `--gallery-gutter`

**レスポンシブ制御:**
- シンプルなメディアクエリによるレイアウト制御
- 理想のイメージに合わせた必要十分な設計

**アクセシビリティ:**
- フォーカス表示の統一（`:focus-visible`）
- スクリーンリーダー対応（`.sr-only`）
- キーボードナビゲーション対応

## WordPress統合

- 親テーマ: Twenty Twenty-Five
- Adobe Fonts統合 Typekit経由（ID: bew5wgt）
- カスタムカラーパレット無効化（theme.jsonで `"custom": false`）
- コンテンツ幅: 700px
- フォントウェイト: 400 (normal)