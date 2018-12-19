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

let itemCount = 1;
let clickCount = 0;

const showButtons = document.getElementsByClassName('show-buttons')
const quoteButtons = document.getElementsByClassName('quote-buttons');
const buttonsHTML = `<div class="">
  <button id="minus" class="quote-list-line quote-list-quantity-minus" data="quote-list-minus"><i class="fa fa-minus"></i></button>
  <div class="quote-list-line quote-list-quantity-counter">
    <span id="item-counter">1</span>
  </div>
  <button id="plus" class="quote-list-line quote-list-quantity-plus" data="quote-list-plus"><i class="fa fa-plus"></i></button>
  <button id="add-item" class="margin-eight-bottom quote-list-line quote-list-quantity-plus" data="quote-list-plus"><i class="fa fa-check"></i></button>
</div>`;
const test = () => {
  alert("Hey there world");
};
const showHideButtons = () => {
  let buttonArea = quoteButtons[1].innerHTML;
  buttonArea += buttonsHTML;
};
document.addEventListener("click", function(event) {
  let buttRegex = /quote-list-line/;
  let targetParent = event.target.parentElement;
  if(event.target.matches('.show-buttons')) {
    if( !(buttRegex.test(targetParent.innerHTML)) ) {
      targetParent.innerHTML += buttonsHTML;
    }
    for(let i of quoteButtons) {
      if(buttRegex.test(i.innerHTML) && i !== targetParent) {
        alert(i.innerHTML);
        i.innerHTML = i.innerHTML.replace(/^\<div.*div\&gt;$/, "");
      }
    }
    test();
    const itemMinus = document.getElementById('minus');
    const itemCounter = document.getElementById('item-counter')
    alert(itemMinus.id);
    itemMinus.onclick = function(){
      alert(itemCount);
      if(itemCount>0) {
        itemCount--;

        itemCounter.innerHTML = itemCount;
      }
    };
  }
}, false);

    let addItemButton = document.getElementById("add-item");
    let itemPlus = document.getElementById("plus");


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
