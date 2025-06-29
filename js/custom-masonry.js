document.addEventListener('DOMContentLoaded', function () {
  console.log('Masonry script loaded');
  // メイソンリーレイアウトを適用するコンテナ要素を取得
  var elem = document.querySelector('.haru-masonry-gallery');
  console.log('Found masonry container:', elem);
  if (elem) {
    // まず、elem（.haru-masonry-gallery）の中の画像が全部読み込まれるのを待つ
    imagesLoaded( elem, function() {
      // 画像が全部読み込まれたら、ここからMasonryの処理を始める！
      var msnry = new Masonry( elem, {
        // オプション
        itemSelector: '.haru-masonry-gallery-item, .haru-masonry-gallery-item--wide',
        columnWidth: '.haru-grid-sizer',
        gutter: (function() {
          // レスポンシブ対応：現在の画面幅に応じて適切なgutter値を取得
          const screenWidth = window.innerWidth;
          let gutterVar;
          
          if (screenWidth >= 1131) {
            gutterVar = '--gallery-gutter'; // 12px
          } else if (screenWidth >= 718) {
            gutterVar = '--gallery-gutter-tablet'; // 10px
          } else {
            gutterVar = '--gallery-gutter-mobile'; // 8px
          }
          
          const gutterValue = getComputedStyle(document.documentElement)
            .getPropertyValue(gutterVar).trim();
          
          const finalGutter = parseInt(gutterValue) || 12;
          console.log('Masonry init - Screen width:', screenWidth, 'Using gutter var:', gutterVar, 'Final gutter:', finalGutter);
          return finalGutter;
        })(),
        percentPosition: true,
        transitionDuration: 0
      });

      // ResizeObserver を使用して要素のサイズ変更を監視
      if (window.ResizeObserver) {
        const resizeObserver = new ResizeObserver(function() {
          clearTimeout(elem.resizeTimeoutId); // デバウンス処理用のタイマーIDを要素に紐付ける
          elem.resizeTimeoutId = setTimeout(function() {
            console.log('ResizeObserver triggered layout - Screen width:', screenWidth, 'Gutter:', newGutterValue); // デバッグ用
            
            // レスポンシブ対応：リサイズ時にgutter値を再取得
            const screenWidth = window.innerWidth;
            let gutterVar;
            
            if (screenWidth >= 1131) {
              gutterVar = '--gallery-gutter'; // 12px
            } else if (screenWidth >= 718) {
              gutterVar = '--gallery-gutter-tablet'; // 10px
            } else {
              gutterVar = '--gallery-gutter-mobile'; // 8px
            }
            
            const newGutterValue = parseInt(
              getComputedStyle(document.documentElement)
                .getPropertyValue(gutterVar).trim()
            ) || 12;
            
            // gutter値が変更された場合はMasonryを再初期化
            if (msnry.options.gutter !== newGutterValue) {
              msnry.options.gutter = newGutterValue;
              msnry.destroy();
              msnry = new Masonry(elem, {
                itemSelector: '.haru-masonry-gallery-item, .haru-masonry-gallery-item--wide',
                columnWidth: '.haru-grid-sizer',
                gutter: newGutterValue,
                percentPosition: true,
                transitionDuration: 0
              });
            } else {
              msnry.layout();
            }
          }, 250); // 250msのデバウンス
        });
        resizeObserver.observe(elem);
      }
    });
  }
}); 