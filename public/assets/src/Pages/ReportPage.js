import Order from "../Classes/Order.js";
import Utility from "../Classes/Utility.js";
import {  getItem } from "../Utils/CrudRequest.js";


class ReportPage {
    constructor() {
        this.initialize();
    }

    async initialize() {
        Order.ORDERS  = await getItem("admin/orders") || [];   
        Utility.runClassMethods(this, ["initialize"]);
    }
}
new ReportPage();