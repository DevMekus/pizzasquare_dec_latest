import Utility from "./Utility.js";
import Pagination from "./Pagination.js";
import { CONFIG } from "../Utils/config.js";

export default  class User {
    static USERS = [];
    static ROLES = [
        { id: 1, role: "user" },
        { id: 2, role: "manager" },
        { id: 3, role: "cashier" },
        { id: 4, role: "admin" },
    ];
    static STATUS = ["pending", "active", "suspended"];
    static DEFAULT_PASSWORD = "pizzasquare";
    static ACTIVITIES = [];

    static renderSummary() {
    const users = User.USERS;
    document.getElementById("totalUsers").textContent = users.length;

    //--users
    document.getElementById("customers").textContent = users.filter(
      (u) => u.role === "user"
    ).length;

    //--cashiers
    document.getElementById("cashier").textContent = users.filter(
      (u) => u.role === "cashier"
    ).length;

    //--admins
    document.getElementById("admin").textContent = users.filter(
      (u) => u.role === "admin"
    ).length;
  }

   static manageUser(id) {
        const user = User.USERS.find((u) => u.userid == id);

        if (!user) {
        Utility.toast("user profile not seen", "error");
        return;
        }

        let domBody = Utility.el("detailModalBody");
        let domTitle = Utility.el("detailModalLabel");

        domTitle.innerHTML = "";
        domBody.innerHTML = "";

        domTitle.textContent = `Edit Profile: ${user.fullname}`;

        const statusHtml = User.STATUS.map((i) => {
        const selected = i === user.status ? "selected" : "";
        return `<option value="${i}" ${selected}>
            ${Utility.toTitleCase(i)}</option>`;
        }).join("");

    const image = user.avatar ?? "";

    const roleHtml = User.ROLES.map((i) => {
      const selected = i.role === user.role ? "selected" : "";
      return `<option value="${i.id}"${selected}>
      ${Utility.toTitleCase(i.role)}</option>`;
    }).join("");

    domBody.innerHTML = `
          <form class="row" id="editUser" enctype="multipart/form-data">
            <div class="col-sm-6">
               <div class="form-group">
                  <label class="muted">Fullname</label>
                  <input type="text" id="fullname" name="fullname" value="${user.fullname}" placeholder="eg: John Doe">
               </div>
               <div class="form-group">
                <label class="muted">Email address</label>
                  <input type="email" id="email_address" value="${user.email_address}" name="email_address" placeholder="eg: johndoe@gmail.com">
               </div>
               <input type="hidden" value="${user.userid}" name="userid" />
               <div class="form-group">
                <label class="muted">Phone number</label>
                  <input type="tel" id="phone" name="phone" value="${user.phone}" placeholder="eg: 08036762728">
               </div>
                 <div class="form-group">
                  <label class="muted">Status</label>
                   <select name="status">${statusHtml}</select>
                 </div>
               <div class="form-group">
                  <label class="muted">Role</label>
                  <select id="role_id" name="role_id">
                    ${roleHtml}
                  </select>                 
                </div>

            </div>
            <div class="col-sm-6">
                 <div>
                 <div class="image-box" style="background-image: url(${image})">                    
                 </div>
                     <label class="muted mt-2">User image</label>
                    <input type="file" id="profileImage" name="profileImage" accepts="image/*" placeholder="Upload Images">
                </div>
                <p class="muted mt-2">By clicking on the submit button, you will make changes to the product information.</p>
                <button class="btn btn-primary mt-2" type="submit">Submit</button>
            </div>
          </form>
        `;
    $("#displayDetails").modal("show");
  }

   static renderUserTable(data, page = 1) {
    const userTableBody = Utility.el("userTableBody");
    const notDATA = Utility.el("no-data");
    userTableBody.innerHTML = "";
    notDATA.innerHTML = "";

    const start = (page - 1) * Utility.PAGESIZE;
    const end = start + Utility.PAGESIZE;

    if (!data || data.length == 0) {
      Utility.renderEmptyState(notDATA);
      return;
    }

    const paginatedData = data.slice(start, end);

    paginatedData.forEach((u, idx) => {
      const tr = document.createElement("tr");
      const image = u.avatar        
        ?? "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRIwRBD9gNuA2GjcOf6mpL-WuBhJADTWC3QVQ&s";

        tr.classList.add("bounce-card");
        tr.innerHTML = `
        <td>${idx + 1}</td>
        <td>
            <div class="d-flex gap-3 justify-content-between align-items-center">
            <img class="avatar" src="${image}" />
                ${Utility.toTitleCase(u.fullname)}
            </div>
        </td>
        <td>${u.userid}</td>
        <td>${u.email_address}</td>
        <td>${Utility.toTitleCase(u.role)}</td>
        <td><span class="status ${u.status}">
        ${Utility.toTitleCase(u.status)}</span></td>
        <td>
            <button class="btn btn-sm btn-primary" 
            data-edit-user="${u.userid}">Edit</button>
            <a class="btn btn-sm btn-ghost" 
            href="${CONFIG.BASE_URL}/secure/admin/user?userid=${
            u.userid
        }">Manage</a>
            <button class="btn btn-sm btn-ghost" 
            data-del-user="${u.userid}">Delete</button>         
            </td>
        `;
        userTableBody.appendChild(tr);
        });
        User.renderSummary();

    if (paginatedData.length > Utility.PAGESIZE)
      Pagination.render(data.length, page, data, User.renderUserTable);
    }

    static addUserForm() {
    let domBody = Utility.el("detailModalBody");
    let domTitle = Utility.el("detailModalLabel");

    domTitle.innerHTML = "";
    domBody.innerHTML = "";

    domTitle.textContent = `Add New Team Member`;

    const roleHtml = User.ROLES.map(
      (i) => `<option value="${i.id}">${Utility.toTitleCase(i.role)}</option>`
    ).join("");

    domBody.innerHTML = `
          <form class="row" id="addUser" enctype="multipart/form-data">
            <div class="col-sm-6">
               <div class="form-group">
                  <label class="muted">Fullname</label>
                  <input type="text" id="fullname" name="fullname" placeholder="eg: John Doe">
               </div>
               <div class="form-group">
                <label class="muted">Email address</label>
                  <input type="email" id="email_address" name="email_address" placeholder="eg: johndoe@gmail.com">
               </div>
               <div class="form-group">
                <label class="muted">Phone number</label>
                  <input type="tel" id="phone" name="phone" placeholder="eg: 08036762728">
               </div>
                 <div class="form-group">
                  <label class="muted">Default password</label>
                    <input type="password" id="user_password" name="user_password" 
                    value="${User.DEFAULT_PASSWORD}">
                    <small class="muted">Default password is <strong>${User.DEFAULT_PASSWORD}</strong>. You can change it to suit you. The user should be encouraged to change to another.</small>
                 </div>
               <div class="form-group">
                  <label class="muted">Role</label>
                  <select id="role_id" name="role_id">
                    ${roleHtml}
                  </select>                 
                </div>

            </div>
            <div class="col-sm-6">
                 <div>
                 <div class="image-box"></div>
                     <label class="muted mt-2">User image</label>
                    <input type="file" id="profileImage" name="profileImage" accepts="image/*" placeholder="Upload Images">
                </div>
                <p class="muted mt-2">By clicking on the submit button, you will make changes to the product information.</p>
                <button class="btn btn-primary mt-2" type="submit">Submit user</button>
            </div>
          </form>
        `;
    $("#displayDetails").modal("show");
  }

  static renderActivityLog(data, page = 1) {
    const userTableBody = Utility.el("activityTableBody");
    const notDATA = Utility.el("no-data");
    userTableBody.innerHTML = "";
    notDATA.innerHTML = "";

    const start = (page - 1) * Utility.PAGESIZE;
    const end = start + Utility.PAGESIZE;

    if (!data || data.length == 0) {
      Utility.renderEmptyState(notDATA);
      return;
    }

    const paginatedData = data.slice(start, end);

    paginatedData.forEach((u, idx) => {
      const tr = document.createElement("tr");
      tr.classList.add("bounce-card");
      tr.innerHTML = `
      <td>${idx + 1}</td>      
      <td>${u.logid}</td>
      <td>${u.userid}</td>
      <td>${u.fullname ? Utility.toTitleCase(u.fullname) : "NA"}</td>
      <td>${Utility.toTitleCase(u.type)}</td>
      <td>${Utility.toTitleCase(u.title)}</td>
      <td><span class="status ${u.status}">
      ${u.status ? Utility.toTitleCase(u.status) : "N/A"}</span></td>
       <td>${u.period}</td>
       <td>${u.ip}</td>
       <td title="${u.device}">${Utility.truncateText(u.device, 70)}</td>
      `;
      userTableBody.appendChild(tr);
    });

    data.length >= Utility.PAGESIZE &&
      Pagination.render(data.length, page, data, User.renderActivityLog);
  }
    
}