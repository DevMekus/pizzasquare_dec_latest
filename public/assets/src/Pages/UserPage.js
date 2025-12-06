import Utility from "../Classes/Utility.js";
import User from "../Classes/User.js";
import { deleteItem, getItem, patchItem, postItem, putItem } from "../Utils/CrudRequest.js";

class UserPage {
    constructor() {
        this.initialize();
    }

    async initialize() {
        User.USERS = await getItem("admin/users");
        Utility.runClassMethods(this, ["initialize"]);   
    }

    renderUserTable() {
        const domEl = Utility.el("usersTable");
        if (!domEl) return;
        User.renderUserTable(User.USERS);
    }

    searchUserData() {
        let timeout;
        document.getElementById("searchUsers")?.addEventListener("input", (e) => {
        const query = e.target.value.toLowerCase();

        const filtered = User.USERS.filter((u) => {
            const name = (u.fullname ?? "").toLowerCase();
            const email = (u.email_address ?? "").toLowerCase();
            const phone = (u.phone ?? "")
            return name.includes(query) || email.includes(query) || phone.includes(query);
        });

        clearTimeout(timeout);
        timeout = setTimeout(() => {
            User.renderUserTable(filtered.length > 0 ? filtered : User.USERS);
        }, 100);
        });
    }

    addUserbtn() {
        const domEl = Utility.el("addUser");
        if (!domEl) return;
        domEl.addEventListener("click", User.addUserForm);
    }

    eventDelegations() {
        //--Add user
        document.addEventListener("submit", async (e) => {
            if (e.target && e.target.matches && e.target.matches("#addUser")) {
                e.preventDefault();
                const formD = e.target;
                $("#displayDetails").modal("hide");
                const send = await postItem("admin/users", new FormData(formD), "Add new user?");
                if (send){
                    User.USERS = await getItem("admin/users");
                    User.renderUserTable(User.USERS);
                } else {
                    Utility.toast("Failed to add user", "error");
                }
            
            }
        });

        //--Delete user
        document.addEventListener("click", async (e) => {
        const deleteBtn = e.target.closest("[data-del-user]");
            if (deleteBtn) {
                const id = deleteBtn.dataset.delUser;
                $("#displayDetails").modal("hide");
                const del = await deleteItem(`admin/users/${id}`, "Delete user account?");
                if (del){
                   setTimeout(() => {
                        Utility.reloadPage();
                   }, 1000);
                } else {
                    Utility.toast("Failed to delete user", "error");
                }
            
            }
        });

        //--Open user
        document.addEventListener("click", (e) => {
        const editBtn = e.target.closest("[data-edit-user]");
        if (editBtn) {
            const id = editBtn.dataset.editUser;
            User.manageUser(id);
        }
        });

        //--Edit user
        document.addEventListener("submit", async(e) => {
        if (e.target && e.target.matches && e.target.matches("#editUser")) {
            e.preventDefault();
            $("#displayDetails").modal("hide");
          
            const formData = new FormData(e.target);
            const obj = Utility.toObject(formData);
            const userid = obj.userid;
            const send = await postItem(`users/${userid}`, formData, "Update user?");
            if (send){
                User.USERS = await getItem("admin/users");
                User.renderUserTable(User.USERS);
            } else {
                Utility.toast("Failed to update user", "error");
            }
        }
        });
    }
    
    async renderActivities() {
        const domEl = Utility.el("activityTable");
        if (!domEl) return;
        User.ACTIVITIES = await getItem("admin/log");
        User.renderActivityLog(User.ACTIVITIES);
    }



}

new UserPage();