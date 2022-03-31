export default class cleanActions {
    buttons;
    url;

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
            const container = document.getElementById(key) as HTMLElement;
            container.innerHTML = value as string
        }
    }
}
