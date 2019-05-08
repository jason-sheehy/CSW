//retrieve the list from sessionStorage and parse it into an array of objects
let quoteList = JSON.parse(sessionStorage.getItem('list'));
let quoteListContainer = document.getElementById('quote-list-container');
let quoteLines = "";
let quoteListEmpty = '<div class="col-md-12 padding-one-half-tb">' +
  '<span class="font-weight-600 text-extra-large text-center display-inline-block vertical-align-middle margin-30px-right xs-no-margin">You currently have nothing on your quote list.</span>' +
  '<a href="rockyard-products.html" class="btn btn-small btn-rounded btn-deep-pink margin-10px-bottom margin-10px-top xs-width-100 xs-margin-40px-bottom">Go to Products<i class="fa fa-arrow-right"></i></a>' +
'</div>';
let quoteListDropDown = '<div class="quote-list-container-row border-1px-solid padding-10px-tb col-md-12">' +
  '<button class="quote-list-line quote-list-quantity-minus ms-grid-minus"><i class="fa fa-minus"></i></button>' +
  '<div class="quote-list-line quote-list-quantity-counter ms-grid-counter">' +
    '<span class="item-counter">1</span>' +
  '</div>' +
  '<button class="quote-list-line quote-list-quantity-plus ms-grid-plus"><i class="fa fa-plus"></i></button>' +
  '<button class="quote-list-line quote-list-add ms-grid-add"><i class="fa fa-check"></i></button>' +
  '<div class="quote-list-line quote-list-item-name ms-grid-name">' +
    '<span>' + '<select id="dropDownSelect">' +
      '<option value="Select">Select an item to add...</option>' +
    '</select>' + '</span>' +
  '</div>' +
  '<div class="quote-list-line quote-list-item-description ms-grid-desc" style="text-align:left;">' +
  '</div>' +
'</div>';
const updatedNotification = '<div id="added" class="alt-font" style="background-color:#71eeb8; padding:10px;"><i class="fa fa-check"></i>Quote Updated!</div>';

//requesting the JSON item list
const requestItemList = new Request('js/items.json');
const fillDropDown = function(request) {
  fetch(request)
    .then(function(response){
    return response.json();})
    .then(function(listData){
      let dropDownList = document.getElementById('dropDownSelect');
      for(let i=0; i<listData.length; i++){
        dropDownList.innerHTML += '<option>' + listData[i]['name'] + '</option>';
      }
    });
  };

//If nothing has been added to the quote list, display quoteListEmpty and drop down item list.
if(sessionStorage.length == 0) {
  quoteListContainer.style.display = "block";
  quoteListContainer.innerHTML += quoteListEmpty;
  quoteListContainer.innerHTML += quoteListDropDown;

} else {

  quoteLines = "";
  for(let i = 0; i < quoteList.length; i++) {
    quoteLines += '<div id="' + quoteList[i]['itemName'] + '" class="quote-list-container-row border-1px-solid padding-10px-tb col-md-12">' +
      '<button class="quote-list-line quote-list-quantity-minus ms-grid-minus"><i class="fa fa-minus"></i></button>' +
      '<div class="quote-list-line quote-list-quantity-counter ms-grid-counter">' +
        '<span class="item-counter">' + quoteList[i]['quantity'] + '</span>' +
      '</div>' +
      '<button class="quote-list-line quote-list-quantity-plus ms-grid-plus"><i class="fa fa-plus"></i></button>' +
      '<button class="quote-list-line quote-list-delete ms-grid-delete"><i class="fa fa-ban"></i></button>' +
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
    quoteListContainer.innerHTML += quoteListDropDown;
    fillDropDown(requestItemList);
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

//Listen for clicks on buttons to manipulate list items
document.addEventListener("click", function(event) {
  let targetParent = event.target.parentElement;
  let targetGrandParent = event.target.parentElement.parentElement;
  //Delete button
  if(event.target.matches ? event.target.matches(".quote-list-delete") : event.target.msMatchesSelector('.quote-list-delete')) {
    targetParent.parentNode.removeChild(targetParent);
    reWriteList();
  }
  if(event.target.matches ? event.target.matches(".fa-ban") : event.target.msMatchesSelector(".fa-ban")) {
    targetGrandParent.parentNode.removeChild(targetGrandParent);
    reWriteList();
  }
  //Increment count button
  if(event.target.matches ? event.target.matches(".quote-list-quantity-plus") : event.target.msMatchesSelector(".quote-list-quantity-plus")) {
    incrementCount(targetParent);
  }
  if(event.target.matches ? event.target.matches(".fa-plus") : event.target.msMatchesSelector(".fa-plus")) {
    incrementCount(targetGrandParent);
  }
  //Decrement count button
  if(event.target.matches ? event.target.matches(".quote-list-quantity-minus") : event.target.msMatchesSelector(".quote-list-quantity-minus")) {
    decrementCount(targetParent);
  }
  if(event.target.matches ? event.target.matches(".fa-minus") : event.target.msMatchesSelector(".fa-minus")) {
    decrementCount(targetGrandParent);
  }
  //Add from dropdown button
  if(event.target.matches ? event.target.matches(".fa-check") : event.target.msMatchesSelector(".fa-check")) {
    addDropDownItem();
  }
  if(event.target.matches ? event.target.matches(".quote-list-add") : event.target.msMatchesSelector(".quote-list-add")) {
    addDropDownItem();
  }
  //Update list button
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

  //Increment item count function
  function incrementCount(targPar) {
    let parents = targPar.childNodes;
    let itemCount = Number(parents[1]['firstElementChild']['innerHTML']);
    let itemCounter = parents[1]['firstElementChild'];
    itemCount++;
    itemCounter.innerHTML = itemCount;
  }
  function decrementCount(targPar) {
    let parents = targPar.childNodes;
    let itemCount = Number(parents[1]['firstElementChild']['innerHTML']);
    let itemCounter = parents[1]['firstElementChild'];
    if(itemCount>0) {
      itemCount--;
      itemCounter.innerHTML = itemCount;
    }
  }
  //Add selected item from dropdown list to quote list
  function addDropDownItem() {
    let selectedItem = document.getElementById('dropDownSelect').value;
    let quoteElements = document.getElementsByClassName('quote-list-container-row');
    let papaBearent = quoteElements[0].childNodes;
    let resultArr = [];
    let obj = {};
    obj.quantity = Number(papaBearent[1]['firstElementChild']['innerHTML']);
    fetch(requestItemList)
      .then(function(response){
        return response.json();})
      .then(function(listData){
        let selItem = document.getElementById('dropDownSelect').value;
        for(i=0; i<listData.length; i++) {
          if (listData[i]['name'] == selItem) {
            obj.itemName = listData[i]['page-name'];
            obj.description = listData[i]['page-name'] + ' per ' + listData[i]['uom'];
            resultArr.push(obj);
          }
        }
        jsonList = JSON.stringify(resultArr);
        sessionStorage.setItem("list", jsonList);
      });
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
