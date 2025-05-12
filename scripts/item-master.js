function initializeItemMaster() {
    const modal = document.getElementById("itemModal");
    const openBtn = document.getElementById("openModalBtn");
    const closeBtn = modal?.querySelector(".close");
    const form = document.getElementById("itemForm");
    const tableBody = document.querySelector("#itemTable tbody");
    const searchInput = document.getElementById("searchInput");

    if (!modal || !openBtn || !form || !tableBody || !searchInput) {
        console.warn("One or more required elements are missing.");
        return;
    }

    let allItems = []; // Store all items globally

    openBtn.addEventListener("click", () => {
        form.reset();
        delete form.dataset.editingId;
        modal.style.display = "block";
    }); 

    closeBtn.addEventListener("click", () => {
        modal.style.display = "none";
    });

    window.addEventListener("click", (e) => {
        if (e.target === modal) {
            modal.style.display = "none";
        }
    });

    form.addEventListener("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        data.rate = parseFloat(data.rate || 0);
        data.qty = parseInt(data.qty);
        data.createdBy = parseInt(data.createdBy || "1");

        const isEditing = !!form.dataset.editingId;
        if (isEditing) {
            data.id = parseInt(form.dataset.editingId);
        }

        const url = isEditing
            ? "http://127.0.0.1/DRW/api/items/update.php"
            : "http://127.0.0.1/DRW/api/items/add.php";

        fetch(url, {
            method: isEditing ? "PUT" : "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data)
        })
            .then(res => res.json())
            .then(res => {
                
                if (res.status === 200) {
                    alert(isEditing ? "Item updated!" : "Item added!");
                    form.reset();
                    delete form.dataset.editingId;
                    modal.style.display = "none";
                    fetchItems();
                } else {
                    alert("Operation failed: " + res.message);
                }
            })
            .catch(err => console.error(`${isEditing ? "Update" : "Add"} API error:`, err));
    });

    searchInput.addEventListener("input", function () {
        const query = this.value.trim().toLowerCase();
        if (query === "") {
            displayItems(allItems); // Show all if input is cleared
        } else {
            const query = this.value.trim().toLowerCase();
            const filtered = allItems.filter(item =>
                Object.values(item).some(val =>
                    String(val).toLowerCase().includes(query)
                )
            );
            displayItems(filtered);
        }
    });

    function fetchItems() {
        fetch("http://127.0.0.1/DRW/api/items/all.php")
            .then(res => res.json())
            .then(res => {
                if (res.status === 200) {
                    allItems = res.data; // Store data globally
                    displayItems(allItems);
                } else {
                    displayItems([]); // No data available
                }
            })
            .catch(err => {
                console.error("Fetch API error:", err);
                displayItems([]); // Error fetching data
            });
    }

    function displayItems(items) {
        const tableBody = document.querySelector("#itemTable tbody");

        if (!items || items.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="8">No matching items found</td></tr>`;
            return;
        }

        tableBody.innerHTML = items.map(item => {
            const createdAt = item.createdAt ? item.createdAt.split(' ')[0] : '-';
            return `
                <tr data-id="${item.id}">
                    <td>${item.id}</td>
                    <td>${item.itemName}</td>
                    <td>${item.modelNo}</td>
                    <td>${item.rate ?? '0.00'}</td>
                    <td>${item.qty}</td>
                    <td>${createdAt}</td>
                    <td>
                        <button class="editBtn"><i class="fas fa-edit"></i></button>
                        <button class="deleteBtn"><i class="fas fa-trash-alt"></i></button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    tableBody.addEventListener("click", function (e) {
        const row = e.target.closest("tr");
        const id = row?.dataset.id;

        if (e.target.classList.contains("deleteBtn")) {
            if (confirm("Are you sure you want to delete this item?")) {
                fetch("http://127.0.0.1/DRW/api/items/delete.php", {
                    method: "DELETE",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ id: parseInt(id) })
                })
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === 200) {
                            alert("Item deleted!");
                            fetchItems();
                        } else {
                            alert("Delete failed: " + res.message);
                        }
                    })
                    .catch(err => console.error("Delete API error:", err));
            }
        }

        if (e.target.classList.contains("editBtn")) {
            const cells = row.querySelectorAll("td");
            form.itemName.value = cells[1].innerText;
            form.modelNo.value = cells[2].innerText;
            form.rate.value = cells[3].innerText;
            form.qty.value = cells[4].innerText;

            form.dataset.editingId = id;
            modal.style.display = "block";
        }
    });

    fetchItems();
}
