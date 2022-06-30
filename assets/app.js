/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

// start the Stimulus application
import './bootstrap';

const $ = require('jquery');
require('bootstrap');
require("datatables");
require("select2");

window.addEventListener("DOMContentLoaded", () => {
    let table = $('.datatable').DataTable({
        pageLength: 25,
    });

    let search = "";
    if (location.href.includes("?search=")) {
        let search = location.href.split("?search=")[1];
        table.search(search).draw();
    }

    $(".select2-single").select2()
    $(".select2-multiple").select2({
        multiple: true,
        closeOnSelect: false
    })
})

let copyBtn = document.getElementsByClassName("copy-on-click");

for (let i = 0; i < copyBtn.length; i++) {
    copyBtn[i].addEventListener("click", () => {
        let value = copyBtn[i].getAttribute("data-value");
        navigator.clipboard.writeText(value);
        addFlash("success", `<b>${value}</b> copié dans le presse papier !`);
    })
}

let notificationBox = document.getElementById("notifications");

function addFlash(type, message) {
    let notification = document.createElement("div");
    notification.setAttribute("class", `alert alert-${type} alert-dismissible show`);
    notification.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`;
    notificationBox.appendChild(notification);
}

let searchBox = document.getElementById("search-website");
let submit = document.getElementById("search-website-submit");
let category = document.getElementById("category");

if(location.href.includes("servers")){
    category.value = "Serveurs";
}

submit.addEventListener("click", () => {
    let search = searchBox.value;
    switch (category.value) {
        case "Serveurs":
            location.href = `/servers?search=${search}`;
            break;
        default:
            location.href = `/websites?search=${search}`;
            break;
    }
})

searchBox.addEventListener("keydown", (e) => {
    if (e.key == "Enter") {
        submit.click();
    }
})

let modalToggles = document.getElementsByClassName("has-modal");

for(let i = 0; i < modalToggles.length; i++){
    modalToggles[i].addEventListener("click", (e) => {
        e.preventDefault();
        let text = modalToggles[i].getAttribute("data-modal-dialog");
        let action = modalToggles[i].getAttribute("href");
        createModal(text, action);
    })
}

function createModal(text, action, title = "Confirmation"){
    let modal = document.createElement("div");
    modal.innerHTML = `
        <div class="modal fade show" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">${title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ${text}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <a href="${action}" type="button" class="btn btn-primary">Confirmer</a>
                    </div>
                </div>
            </div>
        </div>
    `;
    document.body.append(modal);
}