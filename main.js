function openCreateModal() {
    document.getElementById('createModal').style.display = 'flex';
}

function closeCreateModal() {
    document.getElementById('createModal').style.display = 'none';
}

// Fermer le modal en cliquant dehors
window.onclick = function(event) {
    const modal = document.getElementById('createModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}