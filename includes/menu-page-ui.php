 <div class="content-centered p-4">
    <div data-aos="fade-down" class="page-header mt-4">
        <div class="welcome">Manage Products</div>
        <div class="center-mobile">Manage Your business Products.</div>
        <div class="actions">
          <a href="new-product" class="btn btn-primary btn-sm"><i class="fas fa-arrow-left"></i> Add Product</a>
        </div>
    </div>
   

    <section id="summary-cards" class="summary-cards" data-aos="fade-down">
        <div class="kpi-card bounce-card">
            <div class="icon-box bg-default"><i class="bi bi-egg-fried fs-2"></i></div>
            <p>Total Items</p>
            <h2 id="totalItems">0</h2>
        </div>
        <div class="kpi-card bounce-card">
            <div class="icon-box bg-success"><i class="bi bi-check-lg fs-2"></i></div>
            <p>Available</p>
            <h2 id="availableItems">0</h2>
        </div>
        <div class="kpi-card bounce-card">
            <div class="icon-box bg-primary">
                <i class="bi bi-x-lg fs-2"></i>
            </div>
            <p>Low Stock</p>
            <h2 id="outOfStock">0</h2>
        </div>
        <div class="kpi-card bounce-card">
            <div class="icon-box bg-default">
                <i class="bi bi-trophy fs-2"></i>
            </div>
            <p>Most Popular</p>
            <h2 id="popularDish">-</h2>
        </div>
    </section>

    <section class="category-tabs p-0 mt-3 mb-3" data-aos="fade-down" id="categoryTabs">        
    </section>                   
    <div class="table-responsive" data-aos="fade-down">
        <section class="table-view pizzasquare-table table-responsive" id="tableView">
          <table class="" id="productTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>SKU</th>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Products will render here -->
            </tbody>
            </table>
        </section>
    </div>
    <div id="pagination" class="p-4 pagination"></div>
    <div class="no-data" id="no-data"></div>

    
</div>
<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="editProductForm">
        <div class="modal-header">
          <h5 class="modal-title">Edit Product</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="editProductId">
          <div class="mb-3">
            <label for="editProductName" class="form-label">Name</label>
            <input type="text" id="editProductName" class="form-control">
          </div>
          <div class="mb-3">
            <label for="editProductSKU" class="form-label">SKU</label>
            <input type="text" id="editProductSKU" class="form-control">
          </div>
          <div class="mb-3">
            <label for="editProductDescription" class="form-label">Description</label>
            <textarea id="editProductDescription" class="form-control"></textarea>
          </div>
          <div class="mb-3">
            <label for="editProductStatus" class="form-label">Status</label>
            <select id="editProductStatus" class="form-select">
              <option value="1">Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>
          <hr>
          <h6>Sizes & Pricing</h6>
          <div id="sizesContainer" class="row g-2">
            <!-- Sizes checkboxes and price inputs will be rendered here -->
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>




