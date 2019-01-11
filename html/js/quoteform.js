//retrieve the list from sessionStorage and parse it into an array of objects
let quoteList = JSON.parse(sessionStorage.getItem('list'));
let quoteListContainer = document.getElementById('quote-list-container');
let quoteLines = "";
let quoteListEmpty = '<div class="col-md-12 padding-one-half-tb">' +
  '<span class="font-weight-600 text-extra-large text-center display-inline-block vertical-align-middle margin-30px-right xs-no-margin">You currently have nothing on your quote list.</span>' +
  '<a href="rockyard-products.html" class="btn btn-small btn-rounded btn-deep-pink margin-10px-bottom margin-10px-top xs-width-100 xs-margin-40px-bottom">Go to Products<i class="fa fa-arrow-right"></i></a>' +
'</div>';
const updatedNotification = '<div id="added" class="alt-font" style="background-color:#71eeb8; padding:10px;"><i class="fa fa-check"></i>Quote Updated!</div>';

if(sessionStorage.length == 0) {
  quoteListContainer.style.display = "block";
  quoteListContainer.innerHTML += quoteListEmpty;
} else {
  quoteLines = "";
  for(let i = 0; i < quoteList.length; i++) {
    quoteLines += '<div id="' + quoteList[i]['itemName'] + '" class="quote-list-container-row border-1px-solid padding-10px-tb col-md-12">' +
      '<button class="quote-list-line quote-list-quantity-minus ms-grid-minus"><i class="fa fa-minus"></i></button>' +
      '<div class="quote-list-line quote-list-quantity-counter ms-grid-counter">' +
        '<span class="item-counter">' + quoteList[i]['quantity'] + '</span>' +
      '</div>' +
      '<button class="quote-list-line quote-list-quantity-plus ms-grid-plus"><i class="fa fa-plus"></i></button>' +
      '<button class="quote-list-line quote-list-delete ms-grid-add"><i class="fa fa-ban"></i></button>' +
      '<div class="quote-list-line quote-list-item-name ms-grid-name">' +
        '<span>' + quoteList[i]['itemName'] + '</span>' +
      '</div>' +
      '<div class="quote-list-line quote-list-item-description ms-grid-desc">' +
        '<span>' + quoteList[i]['description'] + '</span>' +
      '</div>' +
    '</div>';
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

const getButtonsToRemove = function(strOne, strTwo) {
  let result = "";
  for(let k = strOne.indexOf(strTwo) + strTwo.length; k < strOne.length; k++) {
    result += strOne[k];
  }
  return result;
};

document.addEventListener("click", function(event) {
  let targetParent = event.target.parentElement;
  if(event.target.matches ? event.target.matches(".quote-list-delete") : event.target.msMatchesSelector('.quote-list-delete')) {
    targetParent.parentNode.removeChild(targetParent);
    reWriteList();
  }
  if(event.target.matches ? event.target.matches(".fa-ban") : event.target.msMatchesSelector(".fa-ban")) {
    let element = targetParent.parentElement;
    element.parentNode.removeChild(element);
    reWriteList();
  }
  if(event.target.matches ? event.target.matches(".quote-list-quantity-plus") : event.target.msMatchesSelector(".quote-list-quantity-plus")) {
    let parents = targetParent.childNodes;
    let itemCount = Number(parents[1]['firstElementChild']['innerHTML']);
    let itemCounter = parents[1]['firstElementChild'];
    itemCount++;
    itemCounter.innerHTML = itemCount;
  }
  if(event.target.matches ? event.target.matches(".fa-plus") : event.target.msMatchesSelector(".fa-plus")) {
    let parents = targetParent.parentElement.childNodes;
    let itemCount = Number(parents[1]['firstElementChild']['innerHTML']);
    let itemCounter = parents[1]['firstElementChild'];
    itemCount++;
    itemCounter.innerHTML = itemCount;
  }
  if(event.target.matches ? event.target.matches(".quote-list-quantity-minus") : event.target.msMatchesSelector(".quote-list-quantity-minus")) {
    let parents = targetParent.childNodes;
    let itemCount = Number(parents[1]['firstElementChild']['innerHTML']);
    let itemCounter = parents[1]['firstElementChild'];
    if(itemCount>0) {
      itemCount--;
      itemCounter.innerHTML = itemCount;
    }
  }
  if(event.target.matches ? event.target.matches(".fa-minus") : event.target.msMatchesSelector(".fa-minus")) {
    let parents = targetParent.parentElement.childNodes;
    let itemCount = Number(parents[1]['firstElementChild']['innerHTML']);
    let itemCounter = parents[1]['firstElementChild'];
    if(itemCount>0) {
      itemCount--;
      itemCounter.innerHTML = itemCount;
    }
  }
  let updatedNotificationRegex = new RegExp(updatedNotification);
  if((event.target.matches ? event.target.matches(".update-list-quantities") : event.target.msMatchesSelector(".update-list-quantities")) && !(updatedNotificationRegex.test(targetParent.innerHTML))) {
    reWriteList();
    targetParent.innerHTML += updatedNotification;
    window.setTimeout(closeUpdatedNotification, 1000);
    function closeUpdatedNotification() {
      const updatedNotHTML = targetParent.parentElement.childNodes;
      updatedNotHTML[1].innerHTML = '<a class="update-list-quantities btn btn-small btn-rounded btn-transparent-black margin-10px-bottom margin-10px-top margin-30px-right xs-no-margin xs-width-100">Update list quantities</a>';
    }
  }

  function reWriteList() {
    let quoteElements = document.getElementsByClassName('quote-list-container-row');
    let resultArr = [];
    for (let i = 0; i < quoteElements.length; i++) {
      let papaBearent = quoteElements[i].childNodes;
      let obj = {};
      obj.itemName = quoteElements[i].id;
      obj.quantity = Number(papaBearent[1]['firstElementChild']['innerHTML']);
      obj.description = papaBearent[5]['firstElementChild']['innerHTML'];
      if(obj.quantity > 0) {
        resultArr.push(obj);
      }
    }
    jsonList = JSON.stringify(resultArr);
    sessionStorage.setItem("list", jsonList);
  }
}, false);
//Workaround function to add list to a display:none text box before submitting form
function submitForQuote() {
  let listInputField = document.getElementById('list');
  let result = ""
  for(let i = 0; i < quoteList.length; i++) {
    result += quoteList[i]['itemName'] + " Qty: " + quoteList[i]['quantity'] + "\r\n";
  }
  listInputField.style.display = "block";
  listInputField.innerHTML += result;

}
