import {getItem, postItem} from '../Utils/CrudRequest.js'
import Pagination from './Pagination.js';
import Utility from './Utility.js';
import Category from './Category.js';
import Cart from './Cart.js';
import { CONFIG } from '../Utils/config.js';

export default class Promotions {
    static XTRATHURSDAYPRODUCTS = [] 
    static PROMOTIONS = []

    // Promotions Table

    static async loadPromotions() {
        const promotions = await getItem('promotions');       
         this.PROMOTIONS = promotions;    
        this.renderPromotions(promotions);
    }

    static renderPromotions(promotions, page = 1){
        
        const promotionsTableBody = document.querySelector('#promotions-table tbody');
        if (!promotionsTableBody) return;

        promotionsTableBody.innerHTML = '';
        
        if(promotions.length === 0){
            promotionsTableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center">No promotions found.</td>
                </tr>
            `;
            return;
        }

      
        promotions.forEach((promo,i) => {
            promotionsTableBody.innerHTML += `
                <tr>    
                    <td>${i + 1}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="${promo.banner}" alt="${promo.title}" class="table-img mr-2 avatar"/>
                            ${promo.title}
                        </div>
                    </td>
                    <td>${Utility.toTitleCase(promo.active_day)}</td>
                    <td>${promo.created_at}</td>                   
                    <td>
                        <span class="badge ${promo.status == 'active' ? 'success' : 'muted'}">${Utility.toTitleCase(promo.status)}</span>
                    </td>                   
                    <td>
                        <button class="btn btn-sm btn-primary" data-action="edit" data-id="${promo.id}">Edit</button>
                        <button class="btn btn-sm btn-outline-error" data-action="delete" data-id="${promo.id}">Delete</button>
                    </td>
                </tr>
            `;
        });
    }

    static addpromotionModal() {
        let domBody = Utility.el("detailModalBody");
        const domFooter = Utility.el("detailModalButtons");
        let domTitle = Utility.el("detailModalLabel");
        
        domTitle.innerHTML = "";
        domBody.innerHTML = "";
        domFooter.innerHTML = "";
        domTitle.textContent = `New Promotion / Deals`;
        
        domBody.innerHTML = `
            <form class="row"  id="newPromotionForm" enctype="multipart/form-data">
                <div class="container">
              
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" id="title" name="title" placeholder="eg: October Splash">
                                <small class="text-danger">Enter exact promotion name from the developer!</small>
                            </div>
    
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea name="description" id="description" maxlength="100" placeholder="Write briefly about Deal / Promotion"></textarea>
                                <small id="charCount">0 / 100</small>
                            </div>
                            <div class="form-group">
                                <label for="title">Active Day(s) </label>
                                <input type="text" id="title" name="active_day" placeholder="eg: Monday or Monday, Tuesday, ...">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="image-box" id="preview"></div>
                            <div>
                                <label class="muted mt-2">Promotion Banner</label>
                                <input type="file" id="promoBanner" name="promoBanner" accepts="image/*" placeholder="Upload Images">
                            </div>
                            <p class="muted mt-2">By clicking on the submit button, you will make upload this information.</p>
                            <button class="btn btn-md btn-primary mt-2" type="submit">Create Deal</button>
                        </div>
                    </div>
                </div>
            </form>
            `;
        $("#displayDetails").modal("show");
        Promotions.runCounter();
        const promoBanner = document.getElementById("promoBanner");
        const preview = document.getElementById("preview");
        promoBanner.addEventListener("change", function () {
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

   static EditPromotionModal(promotion) {
        let domBody = Utility.el("detailModalBody");
        const domFooter = Utility.el("detailModalButtons");
        let domTitle = Utility.el("detailModalLabel");
        
        domTitle.innerHTML = "";
        domBody.innerHTML = "";
        domFooter.innerHTML = "";
        domTitle.textContent = `New Promotion / Deals`;
        
        domBody.innerHTML = `
            <form class="row"  id="editPromotionForm" data-id="${promotion.id}" enctype="multipart/form-data">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" id="title" name="title" placeholder="eg: October Splash" value="${promotion.title}">
                            </div>
    
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea name="description" id="description" maxlength="100" placeholder="Write briefly about Deal / Promotion">${promotion.description}</textarea>
                                <small id="charCount">0 / 100</small>
                            </div>
                            <div class="form-group">
                                <label for="title">Active Day</label>
                                <input type="text" id="title" name="active_day" placeholder="eg: Monday, Tuesday..." value="${promotion.active_day}">
                            </div>
                             <div class="form-group">
                                <label for="title">Status</label>
                                <select name="status" id="status">
                                    <option value="active" ${promotion.status === 'active' ? 'selected' : ''}>Active</option>
                                    <option value="inactive" ${promotion.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="image-box" id="preview"><img src="${promotion.banner}" style="width: 100%; height: 250px; object-fit: cover;"></div>   
                            <div>
                                <label class="muted mt-2">Promotion Banner</label>
                                <input type="file" id="promoBanner" name="promoBanner" accepts="image/*" placeholder="Upload Images">
                            </div>
                            <p class="muted mt-2">By clicking on the submit button, you will make upload this information.</p>
                            <button class="btn btn-md btn-primary mt-2" type="submit">Edit Deal</button>
                        </div>
                    </div>
                </div>
            </form>
            `;
        $("#displayDetails").modal("show");
        Promotions.runCounter();
        const promoBanner = document.getElementById("promoBanner");
        const preview = document.getElementById("preview");
        promoBanner.addEventListener("change", function () {
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

    static promotionCards(promotions){
        const dealsCardContainer = document.querySelector('#pizzaSquareDealsRow');
        if (!dealsCardContainer) return;

        dealsCardContainer.innerHTML = '';
        promotions.forEach((promo, i) => {
            dealsCardContainer.innerHTML += `
                <div class="col-md-4 mb-4 cursor-pointer"
                 aos="fade-up" data-aos-delay="${i * 100}"
                 data-aos-duration="800"
                 data-aos-easing="ease-in-out"                
                 >
                    <div class="card">
                        <img src="${promo.banner}" class="card-img-top" alt="${promo.title}"> 
                        <div class="card-body">
                            <div class="d-flex flex-column justify-content-center align-items-center">
                                <button type="button" 
                                    data-title="${promo.title}"
                                    data-active_day="${promo.active_day}"
                                    data-status="${promo.status}"
                                    data-description="${promo.description}"
                                    data-id="${promo.id}"
                                    data-code="${promo.code}" 
                                    class="btn btn-sm btn-primary mt-2 center-mobile w-50">
                                    View Deal
                                </button>
                            </div>
                        </div>                      
                    </div>
                </div>
            `;
        });

        //Event delegation for deal cards
        dealsCardContainer.addEventListener('click', (e) => {
            const dealButton = e.target.closest('button');
            if (dealButton) {
                const isActive = dealButton.dataset.status === 'active' ? true : false;
                const today = new Date().toLocaleString('en-US', { weekday: 'long' }).toLowerCase();
 
                const activeDay = dealButton.dataset.active_day.toLowerCase();
                const isActiveToday = activeDay.includes(today);
                const promoCode = dealButton.dataset.code

                if(!isActiveToday){
                        Swal.fire({
                        title: "Promotion Not Active Today",
                        text: `This promotion is only active on ${Utility.toTitleCase(activeDay)}.`,
                        icon:  "info",
                        confirmButtonColor: "#d51d28",
                    });                                   
                    return;
                }

                if(!isActive){
                     Swal.fire({
                        title: "Promotion Unavailable",
                        text: "This promotion is currently unavailable at the moment.",
                        icon:  "error",
                        confirmButtonColor: "#d51d28",
                    });                                   
                    return;
                }

                //check if the url contains pos or not
                const isPos = window.location.href.includes('/pos/');

                if(isPos){
                    window.location.href = `${CONFIG.BASE_URL}/secure/pos/promo/promo-day?id=${promoCode}`;
                } else {
                    window.location.href = `${CONFIG.BASE_URL}/promo/promo-day?id=${promoCode}`;
                }

             
                
                
            }
        });
    }

    
    
}