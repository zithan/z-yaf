<?php

/**
 * Created by zithan.
 * User: zithan <zithan@163.com>
 */

class BaseController extends Yaf\Controller_Abstract
{
    public function method()
    {
        return $this->Request()->isGet();
    }
}