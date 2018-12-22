//retrieve the list from sessionStorage and parse it into an array of objects
let quoteList = JSON.parse(sessionStorage.getItem('list'));
let quoteListContainer = document.getElementById('quote-list-container');
let quoteLines = "";
let quoteListFooter = `<div>
<a class="update-list-quantities btn btn-small btn-rounded btn-transparent-black margin-10px-bottom margin-10px-top margin-30px-right xs-no-margin xs-width-100">Update list quantities</a>
<a class="btn btn-small btn-rounded btn-deep-pink margin-10px-bottom margin-10px-top xs-width-100 xs-margin-40px-bottom">Submit for quote</a>
</div>`;
let quoteListEmpty = `<div class="col-md-12 padding-one-half-tb">
  <span class="font-weight-600 text-extra-large text-center display-inline-block vertical-align-middle margin-30px-right xs-no-margin">You currently have nothing on your quote list.</span>
  <a href="rockyard-products.html" class="btn btn-small btn-rounded btn-deep-pink margin-10px-bottom margin-10px-top xs-width-100 xs-margin-40px-bottom">Go to Products<i class="fa fa-arrow-right"></i></a>
</div>`;
const updatedNotification = `<div id="added" style="background-color:#71eeb8; padding:10px;"><i class="fa fa-check"></i>Quote Updated!</div>`;

if(sessionStorage.length == 0) {
  alert(quoteListContainer.innerHTML);
  quoteListContainer.style.display = "block";
  quoteListContainer.innerHTML += quoteListEmpty;
} else {
  quoteLines = "";
  for(let line of quoteList) {
    quoteLines += `<div id="${line['itemName']}" class="quote-list-container-row border-1px-solid padding-10px-tb col-md-12">
      <button class="quote-list-line quote-list-quantity-minus"><i class="fa fa-minus"></i></button>
      <div class="quote-list-line quote-list-quantity-counter">
        <span class="item-counter">${line['quantity']}</span>
      </div>
      <button class="quote-list-line quote-list-quantity-plus"><i class="fa fa-plus"></i></button>
      <button class="quote-list-line quote-list-delete"><i class="fa fa-ban"></i></button>
      <div class="quote-list-line quote-list-item-name">
        <span>${line['itemName']}</span>
      </div>
      <div class="quote-list-line quote-list-item-description">
        <span>${line['description']}</span>
      </div>
    </div>`;
  }
  quoteListContainer.innerHTML = "";
  if(quoteLines == "") {
    quoteListContainer.innerHTML += quoteListEmpty;
    let quoteForm = quoteListContainer.nextElementSibling;
    quoteListContainer.parentNode.removeChild(quoteForm);
  } else {
  quoteListContainer.innerHTML += quoteLines;
  }
}

document.addEventListener("click", function(event) {
  let targetParent = event.target.parentElement;
  if(event.target.matches(".quote-list-delete")) {
    targetParent.parentNode.removeChild(targetParent);
    reWriteList();
  }
  if(event.target.matches(".fa-ban")) {
    let element = targetParent.parentElement;
    element.parentNode.removeChild(element);
    reWriteList();
  }
  if(event.target.matches(".quote-list-quantity-plus")) {
    let parents = targetParent.childNodes;
    let itemCount = Number(parents[3]['firstElementChild']['innerHTML']);
    let itemCounter = parents[3]['firstElementChild'];
    itemCount++;
    itemCounter.innerHTML = itemCount;
  }
  if(event.target.matches(".fa-plus")) {
    let parents = targetParent.parentElement.childNodes;
    let itemCount = Number(parents[3]['firstElementChild']['innerHTML']);
    let itemCounter = parents[3]['firstElementChild'];
    itemCount++;
    itemCounter.innerHTML = itemCount;
  }
  if(event.target.matches(".quote-list-quantity-minus")) {
    console.log("hey");
    let parents = targetParent.childNodes;
    let itemCount = Number(parents[3]['firstElementChild']['innerHTML']);
    let itemCounter = parents[3]['firstElementChild'];
    if(itemCount>0) {
      itemCount--;
      itemCounter.innerHTML = itemCount;
    }
  }
  if(event.target.matches(".fa-minus")) {
    let parents = targetParent.parentElement.childNodes;
    let itemCount = Number(parents[3]['firstElementChild']['innerHTML']);
    let itemCounter = parents[3]['firstElementChild'];
    if(itemCount>0) {
      itemCount--;
      itemCounter.innerHTML = itemCount;
    }
  }
  if(event.target.matches(".update-list-quantities")) {
    reWriteList();
    targetParent.innerHTML += updatedNotification;
    window.setTimeout(closeUpdatedNotification, 1000);
    /*showUpdatedNotification();
    window.setTimeout(closeUpdatedNotification, 1000);
    function showUpdatedNotification() {
      targetParent.parentElement.innerHTML = targetParent.parentElement.innerHTML.replace(quoteListFooter, updatedNotification);
    }*/
    function closeUpdatedNotification() {
      targetParent.parentElement.innerHTML = targetParent.parentElement.innerHTML.replace(updatedNotification, "");
    }

  }
  function reWriteList() {
    let quoteElements = document.getElementsByClassName('quote-list-container-row');
    let resultArr = [];
    for (let item of quoteElements) {
      let papaBearent = item.childNodes;
      let obj = {};
      obj.itemName = item.id;
      obj.quantity = Number(papaBearent[3]['firstElementChild']['innerHTML']);
      obj.description = papaBearent[11]['firstElementChild']['innerHTML'];
      if(obj.quantity > 0) {
        resultArr.push(obj);
      }
    }
    jsonList = JSON.stringify(resultArr);
    sessionStorage.setItem("list", jsonList);
  }
}, false);
