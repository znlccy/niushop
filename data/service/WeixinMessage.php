<?php
/**
 * WeixinMessage.php
 *
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.niushop.com.cn
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : niuteam
 * @date : 2015.4.24
 * @version : v1.0.0.0
 */
namespace data\service;

use data\service\BaseService;
use data\api\IWeixinMessage;
use data\model\WeixinInstanceMsgModel;
use data\model\NsOrderModel;
use data\extend\WchatOauth;
use data\model\WeixinAuthModel;
use data\model\NsGoodsModel;
use data\model\NsOrderGoodsExpressModel;

class WeixinMessage extends BaseService implements IWeixinMessage
{

    /**
     * 获取模板相关内容
     * 
     * @param unknown $template_no            
     */
    private function getMessageInfo($template_no, $instance_id)
    {
        // 模板消息内容
        $instance_message = new WeixinInstanceMsgModel();
        $message = $instance_message->getInfo([
            'template_no' => $template_no,
            'instance_id' => $instance_id
        ], '*');
        return $message;
    }

    /**
     * 获取商品相关内容
     * 
     * @param unknown $template_no            
     */
    private function getGoodsInfo($order_goods_id, $instance_id)
    {
        // 模板消息内容
        $goods = new NsGoodsModel();
        $goods_info = $goods->getInfo([
            'goods_id' => $order_goods_id,
            'shop_id' => $instance_id
        ], '*');
        if (! empty($goods['goods_name'])) {
            return $goods['goods_name'];
        } else {
            return '';
        }
    }

    /**
     * 查询发货物流信息
     * 
     * @param unknown $goods_id            
     */
    private function getExpressInfo($order_id)
    {
        $ordergoodsexpress = new NsOrderGoodsExpressModel();
        $express_info = $ordergoodsexpress->getInfo([
            'order_id' => $order_id
        ], '*');
        return $express_info;
    }

    /**
     * 获取用户openid相关内容
     * 
     * @param unknown $uid            
     * @param unknown $instance_id            
     */
    private function getUserOpenid($uid, $instance_id)
    {
        // 消息要发送的人
        $weixin = new Weixin();
        $fans_info = $weixin->getUserWeixinSubscribeData($uid, $instance_id);
        if (! empty($fans_info['openid'])) {
            return $fans_info['openid'];
        } else {    
            return '';
        }
    }

    /**
     * 发送消息
     * 
     * @param unknown $openid            
     * @param unknown $templateId            
     * @param unknown $url            
     * @param unknown $first            
     * @param unknown $keyword1            
     * @param unknown $keyword2            
     * @param unknown $keyword3            
     * @param unknown $keyword4            
     * @param unknown $remark            
     */
    private function sendMessage($instance_id, $openid, $templateId, $url, $first, $keyword1, $keyword2, $keyword3, $keyword4, $remark)
    {
        if(isWeixin())
        {
            $weixin_auth = new WchatOauth();
            $weixin_auth->templateMessageSend($openid, $templateId, $url, $first, $keyword1, $keyword2, $keyword3, $keyword4, $remark);
       
        }
        
    }
    /*
     * (non-PHPdoc)
     * @see \ata\api\IWeixinMessage::getWeixinInstanceMsg()
     */
    public function getWeixinInstanceMsg($instance_id)
    {
        if ($instance_id == '0') {
            $WeixinInstanceMsgModel = new WeixinInstanceMsgModel();
            $message = $WeixinInstanceMsgModel->getQuery('', '*', '');
            return $message;
        } else {
            $WeixinInstanceMsgModel = new WeixinInstanceMsgModel();
            $message = $WeixinInstanceMsgModel->getQuery([
            'instance_id' => $instance_id,
        ], '*', '');
            return $message;
        }
        
        // TODO Auto-generated method stub
    }
    
    /*
     * (non-PHPdoc)
     * @see \ata\api\IWeixinMessage::updateWeixinInstanceMessage()
     */
    public function updateWeixinInstanceMessage($instance_id)
    {
        $WeixinInstanceMsgModel = new WeixinInstanceMsgModel();
        $message = $WeixinInstanceMsgModel->getQuery('', '*', '');
        if (! empty($message)) {
            $weixin_auth = new WchatOauth();
            foreach ($message as $k => $v) {
                if (! empty($weixin_auth)) {
                    $template_no = $weixin_auth->templateID($v['template_no']);
                    if (! empty($template_no->templateID)) {
                        $WeixinInstanceMsgModel->save([
                            'template_id' => $template_no->templateID
                        ], [
                            'template_no' => $v['template_no'],
                            'instance_id' => $instance_id
                        ]);
                    }
                }
            }
        }
        return $message;
        // TODO Auto-generated method stub
    }
    
    /*
     * (non-PHPdoc)
     * @see \ata\api\IWeixinMessage::getWeixinMsgTemplate()
     */
    public function getWeixinMsgTemplate()
    {
        // TODO Auto-generated method stub
    }
    
    /*
     * (non-PHPdoc)
     * @see \ata\api\IWeixinMessage::sendWeixinOrderCreateMessage()
     */
    public function sendWeixinOrderCreateMessage($order_id)
    {
        $res = $this->sendMessage(0, 'oXTarwCCbPb9eouZmwCr6CHtNI0I', 'K6kXn9_h1Z5tFHyT1IB8sQMkGHhuvuKEgbFdkzLcOnk', '', '测试发送first', '测试发送k1', '测试发送k2', '测试发送k3', '测试发送k4', '测试发送re');
        return $res;
        // 消息要发送的内容
//         $order = new NsOrderModel();
//         $order_data = $order->getInfo([
//             'order_id' => $order_id
//         ], '*');
//         // 查询发送人信息
//         $openid = $this->getUserOpenid($order_data['buyer_id'], $order_data['shop_id']);
//         // 查询模板信息
//         $msg_info = $this->getMessageInfo('OPENTM204763758', $order_data['shop_id']);
//         if (! empty($msg_info) && ! empty($openid)) {
//             $this->sendMessage($order_data['shop_id'], $openid, $msg_info['template_id'], '', $msg_info['headtext'], $order_data['out_trade_no'], $order_data['create_time'], $order_data['pay_money'], '微信支付', $msg_info['bottomtext']);
//         }
    }
    
    /*
     * (non-PHPdoc)
     * @see \ata\api\IWeixinMessage::sendWeixinOrderPayMessage()
     */
    public function sendWeixinOrderPayMessage($order_id)
    {
        // TODO Auto-generated method stub
        // 消息要发送的内容
        $order = new NsOrderModel();
        $order_data = $order->getInfo([
            'order_id' => $order_id
        ], '*');
        // 查询发送人信息
        $openid = $this->getUserOpenid($order_data['buyer_id'], $order_data['shop_id']);
        // 查询模板信息
        $msg_info = $this->getMessageInfo('OPENTM200444326', $order_data['shop_id']);
        if (! empty($msg_info) && ! empty($openid)) {
            $this->sendMessage($order_data['shop_id'], $openid, $msg_info['template_id'], '', $msg_info['headtext'], $order_data['out_trade_no'], $order_data['create_time'], $order_data['pay_money'], '微信支付', $order_data['bottomtext']);
        }
    }
    
    /*
     * (non-PHPdoc)
     * @see \ata\api\IWeixinMessage::sendWeixinOrderDeliverMessage()
     */
    public function sendWeixinOrderDeliverMessage($order_id)
    {
        // TODO Auto-generated method stub
        // 消息要发送的内容
        $order = new NsOrderModel();
        $order_data = $order->getInfo([
            'order_id' => $order_id
        ], '*');
        // 查询发货物流信息
        $express_info = $this->getExpressInfo($order_id);
        // 查询发送人信息
        $openid = $this->getUserOpenid($order_data['buyer_id'], $order_data['shop_id']);
        // 查询模板信息
        $msg_info = $this->getMessageInfo('OPENTM201541214', $order_data['shop_id']);
        if (! empty($msg_info) && ! empty($openid)) {
            $this->sendMessage($order_data['shop_id'], $openid, $msg_info['template_id'], '', $msg_info['headtext'], $order_data['out_trade_no'], $express_info['express_company'], $express_info['express_no'], '', $order_data['bottomtext']);
        }
    }
    
    /*
     * (non-PHPdoc)
     * @see \ata\api\IWeixinMessage::sendWeixinOrderRefundMessage()
     */
    public function sendWeixinOrderRefundMessage($order_id, $order_goods_id)
    {
        // TODO Auto-generated method stub
        // 消息要发送的内容
        $order = new NsOrderModel();
        $order_data = $order->getInfo([
            'order_id' => $order_id
        ], '*');
        // 查询发送人信息
        $openid = $this->getUserOpenid($order_data['buyer_id'], $order_data['shop_id']);
        // 查询模板信息
        $msg_info = $this->getMessageInfo('OPENTM205986235', $order_data['shop_id']);
        if (! empty($msg_info) && ! empty($openid)) {
            $this->sendMessage($order_data['shop_id'], $openid, $msg_info['template_id'], '', $msg_info['headtext'], $order_data['out_trade_no'], $order_data['pay_money'], '', '', $order_data['bottomtext']);
        }
    }
    
    /*
     * (non-PHPdoc)
     * @see \ata\api\IWeixinMessage::sendWeixinOrderRefundApply()
     */
    public function sendWeixinOrderRefundApply($order_id, $order_goods_id)
    {
        // TODO Auto-generated method stub
        // 消息要发送的内容
        $order = new NsOrderModel();
        $order_data = $order->getInfo([
            'order_id' => $order_id
        ], '*');
        // 查询发送人信息
        $openid = $this->getUserOpenid($order_data['buyer_id'], $order_data['shop_id']);
        // 查询货物信息
        $goods = $this->getGoodsInfo($order_goods_id, $order_data['shop_id']);
        // 查询模板信息
        $msg_info = $this->getMessageInfo('OPENTM207103254', $order_data['shop_id']);
        if (! empty($msg_info) && ! empty($openid)) {
            $this->sendMessage($order_data['shop_id'], $openid, $msg_info['template_id'], '', $msg_info['headtext'], $order_data['pay_money'], $goods, $order_data['out_trade_no'], '', $order_data['bottomtext']);
        }
    }
    
    public function sendMessageToUser($openid, $msg_type, $content){
        $weixin_auth = new WchatOauth();
        $res = $weixin_auth->MessageSendToUser($openid, $msg_type, $content);
        return $res;
    }
}