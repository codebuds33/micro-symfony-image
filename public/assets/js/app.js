class clearLogs {
  buttons;

  constructor() {
    this.url = document.getElementById('clear-button-container').dataset.url

    this.buttons = document.querySelectorAll('button.clear-data-button')
    this.addEventListenersToButtons()
  }

  addEventListenersToButtons() {
    this.buttons.forEach((button) => {
      button.addEventListener('click', () => {
        this.callClearRoute(button.dataset.type)
      })
    })
  }

  async callClearRoute(type) {
    const data = new FormData();
    data.append('type', type);
    const response = await fetch(this.url, {
      method: 'POST',
      body: data
    })

    this.renderLogs(await response.json())
  }

  async renderLogs(logData) {
    for (const [key, value] of Object.entries(logData)) {
      const container = document.getElementById(key);
      container.innerHTML = value
    }
  }
}

class collapsibles {

  constructor() {
    this.collapsibles = document.querySelectorAll(".collapsible");
    this.addCollapseListeners();
  }

  addCollapseListeners() {
    this.collapsibles.forEach((element) => {
      element.addEventListener("click", () => {
        element.classList.toggle("active");
        let content = element.nextElementSibling;
        console.log(content.scrollHeight)
        if (content.style.maxHeight){
          content.style.maxHeight = null;
        } else {
          content.style.maxHeight = content.scrollHeight + "px";
        }
      });
    })
  }
}

document.addEventListener('DOMContentLoaded', function (event) {
  new clearLogs();
  new collapsibles();
})
