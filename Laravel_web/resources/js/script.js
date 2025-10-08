// public/js/script.js
function openPopup() {
    document.getElementById('popupOverlay').style.display = 'block';
    fetchBoards();
}
function closePopup() {
    document.getElementById('popupOverlay').style.display = 'none';
}
function fetchBoards() {
    fetch('/api/boards')
        .then(response => response.json())
        .then(data => {
            let list = document.getElementById('boardList');
            list.innerHTML = '';
            data.forEach(board => {
                let li = document.createElement('li');
                li.innerHTML = `<button onclick="saveImage(${board.id})">${board.name}</button>`;
                list.appendChild(li);
            });
        });
}
function createBoard() {
    let name = document.getElementById('newBoard').value;
    fetch('/api/boards', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name })
    }).then(() => fetchBoards());
}
function saveImage(boardId) {
    fetch(`/api/boards/${boardId}/add-image`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ image_id: 123 })
    }).then(() => alert('Ảnh đã lưu!'));
}
