"use strict"

// features フェードイン
{
  const offset = window.pageYOffset;
  const rects = document.querySelectorAll(".fade-in");
  const boxs = document.querySelectorAll(".fade-in2");
  window.addEventListener("scroll", ()=>{
    rects.forEach(rect =>{
      const position = rect.getBoundingClientRect().top;
      const scroll = offset + position;
      const windowHeight = window.innerHeight;
      if(offset > scroll - windowHeight + 150){
        rect.classList.add("scroll-in")
      };
    });
    boxs.forEach(box =>{
      const position = box.getBoundingClientRect().top;
      const scroll = offset + position;
      const windowHeight = window.innerHeight;
      if(offset > scroll - windowHeight + 120){
        box.classList.add("scroll-in")
      };
    });
  });
}