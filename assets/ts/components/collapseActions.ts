export default class collapseActions {

    collapsibles;

    constructor() {
        this.collapsibles = document.querySelectorAll(".collapsible");
        this.addCollapseListeners();
    }

    addCollapseListeners() {
        this.collapsibles.forEach((element) => {
            element.addEventListener("click", () => {
                element.classList.toggle("active");
                let content = element.nextElementSibling;
                if (content.style.maxHeight){
                    content.style.maxHeight = null;
                } else {
                    content.style.maxHeight = content.scrollHeight + "px";
                }
            });
        })
    }
}
