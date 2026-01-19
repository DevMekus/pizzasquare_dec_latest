import {  getItem } from "../Utils/CrudRequest.js";
import Utility from "../Classes/Utility.js";
import Product from "../Classes/Product.js";
import XtraThursdayPromo from "../Classes/XtraThursdayPromo.js";

class XtraThursdayPage{
     constructor() {
        this.initialize();
    }
    
    async initialize() {
        Product.PRODUCTS = await getItem('pizzas-with-sizes');  
        XtraThursdayPromo.XTRATHURSDAYPRODUCTS = await Product.packageXLandM(Product.PRODUCTS) 
        Product.EXTRAS = await getItem("extras");
        Utility.runClassMethods(this, ["initialize"]);
    }

    displayXtraThursdayPromo(){
        XtraThursdayPromo.xtraThursdayProducts(XtraThursdayPromo.XTRATHURSDAYPRODUCTS['xl']);
    }
}

new XtraThursdayPage();