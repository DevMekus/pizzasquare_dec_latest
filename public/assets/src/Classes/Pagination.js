import Utility from "./Utility.js";

export default class Pagination {
  static render(
    totalItems,
    page,
    data,
    classMethod,
    containerId = "pagination"
  ) {
    const totalPages = Math.ceil(totalItems / Utility.PAGESIZE);
    const container = Utility.el(containerId); // ✅ dynamic container

    container.innerHTML = "";

    const createButton = (label, pageNum, disabled = false, active = false) => {
      const btn = document.createElement("button");
      btn.textContent = label;
      btn.disabled = disabled;
      if (active) btn.classList.add("active");

      btn.addEventListener("click", () => {
        Utility.CURRENTPAGE = pageNum;
        classMethod(data, Utility.CURRENTPAGE, containerId); // ✅ keep context
      });

      return btn;
    };

    // First + Prev
    container.appendChild(createButton("⏮", 1, page === 1));
    container.appendChild(createButton("◀", page - 1, page === 1));

    // Page numbers with ellipsis
    let start = Math.max(1, page - 2);
    let end = Math.min(totalPages, page + 2);

    if (start > 1) {
      container.appendChild(createButton("1", 1));
      if (start > 2) container.appendChild(Pagination.ellipsis());
    }

    for (let i = start; i <= end; i++) {
      container.appendChild(createButton(i, i, false, i === page));
    }

    if (end < totalPages) {
      if (end < totalPages - 1) container.appendChild(Pagination.ellipsis());
      container.appendChild(createButton(totalPages, totalPages));
    }

    // Next + Last
    container.appendChild(createButton("▶", page + 1, page === totalPages));
    container.appendChild(createButton("⏭", totalPages, page === totalPages));
  }

  static ellipsis() {
    const span = document.createElement("span");
    span.textContent = "...";
    span.classList.add("ellipsis");
    return span;
  }
}
