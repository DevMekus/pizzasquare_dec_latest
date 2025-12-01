import Utility from "./Utility.js";
import Pagination from "./Pagination.js";



export default class Deals {
  static DEALS = [];
  static pagination = Utility.el("pagination");
  static STATUS = ["active", "inactive"]; 

  static renderDeals(category = "all") {
    const row = document.getElementById("dealsRow");
    row.innerHTML = "";

    const filtered = Deals.DEALS.filter(
      (deal) => category === "all" || deal.category === category
    );

    if (!Deals.DEALS || Deals.DEALS.length == 0 || filtered.length == 0) {
      Utility.renderEmptyState(row);
      Utility.toast("Deal not found", "error");
      return;
    }

    filtered.forEach((deal, index) => {
      row.innerHTML += `
        <div class="col-sm-6 mb-4" data-aos="fade-up" 
        data-aos-delay="${index * 100}">
          <div class="deal-card">
            <img src="${deal.image}" alt="${deal.title}" class="deal-img">
            <div class="overlay">
              <div class="d_details text-center text-white">
                <h4>${deal.title}</h4>
                <p>${deal.description}</p>
              </div>
            </div>
          </div>
        </div>

      `;
    });
  }

   static renderDealsTable(data, page = 1) {
    const tbody = document.querySelector("#dealsTable tbody");
    const notDATA = Utility.el("no-data");

    tbody.innerHTML = "";
    notDATA.innerHTML = "";

    // Deals.renderSummary();

    const deals = Array.isArray(data) ? data : Object.values(data || {});

    const start = (page - 1) * Utility.PAGESIZE;
    const end = start + Utility.PAGESIZE;

    if (!deals || deals.length == 0) {
      Utility.renderEmptyState(notDATA);

      Deals.pagination.style.display = "none";
      return;
    }

    Deals.pagination.style.display = "flex";

    const paginatedData = deals.slice(start, end);

    paginatedData.forEach((item, idx) => {
      const tr = document.createElement("tr");
      tr.classList.add("bounce-card");
      tr.innerHTML = `
       <td>${idx + 1}</td>
       <td>${item.deal_id}</td>
       <td>${Utility.toTitleCase(item.title)}</td>
       <td>${item.created_at}</td>
        <td>
          <span class="status ${item.status ? item.status : ""}">
          ${item.status ? Utility.toTitleCase(item.status) : ""}</span>
        </td>
      
       <td>
         <button class="btn btn-sm btn-primary" data-open="${item.id}">
         <i class="fa fa-edit"></i> View
         </button>
         <button class="btn btn-sm btn-ghost" 
         data-delete="${item.id}">
          <i class="fa fa-trash"></i> Delete
         </button>
       </td>
       `;
      tbody.appendChild(tr);
    });
    if (deals.length > Utility.PAGESIZE)
      Pagination.render(deals.length, page, deals, Deals.renderDealsTable);
  }

   static openDealModal(id) {
    const deal = Deals.DEALS.find((deal) => deal.id == id);
    if (!deal) {
      Utility.toast("Deal not found", "error");
      return;
    }

    let domBody = Utility.el("detailModalBody");
    const domFooter = Utility.el("detailModalButtons");
    let domTitle = Utility.el("detailModalLabel");

    domTitle.innerHTML = "";
    domBody.innerHTML = "";
    domFooter.innerHTML = "";

    domTitle.textContent = `Manage ${deal.title}`;

    const statusHtml = Deals.STATUS.map((i, idx) => {
      return `<option value="${i}" ${deal.status == i ? "selected" : ""}>
      ${Utility.toTitleCase(i)}</option>`;
    }).join("");

    domBody.innerHTML = `
        <form class="row" id="updateDeal" data-id="${id}">
          <div class="container">
              <div class="row">
                  <div class="col-sm-6">
                      <div class="form-group">
                          <label >Promotion name</label>
                          <input type="text" id="dishName" value="${deal.title}" name="title" placeholder="eg: October Splash">
                      </div>

                      <div class="form-group">
                          <label>Description</label>
                          <textarea name="description" value="${deal.description}" maxlength="100" placeholder="Write briefly about promo">${deal.description}</textarea>
                          <small id="charCount">0 / 100</small>
                      </div>
                    
                      <div class="form-group">
                        <label>Status</label>
                        <select id="dishCategory" name="status">
                          ${statusHtml}
                        </select>
                        <div id="categorySizes" class="mt-2"></div> 
                      </div>

                  </div>
                  <div class="col-sm-6">
                        <img src="${deal.image}" class="w-100" style="height:170px; object-fit:cover" 
                alt="${deal.title}" />
                      <div>
                          <label class="muted mt-2">Deal image</label>
                          <input type="file" id="dishStock" name="dealsBanner" accepts="image/*" placeholder="Upload Images">
                      </div>
                      <p class="muted mt-2">By clicking on the submit button, you will make upload this information.</p>
                      <button class="btn btn-primary mt-2" type="submit">Save Changes</button>
                  </div>
              </div>
          </div>
        </form>
      `;
    $("#displayDetails").modal("show");
    Deals.runCounter();
  }

  static runCounter() {
    const textarea = document.querySelector('textarea[name="description"]');
    const counter = document.getElementById("charCount");

    textarea.addEventListener("input", () => {
      counter.textContent = `${textarea.value.length} / 100`;
      textarea.value.length <= 50
        ? (counter.style.color = "green")
        : (counter.style.color = "red");
    });
  }


  static newDealsModal() {
    let domBody = Utility.el("detailModalBody");
    const domFooter = Utility.el("detailModalButtons");
    let domTitle = Utility.el("detailModalLabel");

    domTitle.innerHTML = "";
    domBody.innerHTML = "";
    domFooter.innerHTML = "";
    domTitle.textContent = `New Promotions`;

    domBody.innerHTML = `
        <form class="row"  id="createDeal" enctype="multipart/form-data">
          <div class="container">
              <div class="row">
                  <div class="col-sm-6">
                      <div class="form-group">
                          <label>Promotion name</label>
                          <input type="text" id="dishName" name="title" placeholder="eg: October Splash">
                      </div>

                      <div class="form-group">
                          <label>Description</label>
                          <textarea name="description" maxlength="100" placeholder="Write briefly about promo"></textarea>
                          <small id="charCount">0 / 100</small>
                      </div>
                  </div>
                  <div class="col-sm-6">
                      <div class="image-box"></div>
                      <div>
                          <label class="muted mt-2">Product image</label>
                          <input type="file" id="dishStock" name="dealsBanner" accepts="image/*" placeholder="Upload Images">
                      </div>
                      <p class="muted mt-2">By clicking on the submit button, you will make upload this information.</p>
                      <button class="btn btn-primary mt-2" type="submit">Create Promo</button>
                  </div>
              </div>
          </div>
        </form>
      `;
    $("#displayDetails").modal("show");
    Deals.runCounter();
  }
  

 

  
}
