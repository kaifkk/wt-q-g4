function performSearch() {
    let query    = document.getElementById('searchBar').value;
    let category = document.getElementById('categoryFilter').value;

    fetch(`controllers/searchController.php?q=${encodeURIComponent(query)}&category=${encodeURIComponent(category)}`)
    .then(response => response.json())
    .then(data => {
        let grid         = document.getElementById('contentGrid');
        let sectionTitle = document.getElementById('contentSectionTitle');
        grid.innerHTML   = '';

        if (data.mode === 'highlighted') {
            sectionTitle.textContent = 'Most Downloaded';
        } else {
            sectionTitle.textContent = `Search Results for "${query}"`;
        }

        if (data.status === 'success' && data.data.length > 0) {
            data.data.forEach(item => {
                let title       = document.createElement('div');
                let safeTitle   = document.createTextNode(item.title);
                let safeDesc    = document.createTextNode(item.description || '');
                let safeCat     = document.createTextNode(item.category_name || 'Uncategorized');
                let safeCount   = document.createTextNode(item.download_count);

                let card = document.createElement('div');
                card.className = 'registrationFormCard';
                card.style.cssText = 'width:300px; display:flex; flex-direction:column; justify-content:space-between;';

                let body = document.createElement('div');

                let h3 = document.createElement('h3');
                h3.style.cssText = 'color:white; margin-top:0;';
                h3.appendChild(safeTitle);

                let catP = document.createElement('p');
                catP.style.marginBottom = '5px';
                catP.innerHTML = '<strong>Category:</strong> ';
                catP.appendChild(safeCat);

                let descP = document.createElement('p');
                descP.style.marginBottom = '10px';
                descP.appendChild(safeDesc);

                let dlP = document.createElement('p');
                dlP.style.marginBottom = '15px';
                let small = document.createElement('small');
                small.appendChild(document.createTextNode('Downloads: '));
                small.appendChild(safeCount);
                dlP.appendChild(small);

                body.appendChild(h3);
                body.appendChild(catP);
                body.appendChild(descP);
                body.appendChild(dlP);

                let a = document.createElement('a');
                a.href = `public/uploads/contents/${item.file_path}`;
                a.target = '_blank';
                a.style.textDecoration = 'none';

                let btn = document.createElement('input');
                btn.type = 'button';
                btn.value = 'Download File';
                btn.style.width = '100%';

                a.appendChild(btn);
                card.appendChild(body);
                card.appendChild(a);
                grid.appendChild(card);
            });
        } else {
            grid.innerHTML = data.mode === 'highlighted'
                ? '<p>No content available yet.</p>'
                : '<p>No results found for your search.</p>';
        }
    });
}

function submitRequest() {
    let title    = document.getElementById('req_title').value;
    let category = document.getElementById('req_category').value;
    let message  = document.getElementById('req_message').value;

    document.getElementById('err-title').innerText    = '';
    document.getElementById('err-category').innerText = '';
    document.getElementById('sys-msg').innerText      = '';

    if (title.trim() === '') {
        document.getElementById('err-title').innerText = 'Title is required'; return;
    }
    if (category.trim() === '') {
        document.getElementById('err-category').innerText = 'Category is required'; return;
    }

    let formData = new URLSearchParams();
    formData.append('title',    title);
    formData.append('category', category);
    formData.append('message',  message);

    fetch('controllers/requestController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: formData.toString()
    })
    .then(response => response.json())
    .then(data => {
        let sysMsg = document.getElementById('sys-msg');
        if (data.status === 'success') {
            sysMsg.innerHTML = `<span style="color:#2ecc71;">${data.msg}</span>`;
            document.getElementById('req_title').value    = '';
            document.getElementById('req_category').value = '';
            document.getElementById('req_message').value  = '';
        } else {
            sysMsg.innerHTML = `<span style="color:#e74c3c;">${data.msg}</span>`;
            if (data.errors) {
                if (data.errors.title)    document.getElementById('err-title').innerText    = data.errors.title;
                if (data.errors.category) document.getElementById('err-category').innerText = data.errors.category;
            }
        }
    });
}