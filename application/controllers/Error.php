<?php
/**
 * @name ErrorController
 * @desc 错误控制器, 在发生未捕获的异常时刻被调用
 * @see http://www.php.net/manual/en/yaf-dispatcher.catchexception.php
 * @author root
 */

use exceptions\BaseException;
use helper\Config;

class ErrorController extends \Yaf\Controller_Abstract
{
    //从2.1开始, errorAction支持直接通过参数获取异常
    public function errorAction($exception)
    {
        if ($exception instanceof BaseException) {
            //如果是自定义异常，则控制http状态码，不需要记录日志
            $this->msg = $exception->msg;
            $this->status = $exception->status;
            $this->errcode = $exception->errcode;

            //日后可以再次记录用户使用异常日志

        } else {
            // 如果是服务器未处理的异常，将http状态码设置为500，并记录日志
            if(Config::get('project.debug')) {
                // 很容易看出问题
                dump($exception->getMessage());
            }

            // 记录错误日志
            $this->recordErrorLog($exception);

            $this->msg = 'sorry，接口开小差了';
            $this->status = 500;
            $this->errcode = 10001;
        }

        callbackAjax($this->msg, $this->status, [], $this->errcode);
    }

    /*
     * 将异常写入日志
     */
    private function recordErrorLog($exception)
    {
        $this->administratorsLogModel = new AdministratorsLogModel();

        $content = $exception->getCode() . ':' . $exception->getMessage();

        $this->administratorsLogModel->add($content);
    }
}
