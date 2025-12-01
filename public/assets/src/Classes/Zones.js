import Pagination from "./Pagination.js";
import Utility from "./Utility.js";

export default class Zones {
  static ZONES = [];

   static renderZoneTable(data, page = 1) {
    const tbody = document.querySelector("#zoneTable tbody");
    const notDATA = Utility.el("no-data");

    tbody.innerHTML = "";
    notDATA.innerHTML = "";

    const zones = Array.isArray(data) ? data : Object.values(data || {});

    const start = (page - 1) * Utility.PAGESIZE;
    const end = start + Utility.PAGESIZE;

    if (!zones || zones.length == 0) {
      Utility.renderEmptyState(notDATA);
      return;
    }

    const paginatedData = zones.slice(start, end);

    paginatedData.forEach((item, idx) => {
      const tr = document.createElement("tr");
      tr.classList.add("bounce-card");
      tr.innerHTML = `
        <td>${idx + 1}</td>
        <td>${Utility.toTitleCase(item.city)}</td>      
        <td>${Utility.fmtNGN(item.delivery_price)}</td>        
        <td>
          <button class="btn btn-sm btn-primary" data-action="view" data-id="${
            item.id
          }">
         View
          </button>
          <button class="btn btn-sm btn-ghost" 
          data-action="delete" data-id="${item.id}">
          Delete
          </button>
        </td>
        `;
      tbody.appendChild(tr);
    });
    if (zones.length > Utility.PAGESIZE)
      Pagination.render(zones.length, page, zones, Zones.renderZoneTable);
  }

  static openDetail(zone) {
    let domBody = Utility.el("detailModalBody");
    const domFooter = Utility.el("detailModalButtons");
    let domTitle = Utility.el("detailModalLabel");

    domTitle.innerHTML = "";
    domBody.innerHTML = "";
    domFooter.innerHTML = "";

    domTitle.textContent = `Manage ${zone.city}`;

    domBody.innerHTML = `
      <form class="row" id="updateZone" data-id="${zone.id}">
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="muted">City name</label>
                        <input type="text" id="dishName" name="city" value="${zone.city}" placeholder="eg: New Haven">
                    </div>
                    <div class="form-group">
                      <label class="muted">Delivery Price</label>
                        <input type="number" id="dishPrice" value="${zone.delivery_price}" name="delivery_price" placeholder="Eg: 2000">
                    </div>
                  
                </div>
                <div class="col-sm-6">                  
                    <p class="muted mt-2">By clicking on the submit button, you will make changes to the zone information.</p>
                    <button class="btn btn-primary mt-2" type="submit">Save Changes</button>
                </div>
            </div>
        </div>
      </form>
    `;
    $("#displayDetails").modal("show");
  }

  static zoneModal() {
    let domBody = Utility.el("detailModalBody");
    const domFooter = Utility.el("detailModalButtons");
    let domTitle = Utility.el("detailModalLabel");

    domTitle.innerHTML = "";
    domBody.innerHTML = "";
    domFooter.innerHTML = "";

    domTitle.textContent = `New Delivery Area`;

    domBody.innerHTML = `
      <form class="row" id="newDeliveryArea">
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="muted">City name</label>
                        <input type="text" id="dishName" name="city" placeholder="eg: New Haven">
                    </div>
                    <div class="form-group">
                      <label class="muted">Delivery Price</label>
                        <input type="number" id="delivery_price"  name="delivery_price" placeholder="Eg: 2000">
                    </div>
                  
                </div>
                <div class="col-sm-6">                  
                    <p class="muted mt-2">By clicking on the submit button, you will make changes to the zone information.</p>
                    <button class="btn btn-primary mt-2" type="submit">Save Changes</button>
                </div>
            </div>
        </div>
      </form>
    `;
    $("#displayDetails").modal("show");
  }

}