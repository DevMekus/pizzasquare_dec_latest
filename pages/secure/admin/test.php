<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-4">

    <!-- Category Selector -->
   <!-- CREATE PRODUCT -->
<div class="card mb-4">
  <div class="card-header">Create Product</div>
  <div class="card-body">
    <form id="createProductForm">
      <div class="mb-3">
        <label class="form-label">Product Name</label>
        <input type="text" id="productName" class="form-control" required>
      </div>

      <button type="submit" class="btn btn-primary">Save Product</button>
    </form>
  </div>
</div>

<hr>

<!-- ASSIGN SIZES TO PRODUCT -->
<div class="card">
  <div class="card-header">Assign Sizes to Product</div>
  <div class="card-body">

    <div class="mb-3">
      <label class="form-label">Select Product</label>
      <select id="productSelect" class="form-select">
        <option value="">-- Select Product --</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Select Sizes</label>
      <select id="sizeSelect" class="form-select" multiple>
        <option value="S">Small (S)</option>
        <option value="M">Medium (M)</option>
        <option value="L">Large (L)</option>
        <option value="XL">XL</option>
      </select>
      <small class="text-muted">Hold CTRL to select multiple sizes.</small>
    </div>

    <div id="selectedSizesContainer"></div>

    <button id="saveSizes" class="btn btn-success mt-3">Save Product Sizes</button>
  </div>
</div>


</div>

<script>
    // DEMO DATA (Simulating that admin already created products)
let products = [
  { id: 1, name: "Chicken Pizza" },
  { id: 2, name: "Beef Shawarma" },
  { id: 3, name: "Exotic Drink" },
  { id: 4, name: "Icecream Plate" },
  { id: 5, name: "Mirinda" }
];


// Load products in dropdown
const productSelect = document.getElementById("productSelect");
products.forEach(p => {
  productSelect.innerHTML += `<option value="${p.id}">${p.name}</option>`;
});


// When sizes are selected, render rows
document.getElementById("sizeSelect").addEventListener("change", function () {
  const selected = Array.from(this.selectedOptions).map(o => o.value);
  const container = document.getElementById("selectedSizesContainer");
  container.innerHTML = "";

  selected.forEach(size => {
    container.innerHTML += `
      <div class="card p-3 mb-2">
        <h6><strong>Size ${size}</strong></h6>

        <div class="row">
          <div class="col-md-4">
            <label>Price</label>
            <input type="number" class="form-control size-price" data-size="${size}" placeholder="Enter price">
          </div>

          <div class="col-md-4">
            <label>Shared Stock?</label>
            <select class="form-select size-stock" data-size="${size}">
              <option value="1">Yes (Use stock from parent product)</option>
              <option value="0">No (Has own stock)</option>
            </select>
          </div>
        </div>
      </div>
    `;
  });
});


// Save sizes (ready for backend)
document.getElementById("saveSizes").addEventListener("click", function () {
  const product_id = productSelect.value;
  if (!product_id) return alert("Select a product");

  const selectedSizes = Array.from(
    document.querySelectorAll("#sizeSelect option:checked")
  ).map(o => o.value);

  let payload = selectedSizes.map(size => ({
    product_id,
    size,
    price: document.querySelector(`.size-price[data-size="${size}"]`).value,
    shared_stock: document.querySelector(`.size-stock[data-size="${size}"]`).value
  }));

  console.log("FINAL PAYLOAD TO SEND:", payload);

  alert("Sizes saved successfully!");
});


</script>





</body>
</html>
