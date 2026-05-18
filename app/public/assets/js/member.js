function performSearch() {
    let query = document.getElementById('searchBar').value;
    let category = document.getElementById('categoryFilter').value;

    fetch(`app/controllers/searchController.php?q=${encodeURIComponent(query)}&category=${encodeURIComponent(category)}`)
    .then(response => response.json())
    .then(data => {
        let grid = document.getElementById('contentGrid');
        grid.innerHTML = ''; 

        if (data.status === 'success' && data.data.length > 0) {
            data.data.forEach(item => {
                
                grid.innerHTML += `
                    <div class="registrationFormCard" style="width: 300px; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <h3 style="color: white; margin-top: 0;">${item.title}</h3>
                            <p style="margin-bottom: 5px;"><strong>Category:</strong> ${item.category_name || 'Uncategorized'}</p>
                            <p style="margin-bottom: 10px;">${item.description}</p>
                            <p style="margin-bottom: 15px;"><small>Downloads: ${item.download_count}</small></p>
                        </div>
                        
                        <a href="app/public/uploads/contents/${item.file_path}" target="_blank" style="text-decoration: none;">
                            <input type="button" value="Download File" style="width: 100%;" />
                        </a>
                    </div>
                `;
            });
        } else {
            grid.innerHTML = '<p>No contents found matching your search.</p>';
        }
    });
}

function submitRequest() {
    let title = document.getElementById('req_title').value;
    let category = document.getElementById('req_category').value;
    let message = document.getElementById('req_message').value;

    document.getElementById('err-title').innerText = '';
    document.getElementById('err-category').innerText = '';
    document.getElementById('sys-msg').innerText = '';

    if (title.trim() === '') {
        document.getElementById('err-title').innerText = 'Title is required'; return;
    }
    if (category.trim() === '') {
        document.getElementById('err-category').innerText = 'Category is required'; return;
    }

    let formData = new URLSearchParams();
    formData.append('title', title);
    formData.append('category', category);
    formData.append('message', message);

    fetch('app/controllers/requestController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: formData.toString()
    })
    .then(response => response.json())
    .then(data => {
        let sysMsg = document.getElementById('sys-msg');
        if (data.status === 'success') {
            sysMsg.innerHTML = `<span style="color: #2ecc71;">${data.msg}</span>`;
            document.getElementById('req_title').value = '';
            document.getElementById('req_category').value = '';
            document.getElementById('req_message').value = '';
        } else {
            sysMsg.innerHTML = `<span style="color: #e74c3c;">${data.msg}</span>`;
            if(data.errors) {
                if(data.errors.title) document.getElementById('err-title').innerText = data.errors.title;
                if(data.errors.category) document.getElementById('err-category').innerText = data.errors.category;
            }
        }
    });
}