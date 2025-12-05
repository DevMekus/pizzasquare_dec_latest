import Order from "../Classes/Order.js";
import Utility from "../Classes/Utility.js";
import Report from "../Classes/Report.js";
import { postItem, getItem, deleteItem, patchItem } from "../Utils/CrudRequest.js";


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