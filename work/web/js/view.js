"use strict"

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

// likeボタン
{
  const likes = document.getElementById("likes");
  const memberId = document.getElementById("member_id");
  const postId = document.getElementById("post_id");

  likes.onchange = function(){
    const postData = new FormData;
    postData.set("member_id", memberId.value);
    postData.set("post_id", postId.value);

    const data = {
      method: "POST",
      body: postData
    };

    fetch("../app/likes.php", data)
      .then((res)=> res.text())
      .then(console.log);
  };

}