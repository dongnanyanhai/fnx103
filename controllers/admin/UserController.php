<?php

class UserController extends Admin {

    public function __construct() {
		parent::__construct();
	}

	public function indexAction() {
	    $roleid = $this->get('roleid');
	    $data   = $this->user->get_user_list($roleid, 1);
	    $this->view->assign('list', $data);
		$this->view->display('admin/user_list');
	}
	
	public function addAction() {
	    $role = $this->user->get_role_list();
	    if ($this->post('submit')) {
	        $username = $this->post('username');
	        if (!$username) $this->adminMsg(lang('a-use-0'));
	        if ($this->user->getOne('username=?', $username)) $this->adminMsg(lang('a-use-2'));
			$usermenu = $this->post('menu');
			$menu     = array();
			foreach ($usermenu['name'] as $id=>$v) {
			    if ($v && $usermenu['url'][$id]) {
				    $menu[$id] = array('name'=>$v, 'url'=>$usermenu['url'][$id]);
				}
			}
	        $data = array(
	            'username' => $username,
	            'realname' => $this->post('realname'),
	            'email'    => $this->post('email'),
	            'roleid'   => $this->post('roleid'),
				'site'	   => $this->post('site'),
				'usermenu' => array2string($menu),
	        );
			$data['salt']     = substr(md5(time()), 0, 10);
	        $data['password'] = md5(md5($this->post('password')) . $data['salt'] . md5($this->post('password')));
	        $this->user->insert($data);
	        $this->adminMsg(lang('success'), url('admin/user/index/'), 3, 1, 1);
	    }
	    $this->view->assign('role', $role);
	    $this->view->assign('pwd', '');
	    $this->view->display('admin/user_add');
	}
	
	public function editAction() {
	    $role = $this->user->get_role_list();
	    if ($this->post('submit')) {
	        $userid   = (int)$this->post('userid');
			$usermenu = $this->post('menu');
			$menu     = array();
			if (!$user = $this->user->getOne('userid=' . $userid)) $this->adminMsg(lang('a-use-3'));
			foreach ($usermenu['name'] as $id=>$v) {
			    if ($v && $usermenu['url'][$id]) {
				    $menu[$id] = array('name'=>$v, 'url'=>$usermenu['url'][$id]);
				}
			}
	        $data = array(
	            'realname' => $this->post('realname'),
	            'email'    => $this->post('email'),
	            'roleid'   => $this->post('roleid'),
				'usermenu' => array2string($menu),
				'site'	   => $this->post('site'),
	        );
	        if ($this->post('password')) $data['password'] = md5(md5($this->post('password')) . $user['salt'] . md5($this->post('password')));
	        $this->user->update($data, 'userid=' . $userid);
	        $this->adminMsg(lang('success'), url('admin/user/index/'), 3, 1, 1);
	    }
	    $userid = $this->get('userid');
	    $user   = $this->user->find($userid);
	    if (empty($user)) $this->adminMsg(lang('a-use-3'));
	    $this->view->assign(array(
	        'data' => $user,
	        'role' => $role,
			'menu' => string2array($user['usermenu']),
	    ));
	    $this->view->display('admin/user_add');
	}
	
	public function ajaxeditAction() {
	    $user   = $this->userinfo;
	    $userid = $user['userid'];
	    if ($this->post('submit')) {
			$usermenu = $this->post('menu');
			$menu     = array();
			foreach ($usermenu['name'] as $id=>$v) {
			    if ($v && $usermenu['url'][$id]) {
				    $menu[$id] = array('name'=>$v, 'url'=>$usermenu['url'][$id]);
				}
			}
	        $data = array(
	            'realname' => $this->post('realname'),
	            'email'    => $this->post('email'),
				'usermenu' => array2string($menu),
	        );
	        if ($this->post('password')) $data['password'] = md5(md5($this->post('password')) . $user['salt'] . md5($this->post('password')));
	        $this->user->update($data, 'userid=' . $userid);
	        $this->adminMsg(lang('success'), url('admin/user/ajaxedit/'), 3, 1, 1);
	    }
	    if (empty($user)) $this->adminMsg(lang('a-use-3'));
	    $this->view->assign(array(
	        'data' => $user,
			'menu' => string2array($user['usermenu']),
	    ));
	    $this->view->display('admin/user_edit');
	}
	
	public function delAction() {
	    $userid = (int)$this->get('userid');
	    if (empty($userid)) $this->adminMsg(lang('a-use-3'));
	    if ($this->userinfo['userid'] == $userid) $this->adminMsg(lang('a-use-4'));
	    $this->user->delete('userid=' . $userid);
	    $this->adminMsg(lang('success'), url('admin/user/index/'), 3, 1, 1);
	}
}