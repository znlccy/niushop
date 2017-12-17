<?php 
require_once('class/BCGFontFile.php');
require_once('class/BCGColor.php');
require_once('class/BCGDrawing.php');
require_once('class/BCGcode39.barcode.php');

// 加载字体大小
$font = new BCGFontFile('font/Arial.ttf', 14);
//条形码颜色
$color_black = new BCGColor(0, 0, 0);
$color_white = new BCGColor(255, 255, 255);

$drawException = null;
try {
    $code = new BCGcode39();
    $code->setScale(2); 
    $code->setThickness(30); // 条形码的厚度
    $code->setForegroundColor($color_black); // 条形码颜色
    $code->setBackgroundColor($color_white); // 空白间隙颜色
    $code->setFont($font); // 
    $code->parse('6516541515'); // 条形码需要的数据内容
} catch(Exception $exception) {
    $drawException = $exception;
}

//根据以上条件绘制条形码
$drawing = new BCGDrawing('', $color_white);
if($drawException) {
    $drawing->drawException($drawException);
} else {
    $drawing->setBarcode($code);
    $drawing->draw();
}
// 生成PNG格式的图片
header('Content-Type: image/png');

$drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
?>