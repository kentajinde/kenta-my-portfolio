"use strict"

//画像ファイル選択後、画像表示
{
  const img = document.getElementById("image");
  const file = document.getElementById("file");

  file.onchange = function(e){
    const selectFiles = e.target.files;
    if(selectFiles.length != 0){
      const fr = new FileReader();
      fr.readAsDataURL(selectFiles[0]);
      fr.onload = function(){
        img.innerHTML = `<img src="${fr.result}" class="image">`;
      }
    }
  }
}

//羽ペン移動アニメーション
{
  const rewrite = document.querySelector(".whether a");
  const submit = document.querySelector(".whether input");
  const pen = document.querySelector(".whether .pen");

  rewrite.addEventListener("mouseover", ()=>{
    pen.classList.add("anime1");
    pen.classList.remove("anime2");
  });
  submit.addEventListener("mouseover", ()=>{
    pen.classList.add("anime2");
    pen.classList.remove("anime1");
  });
}