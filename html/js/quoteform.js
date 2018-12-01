let itemCount = 0;
const itemPlus = document.querySelectorAll('[data="cart-plus"]');
const itemMinus = document.querySelectorAll('[data="cart-minus"]');
const itemCounter = document.getElementById('item-counter');

itemPlus.addEventListener("click", function(){
  itemCount++;
  itemCounter.innerHTML = itemCount;
});
