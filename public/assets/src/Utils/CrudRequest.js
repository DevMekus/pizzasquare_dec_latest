import {CONFIG} from "./config.js"
import {HttpRequest} from "./httpRequest.js"
import Utility from "../Classes/Utility.js"

export async function getItem(route){
    try {
        const response = await HttpRequest(`${CONFIG.API}/${route}`)
        return response.success ? response.data : []
    } catch (error) {
        Utility.toast("An error has occurred")
    }
}

export async function postItem(route, data, message = "Add item?", showResponse = true){
    try {
         const result = await Utility.confirm(message);
         if (result.isConfirmed){
                Utility.alertLoader("Uploading data...")
                const response = await HttpRequest(
                    `${CONFIG.API}/${route}`,
                    data,
                    "POST"
                );

                Utility.clearAlertLoader()
                if (showResponse) {
                    Utility.SweetAlertResponse(response);
                }
                console.log(response);
                return response.success
         } else {
             Utility.toast("Action cancelled");
         }
    } catch (error) {
        Utility.toast("An error has occurred")
    }
}

export async function putItem(route, data, message = "Update item?", showResponse = true){
    try {
        const result = await Utility.confirm(message);
         if (result.isConfirmed){

                Utility.alertLoader("Updating item...")
               
                const response = await HttpRequest(
                    `${CONFIG.API}/${route}`,
                    data,
                    "PUT"
                );
                console.log(response);

                Utility.clearAlertLoader()
                if (showResponse) {
                    Utility.SweetAlertResponse(response);
                }
                return response.success
         } else {
             Utility.toast("Action cancelled");
         }
        
    } catch (error) {
         Utility.toast("An error has occurred")
    }
}

export async function patchItem(route, data, message = "", showResponse = true){
    try {
        const result = await Utility.confirm(message);
         if (result.isConfirmed){
            
                Utility.alertLoader("Updating item...")
               
                const response = await HttpRequest(
                    `${CONFIG.API}/${route}`,
                    data,
                    "PATCH"
                );

                Utility.clearAlertLoader()
                if (showResponse) {
                    Utility.SweetAlertResponse(response);
                }
                return response.success
         } else {
             Utility.toast("Action cancelled");
         }
        
    } catch (error) {
         Utility.toast("An error has occurred")
    }
}

export async function deleteItem(route, message = "Delete item?", showResponse = true){
    try {
        const result = await Utility.confirm(message);
         if (result.isConfirmed){
            
                Utility.alertLoader("Deleting item...")
               
                const response = await HttpRequest(
                    `${CONFIG.API}/${route}`,
                    {},
                    "DELETE"
                );

                Utility.clearAlertLoader()
                if (showResponse) {
                    Utility.SweetAlertResponse(response);
                }
                return response.success
         } else {
             Utility.toast("Action cancelled");
         }
        
    } catch (error) {
         Utility.toast("An error has occurred")
    }
}

