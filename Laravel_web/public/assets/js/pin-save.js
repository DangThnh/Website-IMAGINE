document.addEventListener('DOMContentLoaded', function() {
    // Hiển thị nút "Lưu" khi hover vào pin
    const pinItems = document.querySelectorAll('.pin-item');
    
    pinItems.forEach(pin => {
        pin.addEventListener('mouseenter', function() {
            this.querySelector('.pin-save-button').classList.remove('d-none');
        });
        
        pin.addEventListener('mouseleave', function() {
            this.querySelector('.pin-save-button').classList.add('d-none');
        });
    });
    
    // Xử lý form trong modal
    const saveForm = document.getElementById('pin-save-form');
    const newCollectionToggle = document.getElementById('new-collection-toggle');
    const existingCollectionContainer = document.getElementById('existing-collection-container');
    const newCollectionContainer = document.getElementById('new-collection-container');
    
    if (newCollectionToggle) {
        newCollectionToggle.addEventListener('click', function(e) {
            e.preventDefault();
            existingCollectionContainer.classList.toggle('d-none');
            newCollectionContainer.classList.toggle('d-none');
            
            // Reset form
            if (existingCollectionContainer.classList.contains('d-none')) {
                document.getElementById('collection_id').value = '';
            } else {
                document.getElementById('new_collection').value = '';
            }
        });
    }
    
    // Xử lý lưu pin vào collection với AJAX
    if (saveForm) {
        saveForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(this.getAttribute('action'), {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hiển thị thông báo thành công
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success';
                    alert.textContent = data.message;
                    document.getElementById('save-result').innerHTML = '';
                    document.getElementById('save-result').appendChild(alert);
                    
                    // Đóng modal sau 1 giây
                    setTimeout(() => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('saveModal'));
                        modal.hide();
                    }, 1000);
                } else {
                    // Hiển thị thông báo lỗi
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-danger';
                    alert.textContent = data.message;
                    document.getElementById('save-result').innerHTML = '';
                    document.getElementById('save-result').appendChild(alert);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    }
});
