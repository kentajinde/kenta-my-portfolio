"use strict"

{
  // const images = document.querySelectorAll("img");
  const files = document.querySelectorAll("input[type='file']");

  files.forEach(file =>{
    file.onchange = function(e){
      const selectFiles = e.target.files;
      if(selectFiles.length != 0){
        const fr = new FileReader();
        fr.readAsDataURL(selectFiles[0]);
        fr.onload = function(){
          document.getElementById(file.dataset.id).innerHTML = `<img src="${fr.result}" id="${file.dataset.id}">`;
        }
      }
    }
  })
}