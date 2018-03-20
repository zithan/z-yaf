<?php
class IndexController extends BaseController
{
	public function init()
	{
		parent::init();

		$this->adminUserModel = new AdminUserModel();
	}

	public function indexAction($name = "Stranger")
	{
		//1. fetch query
		$get = $this->getRequest()->getQuery("get", "default value");

		$this->getView()->assign("name", $name);

		//4. render by Yaf, 如果这里返回FALSE, Yaf将不会调用自动视图引擎Render模板
        return true;
	}

	/**
	 * 获取菜单
	 *
	 * @return [type] [description]
	 * @author hutong
	 * @date   2018-03-01T09:28:28+080
	 */
	public function menuAction()
	{
		$meuns = $this->adminUserModel->getMenus();
		$action_list = $this->adminUserModel->getActions($this->aid);

		$actionList = explode(',', $action_list);

		$actions = array();
		foreach($meuns as $meun)
		{
			if(empty($meun['is_menu']))
			{
				continue;
			}
			if($this->isAdmin || in_array($meun['action_code'], $actionList))
			{
				$actions[] = array(
					'id' => $meun['action_id'],
					'pid' => $meun['parent_id'],
					'title' => $meun['action_name'],
					'action' => $meun['url'],
					'icon' => 'el-icon-menu',
					'query' => '',
				);
			}
		}

		$this->initCache()->store()->set('action.'.$this->aid, $actionList, 30);

		$this->callbackAjax('菜单列表', 200, $actions);
	}
}
