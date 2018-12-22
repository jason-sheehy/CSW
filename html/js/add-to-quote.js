//retrieve existing list from sessionStorage and parse into an array of objects
let quoteList = JSON.parse(sessionStorage.getItem('list'));

(function () {
  if (sessionStorage.length > 0) {
    let nav = document.getElementById('accordion');
    let quoteListLink = `<li><a href="quote-list.html">Quote</a></li>`
    nav.innerHTML += quoteListLink;
  }
})();

//receives an item, adds to quoteList, and updates sessionStorage
const addToList = (item) => {
  //if quoteList is not yet an array, set it to an empty array
  if (sessionStorage.length === 0) {
    quoteList = [];
  }
  quoteList.push(item);
  jsonList = JSON.stringify(quoteList);
  sessionStorage.setItem("list", jsonList);
};

//Returns true if the passed item is already in sessionStorage
const checkListForDup = (itemToCheck) => {
  const regex = new RegExp(itemToCheck);
  const theList = JSON.stringify(sessionStorage.getItem("list"));
  return regex.test(theList);
};

//Global counter variables
let itemCount = 1;

const showButtons = document.getElementsByClassName('show-buttons')
const quoteButtons = document.getElementsByClassName('quote-buttons');
//String of buttons to concatenate onto item's <div>
const buttonsHTML = `<div class="quote-buttons-container">
  <button id="minus" class="quote-buttons-line"><i class="fa fa-minus"></i></button>
  <div class="quote-buttons-line quote-list-quantity-counter">
    <span id="item-counter" class="item-counter">1</span>
  </div>
  <button id="plus" class="quote-buttons-line"><i class="fa fa-plus"></i></button>
  <button id="add-item" class="quote-buttons-line"><i class="fa fa-check"></i></button>
</div>`;
const addedNotification = `<div id="added" style="background-color:#71eeb8; padding:10px;"><i class="fa fa-check"></i>Added to quote</div>`;
const addToQuoteButton = `<a class="show-buttons btn btn-small btn-rounded btn-transparent-dark-gray">Add to quote<i class="fa fa-arrow-right"></i></a>`;
const viewQuoteButton = `<a href="quote-list.html" class="btn btn-small btn-rounded btn-deep-pink margin-10px-bottom">View Quote List<i class="fa fa-arrow-right"></i></a>`;

//Listen for all clicks with conditional code depending on what is clicked
document.addEventListener("click", function(event) {
  let buttRegex = /quote-buttons-line/;
  let targetParent = event.target.parentElement;
  //add buttons to <div> if "add to quote" is clicked
  if(event.target.matches('.show-buttons') && !(buttRegex.test(targetParent.innerHTML))) {
    targetParent.innerHTML += buttonsHTML;
    for(let i of quoteButtons) {
      let countReset = document.getElementsByClassName('item-counter');
      for (j of countReset) {
        j.innerHTML = 1;
      }
      itemCount = 1;
      //Remove buttons if they are showing on any other <div>
      if(buttRegex.test(i.innerHTML) && i !== targetParent) {
        i.innerHTML = i.innerHTML.replace(buttonsHTML, "");
      }
    }

    //Cache button elements and make them functional
    let itemMinus = document.getElementById('minus');
    let itemCounter = document.getElementById('item-counter');
    let addItemButton = document.getElementById("add-item");
    let itemPlus = document.getElementById("plus");
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
          itemToAdd = `${itemName}`;
        } else {
          itemToAdd = `${itemName} ${itemCategory}`;
        }

        function getUnitOfMeasure(itemCat) {
          let yardRegex = / yard /;
          let tonRegex = / ton /;
          let cementRegex = /\b(mix|mortar|sacks)\b/i;
          let stoneRegex = /stone|gravel|rock/i;
          let description = targetParent.children[1]['innerHTML'];
          alert (description);
          alert (itemCat);
          if (itemCat == undefined) {
            if (yardRegex.test(description)) {
              return "by the yard";
            } else if (tonRegex.test(desciption)) {
              return "by the ton";
            } else if (cementRegex.test(desciption)) {
              return "by the bag";
            }
          } else if (stoneRegex.test(itemCat)){
            return "by the ton";
          } else {
            return "each";
          }
        }

        if(checkListForDup(itemToAdd)){
          showAddedNotification();
          return;
        }
        let obj = {};
        obj.itemName = itemToAdd;
        obj.quantity = Number(itemCounter.innerHTML);
        obj.description = `${itemToAdd} ${getUnitOfMeasure(itemCategory)}`;
        addToList(obj);
        showAddedNotification();
        function showAddedNotification() {
          itemCounter.innerHTML = 1;
          targetParent.innerHTML = targetParent.innerHTML.replace(addToQuoteButton, "");
          targetParent.innerHTML = targetParent.innerHTML.replace(buttonsHTML, addedNotification);
          window.setTimeout(closeAddedNotification, 1000);
        }
        function closeAddedNotification() {
          targetParent.innerHTML = targetParent.innerHTML.replace(addedNotification, viewQuoteButton);
        }
      }
    };
  }
}, false);
