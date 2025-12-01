import Category from "../Classes/Category";
import Utility from "../Classes/Utility";
import { deleteItem, postItem, putItem } from "../Utils/CrudRequest";

class CategoryPage {
    constructor() {
        this.initialize();
    }

    async initialize() {
        await Category.loadCategories();       
        Utility.runClassMethods(this, ["initialize"]);
    }

    bindEvents() {
        const categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));
        const categoryForm = document.getElementById('categoryForm');
       

        document.getElementById('addCategoryBtn')?.addEventListener('click', () => {
            Category.currentAction = 'add';
            document.getElementById('categoryModalTitle').textContent = 'Add Category';
            categoryForm.reset();
            document.getElementById('categoryId').value = '';
            categoryModal?.show();
        }); 
    }

    // categoryFormSelect(){
    //      const selectDom = document.getElementById('category_id');
    //      if(!selectDom) return;
    //      selectDom.innerHTML = '<option value="">Select category</option>';

    //     Category.CATEGORIES.map(cat => {
    //         const option = document.createElement('option');
    //         option.value = cat.id;
    //         option.textContent = cat.name;
    //         selectDom.appendChild(option);
    //     });
    // }

    categoryEventDelegations(){
        const categoryTableBody = document.querySelector('#categoriesTable tbody');
        const categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));

        /**!SECTION Click Events */
        categoryTableBody.addEventListener('click', async (e) => {
            const action = e.target.getAttribute('data-action');
            const id = e.target.getAttribute('data-id');
            if (!action || !id) return;

            if (action === 'edit') {
                Category.currentAction = 'edit';
                document.getElementById('categoryModalTitle').textContent = 'Edit Category';
                try {
                   const data = Category.CATEGORIES.find(cat => cat.id == id);
                   if (data) {
                        document.getElementById('categoryId').value = data.id;
                        document.getElementById('categoryName').value = data.name;
                        categoryModal.show();
                    } else {
                        Utility.toast('Category not found.');
                    }
                } catch (err) {
                    console.error(err);
                    Utility.toast('Failed to fetch category.');
                }
            }

            if (action === 'delete') {
                const del = await deleteItem(`admin/categories/${id}`, 'Delete Category?');
                 if (del) {
                    Utility.toast('Category deleted successfully.');
                    Category.loadCategories();
                } else {
                    Utility.toast('Failed to delete category.');
                }
               
            }
        });

        /**!SECTION Submit Events */
         document.getElementById("categoryForm").addEventListener("submit", async (e) => {
            e.preventDefault();
             const categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));
             

             const name = document.getElementById('categoryName').value.trim();
             const id = document.getElementById('categoryId').value;

             if (!name) return Utility.toast('Category name is required.');

               $("#categoryModal").modal("hide");

             const url = Category.currentAction === 'add' ? 'admin/categories' : `admin/categories/${id}`;
             const formSubmit = Category.currentAction === 'add' ? await postItem(url, {name}) : await putItem(url, {name});

             if (formSubmit) {
                Utility.toast(`Category ${Category.currentAction === 'add' ? 'added' : 'updated'} successfully.`);              
                Category.loadCategories();
             } else {
                Utility.toast(`Failed to ${Category.currentAction === 'add' ? 'add' : 'update'} category.`);
             }
         })

        
    }

   
}

new CategoryPage();