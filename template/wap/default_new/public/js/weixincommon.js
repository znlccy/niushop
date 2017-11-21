/**
 * 微信控制相关
 * 微信隐藏分享按钮
 */
document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
    WeixinJSBridge.call('hideOptionMenu');
});