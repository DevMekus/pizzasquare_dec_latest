 <script>
        // AOS.init();
        let DATA = {
            menu: [{
                    name: 'Margherita Pizza',
                    category: 'Pizza',
                    price: 8.99,
                    stock: 12,
                    image: 'https://via.placeholder.com/300x140?text=Pizza',
                    rating: 4.5,
                    status: 'Available'
                },
                {
                    name: 'Cheeseburger',
                    category: 'Burgers',
                    price: 7.49,
                    stock: 5,
                    image: 'https://via.placeholder.com/300x140?text=Burger',
                    rating: 4.3,
                    status: 'Available'
                }
            ],
            categories: ['Pizza', 'Burgers']
        };
        const menuGrid = document.getElementById('menuGrid');
        const menuTableBody = document.getElementById('menuTableBody');
        const pagination = document.getElementById('pagination');
        let currentCategory = 'all',
            currentPage = 1,
            pageSize = 4,
            cardView = true,
            editIndex = null;

        function renderCategories() {
            const tabs = document.getElementById('categoryTabs');
            tabs.innerHTML = `<button class="active" data-category="all">All</button>`;
            DATA.categories.forEach(cat => {
                const btn = document.createElement('button');
                btn.textContent = cat;
                btn.dataset.category = cat;
                tabs.appendChild(btn);
            });
            tabs.querySelectorAll('button').forEach(tab => {
                tab.onclick = () => {
                    tabs.querySelectorAll('button').forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');
                    currentCategory = tab.dataset.category;
                    currentPage = 1;
                    renderMenu();
                };
            });
            updateCategorySelect();
        }

        function renderMenu() {
            menuGrid.innerHTML = '';
            menuTableBody.innerHTML = '';
            let filtered = currentCategory === 'all' ? DATA.menu : DATA.menu.filter(i => i.category === currentCategory);
            const totalPages = Math.ceil(filtered.length / pageSize);
            const items = filtered.slice((currentPage - 1) * pageSize, currentPage * pageSize);
            if (cardView) {
                items.forEach((item, i) => {
                    const idx = DATA.menu.indexOf(item);
                    const card = document.createElement('div');
                    card.className = 'menu-card';
                    card.innerHTML = `
    <img src="${item.image}" alt="${item.name}">
    <div class="content"><h3>${item.name} <span class="badge">${item.category}</span></h3><p class="price">$${item.price.toFixed(2)}</p><p>Status: <span class="badge">${item.status}</span></p><p>⭐ ${item.rating}</p></div>
    <footer><span>Stock: ${item.stock}</span><div class="actions"><i class="fa fa-edit" onclick="openDishModal(${idx})"></i><i class="fa fa-trash" onclick="deleteDish(${idx})"></i><i class="fa fa-chart-line" onclick="alert('Analytics for ${item.name}')"></i></div></footer>`;
                    menuGrid.appendChild(card);
                });
            } else {
                items.forEach((item) => {
                    const idx = DATA.menu.indexOf(item);
                    const row = document.createElement('tr');
                    row.innerHTML = `<td>${item.name}</td><td>${item.category}</td><td>$${item.price}</td><td>${item.stock}</td><td>${item.status}</td><td>${item.rating}</td><td><i class="fa fa-edit" onclick="openDishModal(${idx})"></i> <i class="fa fa-trash" onclick="deleteDish(${idx})"></i></td>`;
                    menuTableBody.appendChild(row);
                });
            }
            pagination.innerHTML = '';
            for (let p = 1; p <= totalPages; p++) {
                const b = document.createElement('button');
                b.textContent = p;
                if (p === currentPage) b.classList.add('active');
                b.onclick = () => {
                    currentPage = p;
                    renderMenu();
                };
                pagination.appendChild(b);
            }
            updateKpis();
            // AOS.refresh();
        }

        function updateKpis() {
            document.getElementById('totalItems').textContent = DATA.menu.length;
            document.getElementById('availableItems').textContent = DATA.menu.filter(i => i.status === 'Available').length;
            document.getElementById('outOfStock').textContent = DATA.menu.filter(i => i.status === 'Out of Stock').length;
            if (DATA.menu.length > 0) document.getElementById('popularDish').textContent = DATA.menu.reduce((a, b) => a.rating > b.rating ? a : b).name;
        }
        document.getElementById('toggleViewBtn').onclick = () => {
            cardView = !cardView;
            document.getElementById('menuGrid').style.display = cardView ? 'flex' : 'none';
            document.getElementById('tableView').style.display = cardView ? 'none' : 'block';
            renderMenu();
        };

        // Dish modal
        const dishModal = document.getElementById('dishModal');
        const imagePreview = document.getElementById('imagePreview');
        document.getElementById('addDishBtn').onclick = () => {
            editIndex = null;
            document.getElementById('dishModalTitle').textContent = 'Add Dish';
            dishModal.style.display = 'flex';
        };
        document.getElementById('saveDishBtn').onclick = () => {
            const name = document.getElementById('dishName').value;
            const price = parseFloat(document.getElementById('dishPrice').value);
            const stock = parseInt(document.getElementById('dishStock').value);
            const category = document.getElementById('dishCategory').value;
            const image = imagePreview.src || 'https://via.placeholder.com/300x140';
            if (editIndex !== null) {
                DATA.menu[editIndex] = {
                    ...DATA.menu[editIndex],
                    name,
                    price,
                    stock,
                    category,
                    image
                };
            } else {
                DATA.menu.push({
                    name,
                    price,
                    stock,
                    category,
                    image,
                    rating: 4.5,
                    status: stock > 0 ? 'Available' : 'Out of Stock'
                });
            }
            dishModal.style.display = 'none';
            renderMenu();
        };
        document.getElementById('dishImage').onchange = e => {
            const file = e.target.files[0];
            if (file) {
                imagePreview.src = URL.createObjectURL(file);
                imagePreview.style.display = 'block';
            }
        };

        function openDishModal(idx) {
            editIndex = idx;
            const dish = DATA.menu[idx];
            document.getElementById('dishModalTitle').textContent = 'Edit Dish';
            dishModal.style.display = 'flex';
            document.getElementById('dishName').value = dish.name;
            document.getElementById('dishPrice').value = dish.price;
            document.getElementById('dishStock').value = dish.stock;
            document.getElementById('dishCategory').value = dish.category;
            imagePreview.src = dish.image;
            imagePreview.style.display = 'block';
        }

        function deleteDish(idx) {
            if (confirm('Delete this dish?')) {
                DATA.menu.splice(idx, 1);
                renderMenu();
            }
        }

        // Category modal
        const categoryModal = document.getElementById('categoryModal');
        document.getElementById('addCategoryBtn').onclick = () => {
            categoryModal.style.display = 'flex';
            renderCategoryList();
        };
        document.getElementById('addCategory').onclick = () => {
            const val = document.getElementById('newCategory').value;
            if (val && !DATA.categories.includes(val)) {
                DATA.categories.push(val);
                renderCategories();
                renderCategoryList();
            }
            document.getElementById('newCategory').value = '';
        };

        function renderCategoryList() {
            const list = document.getElementById('categoryList');
            list.innerHTML = '';
            DATA.categories.forEach((c, i) => {
                const div = document.createElement('div');
                div.textContent = c;
                const del = document.createElement('button');
                del.textContent = 'x';
                del.onclick = () => {
                    DATA.categories.splice(i, 1);
                    renderCategories();
                    renderCategoryList();
                };
                div.appendChild(del);
                list.appendChild(div);
            });
        }

        function updateCategorySelect() {
            const sel = document.getElementById('dishCategory');
            sel.innerHTML = '';
            DATA.categories.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c;
                opt.textContent = c;
                sel.appendChild(opt);
            });
        }

        // Close modals on outside click
        window.onclick = (e) => {
            if (e.target.classList.contains('modal')) e.target.style.display = 'none';
        };

        renderCategories();
        renderMenu();
    </script>