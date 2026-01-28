import { postItem } from "../Utils/CrudRequest.js";
import Utility from "../Classes/Utility.js";

class ContactPage{
    constructor() {
        this.initialize();
    }
    
    async initialize() {     
        Utility.runClassMethods(this, ["initialize"]);
    }

    guestSendMessage() {
    const domForm = Utility.el("sendMessage");
    if (!domForm) return;

    domForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      try {
        const data = Utility.toObject(new FormData(e.target));
        const send = await postItem(`contact`, data, "Send Message?");
        if (send) {
          Utility.toast("Message has been sent");
          e.target.reset();
        } else Utility.toast("Message failed", "error");
      } catch (error) {
        Utility.toast("An error has occurred", "error");
      }
    });
  }
}

new ContactPage()