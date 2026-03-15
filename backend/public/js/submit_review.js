// レビュー投稿画面のJavaScript

// 星評価システム
function initStarRating(containerId, inputId, labelId) {
  const container = document.getElementById(containerId);
  const stars = container.querySelectorAll('.star');
  const input = document.getElementById(inputId);
  const label = document.getElementById(labelId);
  let selectedRating = parseInt(input.value) || 0;

  const ratingLabels = {
    1: '★☆☆☆☆ 不満',
    2: '★★☆☆☆ やや不満',
    3: '★★★☆☆ 普通',
    4: '★★★★☆ 満足',
    5: '★★★★★ 大変満足',
  };

  // 初期値がある場合は表示
  if (selectedRating > 0) {
    updateStars(stars, selectedRating);
    label.textContent = ratingLabels[selectedRating];
    label.style.color = '#2e7d32';
    label.style.fontWeight = '600';
  }

  stars.forEach((star) => {
    star.addEventListener('click', function () {
      selectedRating = parseInt(this.getAttribute('data-rating'));
      input.value = selectedRating;
      updateStars(stars, selectedRating);
      label.textContent = ratingLabels[selectedRating];
      label.style.color = '#2e7d32';
      label.style.fontWeight = '600';
    });

    star.addEventListener('mouseenter', function () {
      const hoverRating = parseInt(this.getAttribute('data-rating'));
      updateStars(stars, hoverRating);
    });
  });

  container.addEventListener('mouseleave', function () {
    updateStars(stars, selectedRating);
  });
}

function updateStars(stars, rating) {
  stars.forEach((star, index) => {
    if (index < rating) {
      star.classList.add('active');
    } else {
      star.classList.remove('active');
    }
  });
}

// 文字数カウント
function initCharCount(textareaId, countId) {
  const textarea = document.getElementById(textareaId);
  const counter = document.getElementById(countId);

  // 初期値のカウント
  counter.textContent = `${textarea.value.length} / 500`;

  textarea.addEventListener('input', function () {
    counter.textContent = `${this.value.length} / 500`;
  });
}

// フォーム送信時のバリデーション
function initFormValidation() {
  document.querySelector('form').addEventListener('submit', function (e) {
    const landRating = document.getElementById('land-rating-value').value;
    const ownerRating = document.getElementById('owner-rating-value').value;

    if (!landRating || !ownerRating) {
      e.preventDefault();
      alert('評価を選択してください。両方の星評価が必須です。');
      return false;
    }
  });
}

// DOMContentLoaded時の初期化
document.addEventListener('DOMContentLoaded', function () {
  // 土地と貸し手の星評価を初期化
  initStarRating('land-rating', 'land-rating-value', 'land-rating-label');
  initStarRating('owner-rating', 'owner-rating-value', 'owner-rating-label');

  // 文字数カウントを初期化
  initCharCount('land-comment', 'land-char-count');
  initCharCount('owner-comment', 'owner-char-count');

  // フォームバリデーションを初期化
  initFormValidation();
});
