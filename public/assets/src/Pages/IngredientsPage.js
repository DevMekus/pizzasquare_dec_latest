import Utility from "../Classes/Utility.js";
import Product from "../Classes/Product.js";
import Category from "../Classes/Category.js";
import { getItem, postItem, deleteItem, putItem } from "../Utils/CrudRequest.js";

class IngredientsPage{
    constructor() {      
        this.initialize();
    }
    
    async initialize() {      
        Product.INGREDIENTS = await getItem('admin/products/ingredients');
        await Product.loadProducts();
        Utility.runClassMethods(this, ["initialize"]);      
    }

    loadAllIngredients(){
        const domEl = document.querySelector("#ingredientsTable tbody");
        if(domEl) Product.loadIngredientsTable(Product.INGREDIENTS);
    }

    openAddIngredientModal(){
        const addIngredientBtn = document.querySelector("#addIngredient");
        if(addIngredientBtn){
            
            addIngredientBtn.addEventListener("click", async () => {              
                await Category.loadCategories()
                Category.categoryFormSelect();
                $("#ingredientModal").modal("show");
               
            })
        }

        document.querySelector("#ingredientForm").addEventListener("submit", async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());
            $("#ingredientModal").modal("hide");
           
            const postAction = await postItem('admin/products/ingredients/create', data, "Add Ingredient?");
            
            if(postAction){
                Product.INGREDIENTS = await getItem('admin/products/ingredients');
                Product.loadIngredientsTable(Product.INGREDIENTS);
                Utility.toast("Ingredient added successfully", "success");               
                e.target.reset();
            } else {
                Utility.toast("Failed to add ingredient", "error");
            }
        });
    }

 

    assignIngredientToProduct(){
        const assignBtn = document.querySelector("#assignIngredient");
        if(assignBtn){
            assignBtn.addEventListener("click", async () => {                
                Category.CATEGORIES = await getItem('categories');   
               Product.assignIngredientsModal()
               //show ingredients based on the selected category
               
            })
        }
    }

    async loadProductSelectOptions(){
        const productSelectDom = Utility.el("productSelectionDom");
        if(productSelectDom){           
            productSelectDom.innerHTML = `
                <select id="productSelectForIngredients">
                    <option value="">-- Select Product --</option>
                    ${Product.PRODUCTS.map(p => `<option value="${p.id}">${Utility.toTitleCase(p.name)}</option>`).join("")}
                </select>
            `;
             
            const productSelectForIngredients = document.querySelector("#productSelectForIngredients");
            await Product.loadAssignedIngredientsTable(productSelectForIngredients.value);

            productSelectForIngredients.addEventListener("change", async (e) => {
                const productId = e.target.value;
                if(productId){
                   await Product.loadAssignedIngredientsTable(productId);
                   
                }
            });
        }
    }





       IngredientsEventDelegate(){
       document.querySelector("#ingredientsTable tbody").addEventListener("click", async (e) => {
            const action = e.target.dataset.action
            const id = e.target.dataset.id;

            const ingredient = Product.INGREDIENTS.find(ing => ing.id == id);
            if(!ingredient) {
                Utility.toast("Ingredient not found", "error");
                return;
            }

             if(action === "edit-ingredient"){
                await Category.loadCategories()
                Category.categoryFormSelect();

                document.querySelector("#ingredientNameInput").value = ingredient.ingredient_name;
                document.querySelector("#category_id").value = ingredient.category_id;
                document.querySelector("#ingredientForm").id = 'editingredientForm';              
               
                $("#ingredientModal").modal("show");

                document.querySelector("#editingredientForm").addEventListener("submit", async (e) => {
                    e.preventDefault();
                    const formData = new FormData(e.target);
                    const data = Object.fromEntries(formData.entries());
                    $("#ingredientModal").modal("hide");
                    const postAction = await putItem(`admin/products/ingredients/${id}`, data, `Update "${ingredient.ingredient_name}"?`);
                    if(postAction){
                        Utility.toast("Ingredient updated successfully", "success");
                       setTimeout(()=>{
                         Utility.reloadPage();
                       }, 800)
                    } else {
                        Utility.toast("Failed to update ingredient", "error");
                    }
                });
             }



            if(action === "delete-ingredient"){
               const deleteAction = await deleteItem(`admin/products/ingredients/${id}`, `Delete "${ingredient.ingredient_name}"?`);
                if(deleteAction){
                    Product.INGREDIENTS = await getItem('admin/products/ingredients');
                    Product.loadIngredientsTable(Product.INGREDIENTS);
                    Utility.toast("Ingredient deleted successfully", "success");
                } else {
                    Utility.toast("Failed to delete ingredient", "error");
                }
            }
      
       });

       document.querySelector("#assignedIngredientsTable tbody").addEventListener("click", async (e) => {
            const action = e.target.dataset.action
            const id = e.target.dataset.id;

            const ingredient = Product.ASSIGNED_INGREDIENTS.find(ing => ing.id == id);
                if(!ingredient) {
                    Utility.toast("Ingredient not found", "error");
                    return;
                }


            if(action === "delete"){               
                const deleteAction = await deleteItem(`admin/products/ingredients/unassign/${id}`, `Remove "${ingredient.ingredient_name}" from product?`);
                if(deleteAction){
                    Utility.toast("Ingredient removed from product successfully", "success");
                    setTimeout(async ()=>{
                       Utility.reloadPage();
                    }, 800)
                } else {
                    Utility.toast("Failed to remove ingredient from product", "error");
                }
            }
       });
    }


}

new IngredientsPage();