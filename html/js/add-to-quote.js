const quoteList = [];

const addToList = (item) => {
  quoteList.push(item);
  jsonList = JSON.stringify(quoteList);
  sessionStorage.setItem("list", jsonList);
};

const checkListForDup = () => {
  const regex = /this\sitem/;
  const theList = JSON.stringify(sessionStorage.getItem("list"));
  alert(theList);
  return regex.test(theList);
};

let itemCount = 0;
let clickCount = 0;

const showButtons = document.getElementsByClassName('show-buttons')
const quoteButtons = document.getElementsByClassName('quote-buttons');
const buttonsHTML = `<div>
  <button id="minus" class="quote-list-line quote-list-quantity-minus" data="quote-list-minus"><i class="fa fa-minus"></i></button>
  <div class="quote-list-line quote-list-quantity-counter">
    <span id="item-counter">1</span>
  </div>
  <button id="plus" class="quote-list-line quote-list-quantity-plus" data="quote-list-plus"><i class="fa fa-plus"></i></button>
  <button id="add-item" class="quote-list-line quote-list-quantity-plus" data="quote-list-plus"><i class="fa fa-check"></i></button>
</div>`;
const test = () => {
  alert("Hey there world");
};

for(let i=0; i<showButtons.length; i++) {
  showButtons[i].onclick = function() {
    const buttRegex = /quote-list-line/;
    const buttonContainer = quoteButtons[i].innerHTML;
    if(!(buttRegex.test(buttonContainer))) {
      quoteButtons[i].innerHTML += buttonsHTML;
      for(let j=0; j<quoteButtons.length; j++) {
        if(j === i) {

        } else {
          if(buttRegex.test(quoteButtons[j].innerHTML)) {
            quoteButtons[j].innerHTML = quoteButtons[j].innerHTML.replace(buttonsHTML, "");
          }
        }
      }
    }
  };
  
}

addItemButton.onclick = function(){
  alert(clickCount);
  if(clickCount > 0) {
    if(checkListForDup()){
      return;
    }
  }
  let obj = {};
  obj.itemName = "this item";
  obj.quantity = Number(itemCounter.innerHTML);
  addToList(obj);
  clickCount++;
  alert(sessionStorage.getItem("list"));
};

itemPlus.onclick = function(){
  itemCount++;
  itemCounter.innerHTML = itemCount;
};

itemMinus.onclick = function(){
  if(itemCount>0) {
    itemCount--;
    itemCounter.innerHTML = itemCount;
  }
};
