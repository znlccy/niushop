//onload
//图片按比例缩放
function DrawImage(ImgD, iwidth, iheight) {
    //参数(图片便签,允许的宽度,允许的高度)
    var image = new Image();
    image.src = ImgD.src;
    if (image.width > 0 && image.height > 0) {
        if (image.width / image.height >= iwidth / iheight) {
            if (image.width > iwidth) {
                ImgD.width = iwidth;
                ImgD.height = (image.height * iwidth) / image.width;
                ImgD.style.display = "inline-block";
            } else {
                ImgD.width = image.width;
                ImgD.height = image.height;
                ImgD.style.display = "block";
            }
        }
        else {
            if (image.height > iheight) {
                ImgD.height = iheight;
                ImgD.width = (image.width * iheight) / image.height;
                ImgD.style.display = "block";
            } else {
                ImgD.width = image.width;
                ImgD.height = image.height;
                ImgD.style.display = "inline-block";
            }
        }
    }
}
//onerror
//图片加载出错时替换成默认图片,同时也解决了ie6的over stack问题
//参数(图片便签,默认图片地址,允许的宽度,允许的高度)
function errorload(ImgD, src, iwidth, iheight) {
    var w = iwidth || 100, h = iheight || 100;
    var image = new Image();
    image.src = src;
    ImgD.src = src;
    if (image.width > 0 && image.height > 0) {
        if (image.width / image.height >= iwidth / iheight) {
            if (image.width > iwidth) {
                ImgD.width = iwidth;
                ImgD.height = (image.height * iwidth) / image.width;
                ImgD.style.display = "inline-block";
            } else {
                ImgD.width = image.width;
                ImgD.height = image.height;
                ImgD.style.display = "block";
            }
        }
        else {
            if (image.height > iheight) {
                ImgD.height = iheight;
                ImgD.width = (image.width * iheight) / image.height;
                ImgD.style.display = "block";
            } else {
                ImgD.width = image.width;
                ImgD.height = image.height;
                ImgD.style.display = "inline-block";
            }
        }
    }
}