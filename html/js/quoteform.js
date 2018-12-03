let itemCount = 0;
const itemPlus = document.getElementById("plus");
const itemMinus = document.getElementById('minus');
const itemCounter = document.getElementById('item-counter');

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
