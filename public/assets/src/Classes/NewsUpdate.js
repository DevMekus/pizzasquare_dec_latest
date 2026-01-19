import Utility from "./Utility.js";
import Pagination from "./Pagination.js";



export default class NewsUpdate {
  static NEWSUPDATES = [];
  static pagination = Utility.el("pagination");
  static STATUS = ["active", "inactive"]; 

  static renderNewsUpdate() {
    const row = document.getElementById("updateRow");
    row.innerHTML = "";

    if (!NewsUpdate.NEWSUPDATES || NewsUpdate.NEWSUPDATES.length == 0 ) {
      row.innerHTML = `<div class="col-12 my-5">
        <h4 class="text-center muted">No news updates found</h4>
        <p class="text-center muted">Please check back later for exciting updates!</p>      
      </div>`;
      return;
    }

    NewsUpdate.NEWSUPDATES.forEach((update, index) => {
      row.innerHTML += `
        <div class="col-sm-6 mb-4" data-aos="fade-up" 
        data-aos-delay="${index * 100}">
          <div class="deal-card">
            <img src="${update.image}" alt="${update.title}" class="deal-img">
            <div class="overlay">
              <div class="d_details text-center text-white">
                <h4>${update.title}</h4>
                <p>${update.description}</p>
              </div>
            </div>
          </div>
        </div>

      `;
    });
  }

   static renderUpdatesTable(data, page = 1) {
    const tbody = document.querySelector("#update-table tbody");
    const notDATA = Utility.el("no-data");

    tbody.innerHTML = "";
    notDATA.innerHTML = "";

    // Deals.renderSummary();

    const updates = Array.isArray(data) ? data : Object.values(data || {});

    const start = (page - 1) * Utility.PAGESIZE;
    const end = start + Utility.PAGESIZE;

    if (!updates || updates.length == 0) {
      tbody.innerHTML = `<tr><td colspan="${
            Utility.role == "admin" ? 8 : 6
            }" class="text-center muted"><i class="fas fa-info-circle"></i> News and Updates not available</td></tr>`;
          
      NewsUpdate.pagination.style.display = "none";
      return;
    }

    NewsUpdate.pagination.style.display = "flex";
    const paginatedData = updates.slice(start, end);

    paginatedData.forEach((item, idx) => {
      const tr = document.createElement("tr");
      tr.classList.add("bounce-card");
      tr.innerHTML = `
       <td>${idx + 1}</td>
       <td>${item.news_id}</td>
       <td>${Utility.toTitleCase(item.title)}</td>
       <td>${item.created_at}</td>
        <td>
          <span class="status ${item.status ? item.status : ""}">
          ${item.status ? Utility.toTitleCase(item.status) : ""}</span>
        </td>
      
       <td>
         <button class="btn btn-xs btn-primary" data-open="${item.id}">
         <i class="fa fa-edit"></i> View
         </button>
         <button class="btn btn-xs btn-ghost" 
         data-delete="${item.id}">
          <i class="fa fa-trash"></i> Delete
         </button>
       </td>
       `;
      tbody.appendChild(tr);
    });
    if (deals.length > Utility.PAGESIZE)
      Pagination.render(deals.length, page, deals, NewsUpdate.renderUpdatesTable);
  }

   static openUpdatesModal(id) {
    const deal = NewsUpdate.NEWSUPDATES.find((deal) => deal.id == id);
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

    const statusHtml = NewsUpdate.STATUS.map((i, idx) => {
      return `<option value="${i}" ${deal.status == i ? "selected" : ""}>
      ${Utility.toTitleCase(i)}</option>`;
    }).join("");

    domBody.innerHTML = `
        <form class="row" id="updateUpdateForm" data-id="${id}">
          <div class="container">
              <div class="row">
                  <div class="col-sm-6">
                      <div class="form-group">
                          <label for="title">Title</label>
                          <input type="text" id="title" value="${deal.title}" name="title" placeholder="eg: October Splash">
                      </div>

                      <div class="form-group">
                          <label for="description">Description</label>
                          <textarea name="description" id="description" value="${deal.description}" maxlength="100" placeholder="Write briefly about promo">${deal.description}</textarea>
                          <small id="charCount">0 / 100</small>
                      </div>
                    
                      <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                          ${statusHtml}
                        </select>
                      </div>

                  </div>
                  <div class="col-sm-6">
                        <img src="${deal.image}" class="w-100" style="height:250px; object-fit:cover" 
                alt="${deal.title}" />
                      <div>
                          <label class="muted mt-2">Nws / Update banner</label>
                          <input type="file" id="dishStock" name="newsBanner" accepts="image/*" placeholder="Upload Images">
                      </div>
                      <p class="muted mt-2">By clicking on the submit button, you will make upload this information.</p>
                      <button class="btn btn-primary mt-2" type="submit">Save Changes</button>
                  </div>
              </div>
          </div>
        </form>
      `;
    $("#displayDetails").modal("show");
    NewsUpdate.runCounter();
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


  static createUpdatesModal() {
    let domBody = Utility.el("detailModalBody");
    const domFooter = Utility.el("detailModalButtons");
    let domTitle = Utility.el("detailModalLabel");

    domTitle.innerHTML = "";
    domBody.innerHTML = "";
    domFooter.innerHTML = "";
    domTitle.textContent = `New News/Update`;

    domBody.innerHTML = `
        <form class="row"  id="newUpdateForm" enctype="multipart/form-data">
          <div class="container">
              <div class="row">
                  <div class="col-sm-6">
                      <div class="form-group">
                          <label for="title">Title</label>
                          <input type="text" id="title" name="title" placeholder="eg: October Splash">
                      </div>

                      <div class="form-group">
                          <label for="description">Description</label>
                          <textarea name="description" id="description" maxlength="100" placeholder="Write briefly about this update / news"></textarea>
                          <small id="charCount">0 / 100</small>
                      </div>
                  </div>
                  <div class="col-sm-6">
                      <div class="image-box" id="preview"></div>
                      <div>
                          <label class="muted mt-2">News / Update Banner</label>
                          <input type="file" id="newsBanner" name="newsBanner" accepts="image/*" placeholder="Upload Images">
                      </div>
                      <p class="muted mt-2">By clicking on the submit button, you will make upload this information.</p>
                      <button class="btn btn-md btn-primary mt-2" type="submit">Create Update</button>
                  </div>
              </div>
          </div>
        </form>
      `;
    $("#displayDetails").modal("show");
    NewsUpdate.runCounter();
    //preview image
    const newsBanner = document.getElementById("newsBanner");
    const preview = document.getElementById("preview");
    newsBanner.addEventListener("change", function () {
      preview.innerHTML = "";
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          const img = document.createElement("img");
          img.src = e.target.result;
          img.style.width = "100%";
          img.style.height = "250px";
          img.style.objectFit = "cover";
          preview.appendChild(img);
        };
        reader.readAsDataURL(file);
      }
    });
  }

  
  

 

  
}
