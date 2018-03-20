<?php
/**
 * @name Bootstrap
 * @author root
 * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * @see http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 * 这些方法, 都接受一个参数:Yaf\Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf\Bootstrap_Abstract
{
    private $config;

    public function _initApiMode(\Yaf\Dispatcher $dispatcher)
    {
        header('Access-Control-Allow-Origin:*');
        Yaf\Dispatcher::getInstance()->autoRender(FALSE);
    }

    public function _initLoader(\Yaf\Dispatcher $dispatcher)
    {
        //加载vendor下的文件include APPLICATION_PATH.'/vendor/autoload.php';
        \Yaf\Loader::import(APPLICATION_PATH . '/vendor/autoload.php');
    }

    public function _initConfig()
    {
		//把配置保存起来
        $this->config = Yaf\Application::app()->getConfig();
		Yaf\Registry::set('config', $this->config);
	}

	public function _initPlugin(Yaf\Dispatcher $dispatcher)
	{
		//注册一个插件
	}

	public function _initRoute(Yaf\Dispatcher $dispatcher)
	{
		//在这里注册自己的路由协议,默认使用简单路由
	}

	public function _initView(Yaf\Dispatcher $dispatcher)
	{
		//在这里注册自己的view控制器，例如smarty,firekylin
	}

    /*public function _initDb(\Yaf\Dispatcher $dispatcher){
        //鸟哥的yaf examples
        \Db\Factory::create();
    }*/

    public function _initDb(\Yaf\Dispatcher $dispatcher){
        //初始化数据库
        \think\Db::setConfig($this->config->database->default->toArray());
        class_alias('\think\Db', 'DB');
    }

    public function _initUtil(\Yaf\Dispatcher $dispatcher){
        //公用函数载入
        \Yaf\Loader::import('helper/Util.php');
    }
}
