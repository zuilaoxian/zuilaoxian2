<?php
require_once("../config.php");
echo $api->head("给你的头像戴上口罩");
?>
  <style>
    label {
      position: fixed;
      bottom: 0;
      left: 0;
      font-size: 10px;
    }
    .header {
      color: #108757;
      height: 3rem;
    }
    #cvs {
      display: none;
      margin: 0 auto;
	  max-width:100%;
    }
    .canvas-container {
      margin: 0 auto;
	  max-width:100%;
    }
    .hide, #exportBtn {
      display: none;
    }
    .bodys {
      margin: 4rem auto 3rem;
      height: 10rem;
      width: 10rem;
      border: solid 1px #aaa;
      box-shadow: 0 0 5px #aaa;
      border-radius: 1rem;
      line-height: 10rem;
      position: relative;
	  text-align: center;
    }
    #export {
      display: none;
      margin: 0 auto;
	  max-width:100%;
    }
    .footer {
      display: flex;
      justify-content: space-around;
    }
    button {
      font-size: 18px;
      color: #fff;
      padding: 0 30px;
      height: 2.5rem;
      background: #8fd16f;
      border: 0;
      border-radius: 20px;
}
  </style>
  <div class='header'>
    <h2 style='text-align: center;'>给头像加上口罩</h2>

  </div>
  <div class='bodys' id='uploadContainer'>
    <small id='uploadText'>上传头像</small>
    <input id='upload' type='file' onchange='viewer()' style='height: 10rem; width: 10rem; position: absolute; top: 0; left: 0; opacity: 0'>
  </div>
  <img id='export' alt='' src='' />
  <canvas id='cvs'></canvas>
  <p id='tip' style='opacity: 0;text-align: center;'>点击口罩调整位置和大小</p>
  <div class='footer'>
    <button id='change' onClick='changeHat()' style='display: none;'>换 一 个</button>
    <button id='exportBtn' onClick='exportFunc()'>生成头像</button>
  </div>


  <div style='display: none'>
    <img id='img' src='' alt='' style='max-width:100%;'>
    <img class='hide' id='hat0' src='./hat0.png' />
    <img class='hide' id='hat1' src='./hat1.png' />
    <img class='hide' id='hat2' src='./hat2.png' />
    <img class='hide' id='hat3' src='./hat3.png' />
    <img class='hide' id='hat4' src='./hat4.png' />
    <img class='hide' id='hat5' src='./hat5.png' />
    <img class='hide' id='hat6' src='./hat6.png' />
    <img class='hide' id='hat7' src='./hat7.png' />
    <img class='hide' id='hat8' src='./hat8.png' />
    <img class='hide' id='hat9' src='./hat9.png' />
  </div>
<hr>
<script src="https://cdn.bootcss.com/fabric.js/2.0.0-rc.3/fabric.min.js"></script>
<script>
  var cvs = document.getElementById("cvs");
var ctx = cvs.getContext("2d");
var exportImage = document.getElementById("export");
var img = document.getElementById("img");
var hat = "hat6";
var canvasFabric;
var hatInstance;
var screenWidth = window.screen.width < 500 ? window.screen.width: 400;
var screenHeight = window.screen.height < 500 ? window.screen.height: 600;
function viewer() {
    var file = document.getElementById("upload").files[0];
    console.log(file);
    var reader = new FileReader;
    if (file) {
        reader.readAsDataURL(file);
        reader.onload = function(e) {
            img.src = reader.result;
            img.onload = function() {
                img2Cvs(img)
            }
        }
    } else {
        img.src = ""
    }
}
function img2Cvs(img) {
	var awidth;
	var aheight;
	if (img.width < screenWidth&& img.height < screenHeight) {
		awidth = img.width;
		aheight = img.height;
	}else {//原图片宽高比例 大于 图片框宽高比例,则以框的宽为标准缩放，反之以框的高为标准缩放
        if (screenWidth/ screenHeight  <= img.width / img.height) {//原图片宽高比例 大于 图片框宽高比例
            awidth = screenWidth;   //以框的宽度为标准
            aheight = screenWidth* (img.height / img.width);
        }else {//原图片宽高比例 小于 图片框宽高比例
            awidth = screenHeight  * (img.width / img.height);
            aheight = screenHeight  ;   //以框的高度为标准
        }
    }
		console.log(awidth,aheight);
    cvs.width = img.width;
    cvs.height = img.height;
    cvs.style.display = "block";
    canvasFabric = new fabric.Canvas("cvs", {
        width: awidth,
        height: aheight,
        backgroundImage: new fabric.Image(img, {
            scaleX: awidth / img.width,
            scaleY: aheight / img.height
        })
    });
    changeHat();
    document.getElementById("uploadContainer").style.display = "none";
    document.getElementById("uploadText").style.display = "none";
    document.getElementById("upload").style.display = "none";
    document.getElementById("change").style.display = "block";
    document.getElementById("exportBtn").style.display = "block";
    document.getElementById("tip").style.opacity = 1
}
function changeHat() {
    document.getElementById(hat).style.display = "none";
    var hats = document.getElementsByClassName("hide");
    hat = "hat" + ( + hat.replace("hat", "") + 1) % hats.length;
    var hatImage = document.getElementById(hat);
    hatImage.style.display = "block";
    if (hatInstance) {
        canvasFabric.remove(hatInstance)
    }
    hatInstance = new fabric.Image(hatImage, {
        top: 40,
        left: screenWidth / 3,
        scaleX: 100 / hatImage.width,
        scaleY: 100 / hatImage.height,
        cornerColor: "#0b3a42",
        cornerStrokeColor: "#fff",
        cornerStyle: "circle",
        transparentCorners: false,
        rotatingPointOffset: 30
    });
    hatInstance.setControlVisible("bl", false);
    hatInstance.setControlVisible("tr", false);
    hatInstance.setControlVisible("tl", false);
    hatInstance.setControlVisible("mr", false);
    hatInstance.setControlVisible("mt", false);
    canvasFabric.add(hatInstance)
}
function exportFunc() {
    document.getElementsByClassName("canvas-container")[0].style.display = "none";
    document.getElementById("exportBtn").style.display = "none";
    document.getElementById("tip").innerHTML = "长按图片保存或分享";
    document.getElementById("change").style.display = "none";
    cvs.style.display = "none";
    exportImage.style.display = "block";
    exportImage.src = canvasFabric.toDataURL({
        width: canvasFabric.width,
        height: canvasFabric.height
    })
}
</script>
<?php
echo $api->end();
