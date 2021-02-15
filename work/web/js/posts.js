"use strict"

// プロフィール編集モーダル
{
  const open = document.getElementById("edit");
  const modal = document.getElementById("modal");
  const mask = document.getElementById("mask");

  open.addEventListener("click", ()=>{
    modal.classList.add("scroll");
    mask.classList.remove("hidden");
  })

  const cansel = document.querySelector(".cansel");
  cansel.addEventListener("click", ()=>{
    modal.classList.remove("scroll");
    mask.classList.add("hidden");
  })
}

// タブメニュー
{
  const menuItem = document.querySelectorAll(".tab > ul li");
  const contents = document.querySelectorAll(".content");

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

// 投稿一覧 span
{
  const labels = document.querySelectorAll(".posts-label");

  labels.forEach(label=>{
    label.onclick = function(){
      label.classList.toggle("label-shadow");
      document.getElementById(label.dataset.id).classList.toggle("rotate");
    }
  })
}