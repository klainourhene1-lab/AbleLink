// Configuration de l'API
const API_URL = 'api';
let currentStoryId = null;
let editingStoryId = null;

// Charger les stories au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    loadStories();
});

// Charger toutes les stories
async function loadStories() {
    try {
        const response = await fetch(`${API_URL}/success_stories.php`);
        const stories = await response.json();

        const container = document.getElementById('storiesContainer');
        
        if (stories.length === 0) {
            container.innerHTML = `
                <div class="col-lg-12">
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-story"></i>
                        </div>
                        <h3>Aucune histoire pour le moment</h3>
                        <p>Soyez le premier à partager votre success story!</p>
                    </div>
                </div>
            `;
        } else {
            container.innerHTML = stories.map(story => `
                <div class="col-lg-4 col-md-6">
                    <div class="story-card">
                        <img src="${story.image || 'img/default.jpg'}" alt="${story.title}" class="story-image">
                        <div class="story-content">
                            <div class="story-header">
                                <span class="story-category">${story.category}</span>
                            </div>
                            <h3 class="story-title">${story.title}</h3>
                            <div class="story-meta">
                                <span><i class="fas fa-user"></i> ${story.author}</span>
                                <span><i class="fas fa-calendar"></i> ${formatDate(story.created_at)}</span>
                            </div>
                            <p class="story-description">${truncateText(story.description, 150)}</p>
                            <div class="story-actions">
                                <button class="btn-action btn-like" onclick="toggleLike(${story.id})">
                                    <i class="fas fa-heart"></i> ${story.likes}
                                </button>
                                <button class="btn-action btn-comment" onclick="openCommentsModal(${story.id}, '${story.title}')">
                                    <i class="fas fa-comment"></i> Commentaires
                                </button>
                                <button class="btn-action btn-edit" onclick="openEditStoryModal(${story.id})">
                                    <i class="fas fa-edit"></i> Modifier
                                </button>
                                <button class="btn-action btn-delete" onclick="deleteStory(${story.id})">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }
    } catch (error) {
        console.error('Erreur lors du chargement des stories:', error);
    }
}

// Ouvrir modal pour ajouter une story
function openAddStoryModal() {
    editingStoryId = null;
    document.getElementById('storyModalTitle').textContent = 'Partager une Success Story';
    document.getElementById('storyForm').reset();
    document.getElementById('storyModal').style.display = 'block';
}

// Ouvrir modal pour modifier une story
async function openEditStoryModal(storyId) {
    try {
        const response = await fetch(`${API_URL}/success_stories.php/${storyId}`);
        const story = await response.json();

        if (story.id) {
            editingStoryId = storyId;
            document.getElementById('storyModalTitle').textContent = 'Modifier la Success Story';
            document.getElementById('storyTitle').value = story.title;
            document.getElementById('storyAuthor').value = story.author;
            document.getElementById('storyCategory').value = story.category;
            document.getElementById('storyDescription').value = story.description;
            document.getElementById('storyImage').value = story.image || '';
            document.getElementById('storyModal').style.display = 'block';
        }
    } catch (error) {
        console.error('Erreur lors du chargement de la story:', error);
    }
}

// Soumettre le formulaire de story
async function handleSubmitStory(event) {
    event.preventDefault();

    const storyData = {
        title: document.getElementById('storyTitle').value,
        author: document.getElementById('storyAuthor').value,
        category: document.getElementById('storyCategory').value,
        description: document.getElementById('storyDescription').value,
        image: document.getElementById('storyImage').value || 'img/default.jpg'
    };

    try {
        let response;
        
        if (editingStoryId) {
            // Modifier une story existante
            response = await fetch(`${API_URL}/success_stories.php/${editingStoryId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(storyData)
            });
        } else {
            // Créer une nouvelle story
            response = await fetch(`${API_URL}/success_stories.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(storyData)
            });
        }

        const result = await response.json();
        closeModal('storyModal');
        loadStories();
        showNotification(result.message || 'Opération réussie!');
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Une erreur est survenue', 'error');
    }
}

// Supprimer une story
async function deleteStory(storyId) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette story?')) return;

    try {
        const response = await fetch(`${API_URL}/success_stories.php/${storyId}`, {
            method: 'DELETE'
        });

        const result = await response.json();
        loadStories();
        showNotification(result.message || 'Story supprimée avec succès!');
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Une erreur est survenue', 'error');
    }
}

// Toggle Like sur une story
async function toggleLike(storyId) {
    try {
        const response = await fetch(`${API_URL}/success_stories.php/${storyId}/like`, {
            method: 'POST'
        });

        const result = await response.json();
        loadStories();
    } catch (error) {
        console.error('Erreur lors du like:', error);
    }
}

// Ouvrir le modal des commentaires
async function openCommentsModal(storyId, storyTitle) {
    currentStoryId = storyId;
    document.getElementById('commentsModal').style.display = 'block';
    await loadComments(storyId);
}

// Charger les commentaires
async function loadComments(storyId) {
    try {
        const response = await fetch(`${API_URL}/comments.php?story_id=${storyId}`);
        const comments = await response.json();

        const commentsList = document.getElementById('commentsList');
        
        if (comments.length === 0) {
            commentsList.innerHTML = `
                <div style="text-align: center; padding: 30px; color: #999;">
                    <p>Aucun commentaire pour le moment. Soyez le premier!</p>
                </div>
            `;
        } else {
            commentsList.innerHTML = comments.map(comment => `
                <div class="comment-item">
                    <div class="comment-author">${comment.author}</div>
                    <div class="comment-text">${comment.comment_text}</div>
                    <div class="comment-meta">
                        <span>${formatDate(comment.created_at)}</span>
                        <div class="comment-actions">
                            <button class="comment-like-btn" onclick="toggleCommentLike(${comment.id})">
                                <i class="fas fa-heart"></i> ${comment.likes}
                            </button>
                            <button class="comment-delete-btn" onclick="deleteComment(${comment.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }
    } catch (error) {
        console.error('Erreur lors du chargement des commentaires:', error);
    }
}

// Ajouter un commentaire
async function handleAddComment(event) {
    event.preventDefault();

    const commentData = {
        story_id: currentStoryId,
        author: document.getElementById('commentAuthor').value,
        comment_text: document.getElementById('commentText').value
    };

    try {
        const response = await fetch(`${API_URL}/comments.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(commentData)
        });

        const result = await response.json();
        document.getElementById('commentAuthor').value = '';
        document.getElementById('commentText').value = '';
        await loadComments(currentStoryId);
        showNotification('Commentaire publié avec succès!');
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Une erreur est survenue', 'error');
    }
}

// Toggle Like sur un commentaire
async function toggleCommentLike(commentId) {
    try {
        const response = await fetch(`${API_URL}/comments.php/${commentId}/like`, {
            method: 'POST'
        });

        const result = await response.json();
        await loadComments(currentStoryId);
    } catch (error) {
        console.error('Erreur lors du like:', error);
    }
}

// Supprimer un commentaire
async function deleteComment(commentId) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer ce commentaire?')) return;

    try {
        const response = await fetch(`${API_URL}/comments.php/${commentId}`, {
            method: 'DELETE'
        });

        const result = await response.json();
        await loadComments(currentStoryId);
        showNotification('Commentaire supprimé avec succès!');
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Une erreur est survenue', 'error');
    }
}

// Fermer un modal
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Fermer les modals quand on clique en dehors
window.onclick = function(event) {
    const storyModal = document.getElementById('storyModal');
    const commentsModal = document.getElementById('commentsModal');

    if (event.target === storyModal) {
        storyModal.style.display = 'none';
    }
    if (event.target === commentsModal) {
        commentsModal.style.display = 'none';
    }
}

// Utilitaires
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('fr-FR', options);
}

function truncateText(text, length) {
    if (text.length > length) {
        return text.substring(0, length) + '...';
    }
    return text;
}

function showNotification(message, type = 'success') {
    // Créer une notification simple
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background-color: ${type === 'success' ? '#28a745' : '#dc3545'};
        color: white;
        border-radius: 5px;
        z-index: 9999;
        animation: slideIn 0.3s ease-in-out;
    `;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-in-out';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Ajouter les animations CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
