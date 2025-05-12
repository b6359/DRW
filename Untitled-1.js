<div class="page-content">
  <div class="top-model">
    <h2>Product Master</h2>
    <div class="search">
      <input type="text" id="productSearchInput" placeholder="Search Products..." style="padding: 5px; border-radius: 4px; border: 1px solid #ccc;" />
      <button id="openProductModalBtn" style="width: 125px;">Add Data</button>
    </div>
  </div>

  <table id="productTable" border="1" cellpadding="10" cellspacing="0" style="width: 100%; margin-top: 20px;">
    <thead>
      <tr>
        <th>ID</th>
        <th>Product Name</th>
        <th>Model No</th>
        <th>Quantity</th>
        <th>Rate</th>
        <th>Pending For Belt</th>
        <th>Created Date</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>

  <!-- Modal -->
  <div id="productModal" class="modal" style="display:none; position: fixed; top:0; left:0; width:100%; height:100%; background-color: rgba(0,0,0,0.5);">
    <div class="modal-content" style="background:#fff; margin:10% auto; padding:20px; width:80%; position:relative;">
      <span class="closeProductModalBtn" style="float:right; cursor:pointer;">&times;</span>
      <h3>Add New Product</h3>

      <form id="productForm">
        <div class="form-row">
          <label>Product Name:<input type="text" name="itemName" required></label>
          <label>Model No:<input type="text" name="modelNo" required></label>
        </div>
        <div class="form-row">
          <label>Quantity:<input type="number" name="qty" required></label>
          <label>Rate:<input type="number" name="rate" step="0.01" required></label>
        </div>
        <div class="form-row">
          <label>Pending For Belt:<input type="number" name="pendingForBelt" required></label>
          <label>Created By (ID):<input type="number" name="createdBy" value="1" required></label>
        </div>

        <div style="margin-top:20px;">
          <label style="font-weight:bold;">Select Item(s):</label>
          <button type="button" class="addMoreBtn">+ Add More</button>
          <div id="itemFieldsContainer" style="margin-top: 10px;"></div>
        </div>

        <button type="submit" style="margin-top: 20px;">Submit</button>
      </form>
    </div>
  </div>
</div>
