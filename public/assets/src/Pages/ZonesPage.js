import Utility from "../Classes/Utility.js";
import Zones from "../Classes/Zones.js";
import { deleteItem, getItem, patchItem, postItem, putItem } from "../Utils/CrudRequest.js";

class ZonesPage{
    constructor() {
        this.initialize();
    }

    async initialize() {
        Zones.ZONES = await getItem("city");
        Utility.runClassMethods(this, ["initialize"]);
    }
    renderZones() {
        const domEl = Utility.el("zoneTable");
        if (!domEl) return;
        Zones.renderZoneTable(Zones.ZONES);
    }
    
    newZoneModal() {
        const domEl = Utility.el("addZone");
        if (!domEl) return;
        domEl.addEventListener("click", Zones.zoneModal);
    }

    eventDeligation() {
        document
        .getElementById("zoneTable")
        .addEventListener("click", async (e) => {
            const btn = e.target.closest("button");
            if (!btn) return;
            const action = btn.getAttribute("data-action");
            const id = btn.getAttribute("data-id");

            const zone = Zones.ZONES.find((i) => i.id == id);

            if (action === "view") {
            Zones.openDetail(zone);
            } else if (action === "delete") {
                $("#displayDetails").modal("hide")
                const send = await deleteItem(`admin/city/${id}`,"Delete Zone?");
                if (send) {
                    Zones.ZONES = await getItem("city");
                    Zones.renderZoneTable(Zones.ZONES);
                } else {
                    console.error("Error deleting zone");
                    Utility.toast("Error deleting zone", "error");
                }
               
            }
        });

    //--Submit Events
    document.addEventListener("submit", async (e) => {
      if (e.target && e.target.matches && e.target.matches("#updateZone")) {
        e.preventDefault();
        const data = Utility.toObject(new FormData(e.target));
        const id = e.target.dataset.id;
        $("#displayDetails").modal("hide")
        const patch = await patchItem(`admin/city/${id}`, data);
        if (patch) {
          Zones.ZONES = await getItem("city");
          Zones.renderZoneTable(Zones.ZONES);
        } else {
          console.error("Error updating zone");
          Utility.toast("Error updating zone", "error");
        }
      }

      if (
        e.target &&
        e.target.matches &&
        e.target.matches("#newDeliveryArea")
      ) {
        e.preventDefault();
        const data = Utility.toObject(new FormData(e.target));
        $("#displayDetails").modal("hide")
        const create = await postItem("admin/city", data);
        if (create) {
          Zones.ZONES = await getItem("city");
            Zones.renderZoneTable(Zones.ZONES);
        } else {
          console.error("Error creating zone");
          Utility.toast("Error creating zone", "error");
        }

        
      }
    });
  }
}

new ZonesPage()