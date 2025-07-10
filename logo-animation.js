// ロゴアニメーション
document.addEventListener('DOMContentLoaded', function() {
  const logo = document.querySelector('.haru-main-logo');
  if (logo) {
    // 少し遅延してアニメーション開始
    setTimeout(() => {
      logo.classList.add('is-animated');
    }, 100);
  }
});