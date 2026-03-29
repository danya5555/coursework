// Лайки
document.querySelectorAll('.like-btn').forEach(btn => {
    btn.addEventListener('click', async function() {
        const postId = this.dataset.id;
        const response = await fetch('handlers/like.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ post_id: postId })
        });
        const result = await response.json();
        if (result.success) {
            const likesSpan = this.querySelector('.likes-count');
            likesSpan.textContent = result.likes;
        } else {
            alert(result.message);
        }
    });
});

// Комментарии
document.querySelectorAll('.comment-form').forEach(form => {
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        const postId = this.dataset.id;
        const textarea = this.querySelector('textarea');
        const comment = textarea.value.trim();
        if (!comment) return;

        const response = await fetch('handlers/comment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ post_id: postId, comment: comment })
        });
        const result = await response.json();
        if (result.success) {
            location.reload(); // простая перезагрузка для обновления комментариев
        } else {
            alert(result.message);
        }
    });
});

// Удаление поста (только админ)
document.querySelectorAll('.delete-post').forEach(btn => {
    btn.addEventListener('click', async function() {
        if (!confirm('Удалить этот пост?')) return;
        const postId = this.dataset.id;
        const response = await fetch('handlers/delete_post.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ post_id: postId })
        });
        const result = await response.json();
        if (result.success) {
            this.closest('.post').remove();
        } else {
            alert(result.message);
        }
    });
});