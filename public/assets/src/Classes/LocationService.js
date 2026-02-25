import { HttpRequest } from "../Utils/httpRequest.js"
import { CONFIG } from "../Utils/config.js";
import Utility from "./Utility.js";
import Cart from "./Cart.js";
import Checkout from "./Checkout.js";

export default class LocationService {

    static DELIVERY_AREAS = [];
    static LOCATION_DATA = [];

    static async  geolocationService(){
        try {
            const position = await new Promise((resolve, reject)=>{
                navigator.geolocation.getCurrentPosition(resolve, reject);
            })

            const lat = position.coords.latitude;
            const lon = position.coords.longitude;

            const response = await HttpRequest(
                `${CONFIG.API}/reverse-geocode`,
                { lat, lon },
                "POST"
            );
            return response;
        } catch (error) {
            Utility.toast(error);
            return null;
        }
    }

    
    static async fetchDeliveryAreas() {
        try {
            const response = await fetch(`${CONFIG.API}/reverse-geocode`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ latitude: lat, longitude: lon })
            });
            if (response.success) LocationService.DELIVERY_AREAS = response.data
        } catch (error) {
            Utility.toast(error);
        }
    }

    static appendDeliveryAreas(domElement){
        if (!domElement) return

            Cart.DELIVERY_AREAS.forEach(area => {
                const option = document.createElement("option");
                option.value = area.id;
                option.textContent = area.name;
                domElement.appendChild(option);
            });

    }

    static renderManualLocation(el){        
        
        const areas = Cart.DELIVERY_AREAS.map((p, idx) => {
            const selected = p.idx === 0 ? "selected" : "";
            return `<option value="${p.city}" ${selected} data-price="${p.delivery_price}">${p.city}</option>`;
        });
    
        el.innerHTML = `
            <label class="form-label small">Select Delivery Area</label>
            <select class="select-tags" id="manual-locations-select">
            <option value="">-- Select Area --</option>     
            ${areas}
            </select>
        `;
    }

    static handleManualLocationChange(){
        const locationSelect = Utility.el("manual-locations-select");

        function updateDeliveryPrice(currentArea) {
            const area = Cart.DELIVERY_AREAS.find(
                (a) =>
                currentArea &&
                currentArea.toLowerCase().includes(a.city.toLowerCase())
            );

            if (area) {
                Cart.deliveryFeedback.innerHTML = `<div class="p-2">🚚 Delivery to <strong>${
                    area.city.toUpperCase()
                }</strong></div>`;
                Cart.DELIVERY_BASE = Number(area.delivery_price);
                Cart.deliveryArea = area.city;
            } else {
                if (currentArea == '') {
                    Cart.deliveryFeedback.innerHTML = `<div class="p-2">Please select a delivery area to see the delivery fee.</div>`;
                    Cart.DELIVERY_BASE = 0;
                    Cart.deliveryArea = null;
                    Checkout.renderCart();
                    return;
                }
                Cart.deliveryFeedback.innerHTML = `<div>An error has occurred. Please Call or chat the Administrator to queue your order.</div>`;
                Cart.ORDERBTN ? Cart.ORDERBTN.disabled = true : null;
                return;
            }
            
            Cart.ORDERBTN ? Cart.ORDERBTN.disabled = false : null;
            Checkout.renderCart();
        }

        locationSelect.addEventListener("change", (e) => {
            updateDeliveryPrice(e.target.value);
        });

        updateDeliveryPrice(locationSelect.value);
    }
      
}