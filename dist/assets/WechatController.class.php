<?php
namespace Home\Controller;

use Think\Controller;
use Think\Log;

class WechatController extends Controller
{
    /**
     * 微信认证及被动接口
     */
    public function index()
    {
        //实例微信第三方处理类
        $weObj = new \Org\Wechat\Wechat(C('WECHAT_OPTIONS'));

        //微信验证服务器使用 start
        $ret = $weObj->valid();
        if (!$ret) {
            Log::write('验证失败！');
            exit;
        }
        //微信验证服务器使用 end


        $type = $weObj->getRev()->getRevType();
        $data = $weObj->getRev()->getRevData();
        //记录日志
        Log::write("请求数据：\n" . var_export($data, true));

        $replytype='text';
        //处理信息
        switch($type) {

            case $weObj::MSGTYPE_TEXT: //接收普通消息 -- text消息
                $reply="text消息";
                break;
            case $weObj::MSGTYPE_IMAGE: //接收普通消息 -- image消息
                $reply="image消息";
                break;
            case $weObj::MSGTYPE_VOICE: //接收普通消息 -- voice消息
                $reply="voice消息";
                break;
            case $weObj::MSGTYPE_LOCATION://接收普通消息 -- location消息
                $reply="location消息";
                break;
            case $weObj::MSGTYPE_EVENT: //接收事件
                $reply="事件 消息";
                break;
            default:
                $reply="未知类型 消息";
        }

        //回复给微信服务器
        $weObj->$replytype($reply)->reply();

        exit;

    }

    public function demo(){
        //实例微信第三方处理类
        $weObj = new \Org\Wechat\Wechat(C('WECHAT_OPTIONS'));

        $code = isset($_GET['code']) ? $_GET['code'] : '';
        if (! $code) {
            $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $oauth_url = $weObj->getOauthRedirect($url);
            header('Location: ' . $oauth_url);
            exit();
        }


        $userinfo = $weObj->getUserId($code);

        var_dump($userinfo);
    }

}