//retrieve existing list from sessionStorage and parse into an array of objects
let quoteList = JSON.parse(sessionStorage.getItem('list'));

//receives an item, adds to quoteList, and updates sessionStorage
const addToList = function (item) {
  //if quoteList is not yet an array, set it to an empty array
  if (sessionStorage.length === 0) {
    quoteList = [];
  }
  quoteList.push(item);
  jsonList = JSON.stringify(quoteList);
  sessionStorage.setItem("list", jsonList);
};

//Returns true if the passed item is already in sessionStorage
const checkListForDup = function (itemToCheck) {
  const regex = new RegExp(itemToCheck);
  const theList = JSON.stringify(sessionStorage.getItem("list"));
  return regex.test(theList);
};

//Global counter variables
let itemCount = 1;

const showButtons = document.getElementsByClassName('show-buttons')

//String of buttons to concatenate onto item's <div>
const buttonsHTML = '<div class="quote-buttons-container">' +
  '<button id="minus" class="quote-buttons-line ms-grid-minus"><i class="fa fa-minus"></i></button>' +
  '<div class="quote-buttons-line quote-list-quantity-counter ms-grid-counter">' +
    '<span id="item-counter" class="item-counter">1</span><span id="item-unit" class="item-unit"></span>' +
  '</div>' +
  '<button id="plus" class="quote-buttons-line ms-grid-plus"><i class="fa fa-plus"></i></button>' +
  '<button id="add-item" class="quote-buttons-line ms-grid-add"><i class="fa fa-check"></i></button>' +
'</div>';

const addedNotification = '<div id="added" class="alt-font" style="background-color:#71eeb8; padding:10px;"><i class="fa fa-check"></i>Added to quote</div>';
const addToQuoteButton = '<a class="show-buttons btn btn-small btn-rounded btn-transparent-dark-gray margin-10px-bottom">Add to quote</a>';
const viewQuoteButton = '<a href="quote-list.php" class="btn btn-small btn-rounded btn-deep-pink margin-10px-bottom">View Quote List<i class="fa fa-arrow-right"></i></a>';

//escape Regular Expression special characters
function escapeRegExp(text) {
  return text.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, '\\$&');
}
const getButtonsToRemove = function(strOne, strTwo) {
  let result = "";
  for(let k = strOne.indexOf(strTwo) + strTwo.length; k < strOne.length; k++) {
    result += strOne[k];
  }
  return result;
};
//Listen for all clicks with conditional code depending on what is clicked
document.addEventListener("click", function(event) {
  let quoteButtons = document.getElementsByClassName('quote-buttons');
  let buttRegex = /quote-buttons-line/;
  let targetParent = event.target.parentElement;
  //add buttons to <div> if "add to quote" is clicked
  if((event.target.matches ? event.target.matches('.show-buttons') : event.target.msMatchesSelector('.show-buttons')) && !(buttRegex.test(targetParent.innerHTML))) {
    targetParent.innerHTML += buttonsHTML;
    for(let i = 0; i < quoteButtons.length; i++) {
      let currentHTML = quoteButtons[i].innerHTML;
      let unitReset = document.getElementsByClassName('item-unit');
      //Remove buttons if they are showing on any other <div>
      for(let l = 0; l < quoteButtons.length; l++) {
        if(buttRegex.test(currentHTML) && (quoteButtons[l] !== targetParent)) {
          quoteButtons[l].innerHTML = quoteButtons[l].innerHTML.replace(getButtonsToRemove(currentHTML, addToQuoteButton), "");
        }
      }
    }

    //Cache button elements and make them functional
    let itemMinus = document.getElementById('minus');
    let itemCounter = document.getElementById('item-counter');
    let addItemButton = document.getElementById("add-item");
    let itemPlus = document.getElementById("plus");
    let itemUnit = document.getElementById("item-unit");
    itemMinus.onclick = function(){
      if(itemCount>0) {
        itemCount--;
        itemCounter.innerHTML = itemCount;
      }
    };
    itemPlus.onclick = function(){
      itemCount++;
      itemCounter.innerHTML = itemCount;

    };
    addItemButton.onclick = function(){
      if(Number(itemCounter.innerHTML)>0) {
        let findCategory = document.getElementsByClassName('item-category');
        let itemCategory = findCategory[0].id;
        let itemName = targetParent.firstElementChild.innerHTML;
        let itemToAdd = "";
        if(itemCategory == undefined || itemCategory === "Gravel") {
          itemToAdd = itemName;
        } else {
          itemToAdd = itemName + " " + itemCategory;
        }

        function getUnitOfMeasure(itemCat) {
          let yardRegex = /\byard\b/;
          let tonRegex = /\bton\b/;
          let galRegex = /\b(water|cleaner)\b/i;
          let footRegex = /\bfoot\b/;
          let cementRegex = /\b(mix|mortar|sacks)\b/i;
          let stoneRegex = /stone|gravel|rock|boulder/i;
          let description = targetParent.children[1]['innerHTML'];
          if (itemCat == undefined || itemCat == "") {
            if (yardRegex.test(description)) {
              return "yard";
            } else if (tonRegex.test(description)) {
              return "ton";
            } else if (galRegex.test(description)) {
              return "gallon";
            } else if (cementRegex.test(description)) {
              return "bag";
            } else if (footRegex.test(description)) {
              return "foot";
            } else {
              return "each";
            }
          } else if (stoneRegex.test(itemCat)){
            return "ton";
          } else {
            return "each";
          }
        }
        function showUnitWithQty () {

        }

        if(checkListForDup(itemToAdd)){
          showAddedNotification();
          return;
        }
        let obj = {};
        obj.itemName = itemToAdd;
        obj.quantity = Number(itemCounter.innerHTML);
        obj.description = itemToAdd + " per " + getUnitOfMeasure(itemCategory);
        addToList(obj);
        showAddedNotification();
        function showAddedNotification() {
          itemCounter.innerHTML = 1;
          targetParent.innerHTML = targetParent.innerHTML.replace(getButtonsToRemove(targetParent.innerHTML, addToQuoteButton), addedNotification);
          targetParent.innerHTML = targetParent.innerHTML.replace(addToQuoteButton, "");
          window.setTimeout(closeAddedNotification, 1000);
        }
        function closeAddedNotification() {
          targetParent.innerHTML = targetParent.innerHTML.replace(getButtonsToRemove(targetParent.innerHTML, "</p>"), viewQuoteButton);
        }
      }
    };
  }
}, false);
