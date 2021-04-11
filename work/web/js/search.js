"use strict"

{
  const search = document.getElementById("search");
  const button = document.getElementById("button");

  search.oninput = (e)=>{
    if(e.target.value.trim() !== "" ){
      button.removeAttribute("disabled");
    }else{
      button.setAttribute("disabled", "");
    }
  };
  window.onbeforeunload = ()=>{
    search.value = "";
  }
}