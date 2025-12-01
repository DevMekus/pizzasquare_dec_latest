import {getItem} from '../Utils/CrudRequest.js'
import Pagination from './Pagination.js';
import Utility from '../Classes/Utility.js';

export default class Sizes {
    static SIZES = []
    static currentAction = ''
    static sizesTableBody = document.querySelector('#sizesTable tbody');    
    static sizeForm = document.getElementById('sizeForm');
    static pagination = Utility.el("pagination");
    
    static async loadSizes() {
        const sizes = await getItem('sizes');       
         Sizes.SIZES = sizes;    
        Sizes.renderSizes(sizes);
    }

    static renderSizes(sizes, page = 1) {
        
        if (!Sizes.sizesTableBody) return;

        const start = (page - 1) * Utility.PAGESIZE;
        const end = start + Utility.PAGESIZE;

        if (!sizes || sizes.length === 0) {
           Utility.renderEmptyState(Utility.NODATA)
           return;
        }

         const paginatedData = sizes.slice(start, end);

        Sizes.sizesTableBody.innerHTML = '';
        paginatedData.forEach((size, i) => {
            Sizes.sizesTableBody.innerHTML += `
                <tr class="align-middle bounce-card">
                    <td>${i + 1}</td>
                    <td>${size.label}</td>
                    <td>${size.category}</td>
                    <td>${size.code}</td>
                    <td>${size.ordering}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" data-action="edit" data-id="${size.id}">Edit</button>
                        <button class="btn btn-sm btn-outline-danger" data-action="delete" data-id="${size.id}">Delete</button>
                    </td>
                </tr>
            `;
        });
        if (sizes.length > Utility.PAGESIZE)
            Pagination.render(sizes.length, page, sizes, Sizes.renderSizes);
    };
 
}