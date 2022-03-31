import collapseActions from "./components/collapseActions";
import cleanActions from "./components/cleanActions";
import "../styles/app.scss"

document.addEventListener('DOMContentLoaded', function (event) {
    new collapseActions();
    new cleanActions();
})
