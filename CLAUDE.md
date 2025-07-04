# プロジェクト情報

## 基本情報
- **サイト**: https://segawa-haru.jp/
- **テーマ**: TT5（Twenty Twenty-Five）公式テーマ
  - 元々はSwellテーマから変更
- **デプロイ**: WP pusherプラグインでGitHubから自動反映

## 開発方針
- **保守性を重視**してシンプルなコードを書く
- **モバイルファースト**で開発
- **WordPressブロックエディタ**で設定できることは、そちらを優先
- エディタでの作業が必要な場合は提案する
- **単位の使い分け**: rem（文字・余白）、px（レイアウト・隙間）を適切に使い分け

## CSS変数
```css
:root {
  --haru-element-gap: 1px;           /* 要素間の隙間 */
  --haru-element-border-radius: 20px; /* 要素の角丸 */
}
```

## 実装済み機能

### サービスカード（haru-service-card）
- **構造**: 
  ```
  .haru-service-cards-container（グループ、グリッド）
  └── .haru-service-card（グループ）
      ├── 画像
      ├── 見出し（H3）
      └── 段落
  ```
- **レスポンシブ**: WordPressのグリッドブロックで最小列幅をrem設定
- **最大列数制限**: 1800px以上で5列固定（CSS）
- **アニメーション**: ホバー時に0.95倍に縮小
- **パディング**: `clamp(0.75rem, 2vw + 0.5rem, 1.5rem)`
- **H3スタイル**: 
  - フォントサイズ: `clamp(1rem, 4vw, 1.25rem)`
  - 余白: 上下1rem
  - 太字: 700

### レスポンシブスペーサー（haru-spacer-large）
- **サイズ**: `clamp(1.5rem, 5vw, 4rem) !important`
- **用途**: WordPressスペーサーブロックに追加CSSクラスとして使用
- **!important理由**: element.styleを上書きするため

### 縦型ヘッダー（haru-vertical-header）
- **幅**: `clamp(1.5rem, 4vw, 2.5rem)`
- **配置**: `margin-left: auto`で右寄せ
- **テキスト**: `writing-mode: vertical-rl`, `text-orientation: sideways`

## 現在の課題
- **縦ヘッダーのカラム幅問題**: `.wp-block-column`の幅設定により隣のコンテンツとの余白が大きすぎる
- **解決方法検討中**: 
  - `flex: 0 0 auto`で既存widthコードを活かす方向
  - または`flex-basis`でサイズ制御

## 注意点
- 文字スタイルは基本的にWordPressエディタで設定
- シャドウなどの装飾は控えめに
- CSS変数は複数箇所で使用する場合のみ作成