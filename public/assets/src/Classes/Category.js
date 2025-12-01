import {getItem} from '../Utils/CrudRequest.js'
import Pagination from './Pagination.js';

export default class Category {
    static CATEGORIES = []  
    static currentAction = ''

    static async loadCategories() {
        const categories = await getItem('categories');       
         this.CATEGORIES = categories;    
        this.renderCategories(categories);
    }
    
    static renderCategories(categories, page = 1){
        
        const categoryTableBody = document.querySelector('#categoriesTable tbody');
        if (!categoryTableBody) return;

        categoryTableBody.innerHTML = '';
        categories.forEach((cat,i) => {
            categoryTableBody.innerHTML += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${cat.name}</td>
                    <td>${cat.slug}</td>                   
                    <td>${cat.created_at}</td>                   
                    <td>${cat.updated_at}</td>                   
                    <td>
                        <button class="btn btn-sm btn-primary" data-action="edit" data-id="${cat.id}">Edit</button>
                        <button class="btn btn-sm btn-outline-error" data-action="delete" data-id="${cat.id}">Delete</button>
                    </td>
                </tr>
            `;
        });
    };

    static categoryFormSelect(){
         const selectDom = document.getElementById('category_id');
         if(!selectDom) return;
         selectDom.innerHTML = '<option value="">Select category</option>';

        Category.CATEGORIES.map(cat => {
            const option = document.createElement('option');
            option.value = cat.id;
            option.textContent = cat.name;
            selectDom.appendChild(option);
        });
    }
}