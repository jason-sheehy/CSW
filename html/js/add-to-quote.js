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
const itemPlus = document.getElementById("plus");
const itemMinus = document.getElementById('minus');
const itemCounter = document.getElementById('item-counter');
const addItemButton = document.getElementById('add-item');

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
//Create object for items on list

//Retrieve object of items added to list
