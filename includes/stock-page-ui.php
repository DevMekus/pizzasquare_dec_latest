<div class="content-centered p-4">
  <div data-aos="fade-down" class="page-header mt-4">
        <div class="welcome">Category Size Stock</div>
        <div class="center-mobile">Manage product stocks.</div>
         <div class="actions mt-2 mb-3" data-aos="fade-down">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStockModal">
                Add Category Stock
            </button>
        </div>
   
    </div>

    <section>
        <div class="pizzasquare-table table-responsive">
            <table class="table-sms">
              <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Category</th>
                    <th>Size</th>
                    <th>Quantity</th>
                    <th>Low Stock Threshold</th>
                    <th>Last Updated</th>
                    <th>Action</th>
                </tr>
              </thead>
              <tbody id="categoryStockBody">
                  <!-- Stock items will be dynamically rendered -->
              </tbody>
          </table>
            <div id="pagination" class="p-4 pagination"></div>
            <div id="no-data"></div>
      </div>
    </section>
</div>
<div class="modal fade" id="addStockModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="addCategoryStockForm">
        <div class="modal-header">
          <h5 class="modal-title">Add Category Stock</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
            
            <div class="mb-3">
                <label class="form-label">Category</label>
                <select class="form-select" name="category_id" id="category_id">
                    <!-- populated by JS: pizza, shawarma, etc -->
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Size</label>
                <select class="form-select" name="size_id" id="stockSizeSelect">
                    <!-- populated dynamically when category changes -->
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Quantity (Qty)</label>
                <input type="number" class="form-control" name="qty" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Low Stock Threshold</label>
                <input type="number" class="form-control" name="low_stock_threshold" required>
            </div>

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="editStockModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editCategoryStockForm">
        <div class="modal-header">
          <h5 class="modal-title">Edit Category Stock</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

            <input type="hidden" name="id" id="editStockId">

            <div class="mb-3">
                <label class="form-label">Category</label>
                <input type="text" class="form-control" id="editCategoryName" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Size</label>
                <input type="text" class="form-control" id="editSizeLabel" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Quantity (Qty)</label>
                <input type="number" class="form-control" name="qty" id="editQty" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Low Stock Threshold</label>
                <input type="number" class="form-control" name="low_stock_threshold" id="editLowStock" required>
            </div>

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Update</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>

      </form>
    </div>
  </div>
</div>











