const quoteList = [];

//receives an item, adds to quoteList, and updates sessionStorage
const addToList = (item) => {
  quoteList.push(item);
  jsonList = JSON.stringify(quoteList);
  sessionStorage.setItem("list", jsonList);
};

//Returns true if this item is already in sessionStorage
const checkListForDup = (itemToCheck) => {
  const regex = new RegExp(itemToCheck);
  const theList = JSON.stringify(sessionStorage.getItem("list"));
  alert(theList);
  return regex.test(theList);
};

//Global counter variables
let itemCount = 1;
let clickCount = 0;

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
const viewQuoteButton = `<a class="btn btn-small btn-rounded btn-deep-pink margin-10px-bottom">View Quote List<i class="fa fa-arrow-right"></i></a>`;
const test = () => {
  alert("Hey there world");
};

//Listen for all clicks with conditional code depending on what is clicked
document.addEventListener("click", function(event) {
  let buttRegex = /quote-buttons-line/;
  let targetParent = event.target.parentElement;
  if(event.target.matches('.show-buttons') && !(buttRegex.test(targetParent.innerHTML))) {
    targetParent.innerHTML += buttonsHTML;
    for(let i of quoteButtons) {
      let countReset = document.getElementsByClassName('item-counter');
      for (j of countReset) {
        j.innerHTML = 1;
      }
      itemCount = 1;
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
      let itemToAdd = `${itemName} ${itemCategory}`;
      if(clickCount > 0) {
        if(checkListForDup(itemToAdd)){
          return;
        }
      }
      let obj = {};
      obj.itemName = itemToAdd;
      obj.quantity = Number(itemCounter.innerHTML);
      addToList(obj);
      clickCount++;
      alert(sessionStorage.getItem("list"));
      itemCounter.innerHTML = 1;
      targetParent.innerHTML = targetParent.innerHTML.replace(addToQuoteButton, "");
      targetParent.innerHTML = targetParent.innerHTML.replace(buttonsHTML, addedNotification);
      window.setTimeout(closeAddedNotification, 1000);
      function closeAddedNotification() {
        targetParent.innerHTML = targetParent.innerHTML.replace(addedNotification, viewQuoteButton);
      }
    }
    };
  }
}, false);
