<?php

/**
* Finecms X 导航栏功能
*/
class NavbarController extends Admin{
	
	protected $navbar;
	protected $navbar_data;
	protected $tree;

	function __construct(){
		parent::__construct();
		$this->navbar = $this->model('navbar');
		$this->navbar_data = $this->model('navbar_data');
		$this->tree = $this->instance('tree');
		$this->tree->config(array('id' => 'id', 'parent_id' => 'parentid', 'name' => 'title'));
	}

	public function indexAction(){
		if ($this->post('submit')) {
			foreach ($_POST as $var => $value) {
				if(strpos($var,'del_') !== false){
					$id = (int)str_replace('del_','',$var);
					$this->navbar->del($id);
				}
			}
			$this->adminMsg($this->getCacheCode('navbar') . lang('success'), url('admin/navbar/index'), 3, 1, 1);
		}

		$data = $this->navbar->where('site='.$this->siteid)->select();
		$this->view->assign('list',$data);
		$this->view->display('admin/navbar_list');
	}

	public function addAction(){
		if ($this->post('submit')){
			$data = $this->post('data');
			if (empty($data['name'])) $this->adminMsg(lang('a-fnx-43'));
			$data['site'] = $this->siteid;
			if ($this->navbar->set(0,$data)){
				$this->adminMsg($this->getCacheCode('navbar') . lang('success'),url('admin/navbar/index'),3,1,1);
			}else {
				$this->adminMsg(lang('failure'));
			}
		}
		$this->view->display('admin/navbar_add');
	}

	public function editAction(){
		$navid = (int)$this->get('navid');
		if (empty($navid)) $this->adminMsg(lang('a-fnx-44'));
		if ($this->post('submit')) {
			$data = $this->post('data');
			if (empty($data['name'])) $this->adminMsg(lang('a-fnx-43'));
			$data['site'] = $this->siteid;
			$this->navbar->set($navid,$data);
			$this->adminMsg($this->getCacheCode('navbar') . lang('success'),url('admin/navbar/index'),3,1,1);
		}
		$data = $this->navbar->find($navid);
		if (empty($data)) $this->adminMsg(lang('a-fnx-44'));
		$this->view->assign('data',$data);
		$this->view->display('admin/navbar_add');
	}

	public function delAction() {
		$navid = (int)$this->get('navid');
		if (empty($navid)) $this->adminMsg(lang('a-fnx-44'));
		$this->navbar->del($navid);
		$this->adminMsg($this->getCacheCode('navbar') . lang('success'),url('admin/navbar/index'), 3, 1, 1);
	}

	public function listAction() {
		$navid = (int)$this->get('navid');
		$data = $this->navbar->find($navid, 'name');
		if (empty($navid)) $this->adminMsg(lang('a-fnx-44'));
		if ($this->post('submit_order') && $this->post('form') == 'order') {
			foreach ($_POST as $var => $value) {
				if (strpos($var,'order_') !== false) {
					$id = (int)str_replace('order_', '', $var);
					$this->navbar_data->update(array('listorder'=>$value),'id='.$id);
				}
			}
			$this->adminMsg($this->getCacheCode('navbar') . lang('success'),url('admin/navbar/list',array('navid' => $navid)), 3, 1, 1);
		}

		if ($this->post('submit_del') && $this->post('form') == 'del'){
			foreach ($_POST as $var => $value) {
				if (strpos($var,'del_') !== false) {
					$id = (int)str_replace('del_', '', $var);
					$this->navbar_data->delete('id=' . $id);
				}
			}
			$this->adminMsg($this->getCacheCode('navbar') . lang('success'),url('admin/navbar/list',array('navid'=>$navid)), 3, 1, 1);
		}
		$navbar_data = $this->navbar_data->where('navid=' . $navid)->order('listorder ASC')->select();
		$this->view->assign(array(
			'navid' => $navid,
			'navname' =>$data['name'],
			'list'  => $this->tree->get_tree_data($navbar_data),
			));
		$this->view->display('admin/navbar_data_list');
	}

	public function adddataAction() {
		$id = (int)$this->get('id');
		$navid = (int)$this->get('navid');
		if (empty($navid)) $this->adminMsg(lang('a-fnx-44'));
		if ($this->post('submit')) {
			$data = $this->post('data');
			$data['description'] = htmlspecialchars_decode($data['description']);
			if (empty($data['title']) || empty($data['url'])) $this->adminMsg(lang('a-pos-12'));
			$data['navid'] = $navid;
			if ($this->navbar_data->set(0,$data)) {
				$this->adminMsg($this->getCacheCode('navbar') . lang('success'), url('admin/navbar/list', array('navid' => $navid)), 3, 1, 1);
			}else {
				$this->adminMsg(lang('a-pos-13'));
			}
		}

		$navbar = $this->navbar->find($navid);
		$navbar_data = $this->navbar_data->where('navid=' . $navid)->order('listorder ASC')->select();
		$navbar_data_tree = $this->tree->get_tree($navbar_data, 0, $id);
		if (empty($navbar)) $this->adminMsg(lang('a-fnx-44'));
		$this->view->assign(array(
			'navid' => $navid,
			'navbar' => $navbar,
			'navbar_data_tree' => $navbar_data_tree,
			));
		$this->view->display('admin/navbar_data_add');
	}

	public function editdataAction() {
		$id = (int)$this->get('id');
		$navid = (int)$this->get('navid');
		if (empty($navid)) $this->adminMsg(lang('a-fnx-44'));
		if ($this->post('submit')) {
			$data = $this->post('data');
			$data['description'] = htmlspecialchars_decode($data['description']);
			if (empty($data['title']) || empty($data['url'])) $this->adminMsg(lang('a-pos-12'));
			$data['navid'] = $navid;
			if ($this->navbar_data->set($id,$data)) {
				$this->adminMsg($this->getCacheCode('navbar') . lang('success'), url('admin/navbar/list', array('navid' => $navid)), 3, 1, 1);
			}else {
				$this->adminMsg(lang('a-pos-13'));
			}
		}

		$navbar = $this->navbar->find($navid);
		if (empty($navbar)) $this->adminMsg(lang('a-fnx-44'));
		$data = $this->navbar_data->find($id);
		if (empty($data)) $this->adminMsg(lang('a-fnx-46'));

		$navbar_data = $this->navbar_data->where('navid=' . $navid)->order('listorder ASC')->select();
		$navbar_data_tree = $this->tree->get_tree($navbar_data, 0, $data['parentid']);
		
		$this->view->assign(array(
			'data' => $data,
			'navid' => $navid,
			'navbar' => $navbar,
			'navbar_data_tree' => $navbar_data_tree,
			));
		$this->view->display('admin/navbar_data_add');
	}

	public function deldataAction() {
		$id = (int)$this->get('id');
		$navid = (int)$this->get('navid');
		if (empty($id)) $this->adminMsg(lang('a-fnx-46'));
		if (empty($navid)) $this->adminMsg(lang('a-fnx-44'));
		$this->navbar_data->del($id);
		$this->adminMsg($this->getCacheCode('navbar') . lang('success'), url('admin/navbar/list',array('navid' => $navid)), 3, 1, 1);

	}

	/**
	 * $navbar 缓存格式
	 * array(
	 *     navid => array(
	 *                  该导航栏目信息列表
	 *              ),
	 * );
	 */
	public function cacheAction($show=0,$site_id=0) {
		$this->navbar_data->repair();
		$data = array();
		$site_id = $site_id ? $site_id : $this->siteid;
		$navbar = $this->navbar->where('site=' . $site_id)->select();

		foreach ($navbar as $t) {
			$navid = $t['navid'];
			$data[$navid] = $this->navbar_data->where('navid=' . $navid)->order('listorder ASC, id ASC')->select();
		}

		// 写入缓存文件
		$this->cache->set('navbar_' . $site_id, $data);
		$show or $this->adminMsg(lang('a-update'),'', 3, 1, 1);
	}

	public function ajaxviewAction() {
		$navid = (int)$this->get('navid');
		$data = $this->navbar->find($navid);
		if (empty($data)) exit(lang('a-fnx-44'));
		$msg  = "<textarea id='block_" . $id . "' style='font-size:12px;width:100%;height:80px;overflow:hidden;'>";
		$msg .= "<!--" . $data['name'] . "-->\n{php \$menu = navbar(" . $navid . ");}\n<!--" . $data['name'] . "-->";
		$msg .= "\n<!-- 调用方式{loop \$menu \$m}，与调用\$cats一样 --></textarea>";
		echo $msg;
	}
}