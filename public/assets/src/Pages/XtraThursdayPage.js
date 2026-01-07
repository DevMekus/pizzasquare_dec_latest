import { deleteItem, getItem, postItem } from "../Utils/CrudRequest.js";
import Utility from "../Classes/Utility.js";
import Product from "../Classes/Product.js";

class XtraThursdayPage{
     constructor() {
        this.initialize();
    }
    
    async initialize() {
        Product.PRODUCTS = await getItem('pizzas-with-sizes');  
        Product.XTRATHURSDAYPRODUCTS = await Product.packageXLandM(Product.PRODUCTS) 
        console.log(Product.XTRATHURSDAYPRODUCTS['m'])
        Utility.runClassMethods(this, ["initialize"]);
    }

    displayXtraThursdayPromo(){
        const products = Product.xtraThursdayProducts(Product.XTRATHURSDAYPRODUCTS['xl']);
    }
}

new XtraThursdayPage();