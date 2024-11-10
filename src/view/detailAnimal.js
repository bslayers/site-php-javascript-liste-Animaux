"use strict";

function getAnimalDetails(animalId) {
    let xhr = new XMLHttpRequest();
    let url = "action/json/" + animalId;
    xhr.open("GET",url,true);
    xhr.setRequestHeader("Accept", "application/json");
    xhr.onload = function () {
        if (xhr.status === 200) {
            genererDetails(JSON.parse(xhr.responseText),animalId);
        }
    };
    xhr.send();
}

function genererDetails(reponseXhr,animalId){
    let reponse = Object.keys(reponseXhr);
    let espece = reponse[1];
    let age = reponse[2];
    let details = document.getElementById("details-" + animalId);
    let bouton = document.getElementById("bouton-" + animalId);
    let texteBouton = bouton.innerText;
    if (texteBouton.trim() === 'Détails') {
        bouton.innerHTML = "Cacher les détails";
        details.innerHTML = espece+": " + reponseXhr[espece] + "<br>"+age+" : " + reponseXhr[age];
    }
    else if(texteBouton.trim() === "Cacher les détails"){
        bouton.innerHTML = "Détails";
        details.innerHTML = "";
    }
}
