
    function initializeProductMaster() {
        const modal = document.getElementById("productModal");
        const openBtn = document.getElementById("openProductModalBtn");
        const closeBtn = modal?.querySelector(".closeProductModalBtn");
        const form = document.getElementById("productForm");
        const tableBody = document.querySelector("#productTable tbody");
        const searchInput = document.getElementById("productSearchInput");

        let allProducts = [];
        let allItems = [];

        openBtn.addEventListener("click", () => {
            form.reset();
            delete form.dataset.editingId;
            document.getElementById("itemFieldsContainer").innerHTML = "";

            // Load items first, then add initial item row
            loadItemOptions(() => {
                addItemRow();
            });

            modal.style.display = "block";
        });


        closeBtn.addEventListener("click", () => modal.style.display = "none");
        window.addEventListener("click", e => { if (e.target === modal) modal.style.display = "none"; });

        form.addEventListener("submit", function (e) {
            e.preventDefault();
            const data = Object.fromEntries(new FormData(form).entries());
            const itemMasterIds = [];
            const itemFields = document.querySelectorAll(".itemSelection-row");
            itemFields.forEach(row => {
                const select = row.querySelector("select");
                if (select && select.value) {
                    itemMasterIds.push(parseInt(select.value));
                }
            });

            const payload = {
                productName: data.itemName || "", // If no value, set it to empty string
                modelNo: data.modelNo || "",       // If no value, set it to empty string
                rate: parseFloat(data.rate) || 0,  // Default to 0 if not provided
                pendingForBelt: data.pendingForBelt || "no", // Default to "no" if not provided
                itemMasterIds: itemMasterIds
            };
            console.log("Payload being sent to API:", payload);


            // Set default values for required fields
            payload.qty = data.qty ? parseInt(data.qty) : 1; // Default to 1 if not provided
            payload.createdBy = data.createdBy ? parseInt(data.createdBy) : 1; // Default to 1 if not provided

            if (form.dataset.editingId) payload.id = parseInt(form.dataset.editingId);

            const url = form.dataset.editingId
                ? "http://127.0.0.1/DRW/api/products/update.php"
                : "http://127.0.0.1/DRW/api/products/add.php";

            fetch(url, {
                method: form.dataset.editingId ? "PUT" : "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(payload)
            })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 200) {
                        alert(form.dataset.editingId ? "Product updated!" : "Product added!");
                        modal.style.display = "none";
                        fetchProducts();
                    } else {
                        alert("Operation failed: " + res.message);
                    }
                });
        });


        function fetchProducts() {
            fetch("http://127.0.0.1/DRW/api/products/all.php")
                .then(res => res.json())
                .then(res => {
                    allProducts = res.data || [];
                    displayProducts(allProducts);
                });
        }

        function displayProducts(products) {
            tableBody.innerHTML = products.length
                ? products.map((product, index) => `
               <tr data-id="${product.id}">
                 <td>${index + 1}</td> <!-- Sequential numbering -->
                 <td>${product.productName}</td>
                 <td>${product.modelNo}</td>
                 <td>${product.rate}</td>
                 <td>${product.pendingForBelt === 'yes' ? 'Yes' : 'No'}</td>
                 <td>${(product.createdAt || '').split(' ')[0]}</td>
                 <td>
                   <button class="editBtn" title="Edit"><i class="fas fa-edit"></i></button>
                   <button class="deleteBtn" title="Delete"><i class="fas fa-trash-alt"></i></button>
                 </td>
               </tr>`).join('')
                : `<tr><td colspan="8">No products found</td></tr>`;
        }


        function loadItemOptions(callback) {
            fetch("http://127.0.0.1/DRW/api/items/all.php")
                .then(res => res.json())
                .then(res => {
                    allItems = res.data || [];
                    if (typeof callback === "function") callback(); // now call addItemRow
                });
        }


        function addItemRow() {
            const container = document.getElementById("itemFieldsContainer");
            const row = document.createElement("div");
            row.className = "itemSelection-row";
            row.style = "display: flex; align-items: center; gap: 10px; margin-bottom: 10px;";

            row.innerHTML = `
     <div style="display: flex; align-items: center; position: relative; width: 100%; gap: 10px;">
            <!-- Datalist-based input -->
            <input list="itemList" class="itemDatalistInput" placeholder="Search item..."
                style="flex: 1; min-width: 300px; font-size: 14px; padding: 6px; border: 1px solid grey; border-radius: 20px; outline: none;" required />

            <datalist id="itemList" style="min-width: 300px;">
                ${allItems.map(item => `<option value="${item.itemName}">`).join('')}
            </datalist>

            <!-- Quantity input -->
            <input type="number" class="itemQtyInput" placeholder="Qty" min="1"
                style="width: 70px; border: 1px solid grey; border-radius: 20px; outline: none;" required/>

            <!-- Remove button -->
            <span class="removeBtn" title="Remove" style="cursor: pointer; font-size: 20px; color: red;">
                <i class="fas fa-trash-alt"></i>
            </span>
        </div>
    `;

            container.appendChild(row);
            initializeItemRow(row);
        }


        function initializeItemRow(row) {
            const select = row.querySelector(".itemSelect");

            select.addEventListener("change", function () {
                // Handle item selection logic here if needed
                console.log("Item Selected: ", select.value);
            });
        }

        document.addEventListener("click", e => {
            // Handle Add More button
            if (e.target.classList.contains("addMoreBtn")) {
                addItemRow();
            }

            // Handle Remove button
            if (e.target.closest(".removeBtn")) {
                const row = e.target.closest(".itemSelection-row");
                if (row) row.remove(); // Remove the row containing the delete button
            }
        });

        tableBody.addEventListener("click", function (e) {
            const row = e.target.closest("tr");
            const id = row?.dataset.id;
            if (!id) return;

            if (e.target.classList.contains("editBtn")) {
                const cells = row.querySelectorAll("td");
                form.itemName.value = cells[1].innerText;
                form.modelNo.value = cells[2].innerText;
                // form.qty.value = cells[3].innerText;
                form.rate.value = cells[3].innerText;
                form.pendingForBelt.value = cells[4].innerText === 'Yes' ? 'yes' : 'no';
                // form.createdBy.value = "1";
                form.dataset.editingId = id;
                modal.style.display = "block";
            }

            if (e.target.classList.contains("deleteBtn") && confirm("Are you sure?")) {
                fetch("http://127.0.0.1/DRW/api/products/delete.php", {
                    method: "DELETE",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ id: parseInt(id) })
                })
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === 200) {
                            alert("Deleted!");
                            fetchProducts();
                        } else {
                            alert("Delete failed: " + res.message);
                        }
                    });
            }
        });

        searchInput.addEventListener("input", function () {
            const query = this.value.toLowerCase();
            const filtered = allProducts.filter(product =>
                product.productName.toLowerCase().includes(query) ||
                product.modelNo.toLowerCase().includes(query)
            );
            displayProducts(filtered);
        });

        fetchProducts();
    }