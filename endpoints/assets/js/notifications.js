function showSuccessNotification(elementId, text){
    let element = document.getElementById(elementId);
    element.classList.add("successNotification");

    const para = document.createElement("p");
    const node = document.createTextNode(text);
    para.appendChild(node);

    element.appendChild(para);
}

function showErrorNotification(elementId, text){
    let element = document.getElementById(elementId);
    element.classList.add("errorNotification");
    element.textContent = text;
}