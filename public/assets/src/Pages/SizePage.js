import Sizes from "../Classes/Sizes";
import Utility from "../Classes/Utility";
import Category from "../Classes/Category";
import { deleteItem, postItem, putItem } from "../Utils/CrudRequest";

class SizePage {
    constructor() {
        this.initialize();
        this.sizeModal = new bootstrap.Modal(document.getElementById('sizeModal'));
    }

    async initialize() {       
        await Sizes.loadSizes();
        await Category.loadCategories();
        Utility.runClassMethods(this, ["initialize"]);
    }

    categoryFormSelect(){
        Category.categoryFormSelect()
    }

    sizeFormSubmit(){
        Sizes.sizeForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const id = document.getElementById('sizeId').value;           
            const data  = Utility.toObject(new FormData(e.target));

            //code
            data.code = data.label.toLowerCase().replace(/\s+/g, '-');

            if (!data.label || !data.ordering) {                 
                 Utility.SweetAlertResponse({success:false, message: 'Please enter name and ordering.'});
                 return;
            }

            try {
                
                const url = id ? `admin/sizes/${id}` : 'admin/sizes';
                this.sizeModal.hide();
                const result = id ? await putItem(url, data) : await postItem(url, data);
                
              
                if (result) {                  
                    Sizes.loadSizes();
                    Sizes.sizeForm.reset();
                    document.getElementById('sizeModalTitle').textContent = 'Add New Size';
                    document.getElementById('sizeId').value = '';
                } else {                  
                    Utility.SweetAlertResponse({success:false, message: 'Failed to save size.'});
                }
            } catch (err) {
                console.error(err);
                 Utility.SweetAlertResponse({success:false, message: 'Error saving size.'});
                
            }
        });
    }

    sizeEventDelegations(){
        Sizes.sizesTableBody.addEventListener('click', async (e) => {
            const action = e.target.getAttribute('data-action');
            const id = e.target.getAttribute('data-id');
            if (!action || !id) return;

            if (action === 'edit') {              
                const data = Sizes.SIZES.find(size => size.id == id);
                if (data) {
                    document.getElementById('sizeId').value = data.id;
                    document.getElementById('sizeName').value = data.label;
                    document.getElementById('ordering').value = data.ordering;
                    document.getElementById('category_id').value = data.category_id;
                    document.getElementById('sizeModalTitle').textContent = 'Edit Size';
                    this.sizeModal.show();
                } else {
                    Utility.toast('Size not found.');
                }
            }

            if (action === 'delete') {

                const del = await deleteItem(`admin/sizes/${id}`, 'Delete Size?');
                    
                if (del) {
                    Utility.toast('Size deleted successfully.');
                    Sizes.loadSizes();
                } else {
                    Utility.toast('Failed to delete size.');
                } 
                
            }
        });
    }

   
}

new SizePage();