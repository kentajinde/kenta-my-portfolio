"use strict"

// タブメニュー
{
  const menuItem = document.querySelectorAll(".tab > ul li");
  const contents = document.querySelectorAll(".content");
  const busi = document.getElementById("busi");
  const busiItem = document.getElementById("busi-item");
  const urlParams = new URLSearchParams(window.location.search);

  window.onload = () =>{
    contents.forEach(content=>{
      if(urlParams.get("category") === content.id){
        content.classList.add("active");
      }else if(!urlParams.get("category")){
        busi.classList.add("active");
      }
    });
    menuItem.forEach(selectItem=>{
      if(urlParams.get("category") === selectItem.dataset.id){
        selectItem.classList.add("active");
      }else if(!urlParams.get("category")){
        busiItem.classList.add("active");
      }
    })
  };

  menuItem.forEach(selectItem=>{
    selectItem.onclick = function(){
      menuItem.forEach(item=>{
        item.classList.remove("active");
      })
      selectItem.classList.add("active");

      contents.forEach(content=>{
        content.classList.remove("active");
      })
      document.getElementById(selectItem.dataset.id).classList.add("active");
    }
  })
}
