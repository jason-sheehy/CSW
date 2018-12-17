const quoteList = [];

const addToList = (item) => {
  alert("in here");
  quoteList.push(item);
  jsonList = JSON.stringify(quoteList);
  sessionStorage.setItem("list", jsonList);
};

/* const checkListForDup = (the item's name) => {
  regex = /the item's name/;
  return sessionStorage.getItem("list").test(regex));
};
*/
let itemCount = 0;
const itemPlus = document.getElementById("plus");
const itemMinus = document.getElementById('minus');
const itemCounter = document.getElementById('item-counter');
const addItemButton = document.getElementById('add-item');

addItemButton.onclick = function(){
  let obj = {};
  obj.itemName = "this item";
  obj.quantity = Number(itemCounter.innerHTML);
  addToList(obj);
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
