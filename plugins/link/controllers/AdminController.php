<?php

class AdminController extends Plugin {
    
    public function __construct() {
        parent::__construct();
        //Admin控制器进行登录验证
        if (!$this->session->is_set('user_id') || !$this->session->get('user_id')) $this->adminMsg('请登录以后再操作', url('admin/login'));
    }
    
    public function indexAction() {
        if ($this->post('submit_order') && $this->post('form')=='order') {
	        foreach ($_POST as $var=>$value) {
	            if (strpos($var, 'order_')!==false) {
	                $id = (int)str_replace('order_', '', $var);
	                $this->link->update(array('listorder'=>$value), 'id=' . $id);
	            }
	        }
	    }
	    if ($this->post('submit_del') && $this->post('form')=='del') {
	        foreach ($_POST as $var=>$value) {
	            if (strpos($var, 'del_')!==false) {
	                $id = (int)str_replace('del_', '', $var);
	                $this->link->delete('id=' . $id);
	            }
	        }
	    }
        $page = (int)$this->get('page');
		$page = (!$page) ? 1 : $page;
		//分页配置
	    $pagelist = $this->instance('pagelist');
		$pagelist->loadconfig();
	    $total = $this->link->count('link');
	    $pagesize = isset($this->site['SITE_ADMIN_PAGESIZE']) && $this->site['SITE_ADMIN_PAGESIZE'] ? $this->site['SITE_ADMIN_PAGESIZE'] : 8;
	    $url = purl('admin/index', array('page'=>'{page}'));
	    $data = $this->link->page_limit($page, $pagesize)->order(array('listorder ASC', 'addtime DESC'))->select();
	    $pagelist = $pagelist->total($total)->url($url)->num($pagesize)->page($page)->output();
	    $this->assign(array(
	        'list'     => $data,
	        'pagelist' => $pagelist,
	    ));
	    $this->display('admin_list');
    }
    
    public function addAction() {
        if ($this->post('submit')) {
            $data = $this->post('data');
            if (!$data['name'] || !$data['url']) $this->adminMsg('网站名称或地址不能为空');
            $data['addtime'] = time();
            $this->link->insert($data);
            $this->adminMsg('操作成功', url('link/admin'), 3, 1, 1);
        }
        $this->display('admin_add');
    }
    
    public function editAction() {
        $id = $this->get('id');
        $data = $this->link->find($id);
        if (empty($data)) $this->adminMsg('友情链接不存在');
        if ($this->post('submit')) {
            unset($data);
            $data = $this->post('data');
            if (!$data['name'] || !$data['url']) $this->adminMsg('网站名称或地址不能为空');
            $this->link->update($data, 'id=' . $id);
            $this->adminMsg('操作成功', url('link/admin'), 3, 1, 1);
        }
        $this->assign('data', $data);
        $this->display('admin_add');
    }
    
    public function delAction() {
        $id = $this->get('id');
        $this->link->delete('id=' . $id);
        $this->adminMsg('操作成功', url('link/admin'), 3, 1, 1);
    }
	
}