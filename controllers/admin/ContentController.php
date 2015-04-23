<?php

class ContentController extends Admin {
    
    private $tree;
	private $verify;
    
    public function __construct() {
		parent::__construct();
		$this->tree     = $this->instance('tree');
		$this->verify	= $this->model('content_' . $this->siteid . '_verify');
		$this->tree->config(array('id' => 'catid', 'parent_id' => 'parentid', 'name' => 'catname'));
	}
	
	/**
	 * 管理
	 */
	public function indexAction() {
	    if ($this->post('submit') && $this->post('form') == 'search') {	//搜索参数设置
	        $kw    = $this->post('kw');
	        $catid = (int)$this->post('catid');
			$stype = $this->post('stype');
			unset($_GET['page']);
	    } elseif ($this->post('submit_order') && $this->post('form') == 'order') {	//排序操作
	        foreach ($_POST as $var => $value) {
	            if (strpos($var, 'order_') !== false) {
	                $id = (int)str_replace('order_', '', $var);
	                $this->content->update(array('listorder' => $value), 'id=' . $id);
	            }
	        }
	    } elseif ($this->post('submit_status_1') && $this->post('form') == 'status_1') {	//标记通过
			$success = 0;
	        foreach ($_POST as $var => $value) {
	            if (strpos($var, 'del_') !== false) {
	                $ids = str_replace('del_', '', $var);
	                list($_id, $_catid) = explode('_', $ids);
	                $this->content->update(array('status' => 1), 'id=' . (int)$_id);
			        $this->toHtml((int)$_id);
					$success ++;
	            }
	        }
			$this->adminMsg(lang('success') . '(' . $success . ')', '', 3, 1, 1);
	    } elseif ($this->post('submit_status_3') && $this->post('form') == 'status_3') {	//标记回收站
			$success = 0;
	        foreach ($_POST as $var => $value) {
	            if (strpos($var, 'del_') !== false) {
	                $ids = str_replace('del_', '', $var);
	                list($_id, $_catid) = explode('_', $ids);
	                $this->content->update(array('status' => 0), 'id=' . (int)$_id);
			        $this->toHtml((int)$_id);
					$success ++;
	            }
	        }
			$this->adminMsg(lang('success') . '(' . $success . ')', '', 3, 1, 1);
	    } elseif ($this->post('form') == 'del') {	//删除操作
	        foreach ($_POST as $var => $value) {
	            if (strpos($var, 'del_') !== false) {
	                $ids = str_replace('del_', '', $var);
	                list($_id, $_catid) = explode('_', $ids);
	                $this->delAction($_id, $_catid, 1);
	            }
	        }
	    } elseif ($this->post('submit_move') && $this->post('form') == 'move') {	//移动操作
		    $mcatid = (int)$this->post('movecatid');
			if (empty($mcatid)) $this->adminMsg(lang('a-con-0'));
			$mcat   = $this->cats[$mcatid];
			$mtable = $this->model($mcat['tablename']);
			$success = 0;
	        foreach ($_POST as $var => $value) {
	            if (strpos($var, 'del_') !== false) {
	                $ids = str_replace('del_', '', $var);
	                list($_id, $_catid) = explode('_', $ids);
	                $cat = $this->cats[$_catid];
					if ($cat['modelid'] == $mcat['modelid']) { //执行移动
						$this->content->update(array('catid' => $mcatid), 'id=' . (int)$_id);
						$mtable->update(array('catid' => $mcatid), 'id=' . (int)$_id);
			            $this->toHtml($_id);
						$success ++;
					}
	            }
	        }
			$this->adminMsg(lang('success') . '(' . $success . ')', '', 3, 1, 1);
	    }
		//参数接收
	    $kw       = $kw ? $kw : $this->get('kw');
	    $page     = (int)$this->get('page') ? (int)$this->get('page') : 1;
	    $catid    = $catid ? $catid : (int)$this->get('catid');
	    $stype    = isset($stype) ? $stype : (int)$this->get('stype');
		$modelid  = (int)$this->get('modelid');
		$recycle  = (int)$this->get('recycle');
		$model    = $this->get_model();	//加载内容模型
	    $pagelist = $this->instance('pagelist');	//加载分页类
		$pagelist->loadconfig();
		if (empty($modelid)) $this->adminMsg(lang('a-con-1'));
		if (!isset($model[$modelid])) $this->adminMsg(lang('a-con-2', array('1' => $modelid)));
		//模型投稿权限验证
		if ($this->adminPost($model[$modelid]['setting']['auth'])) $this->adminMsg(lang('a-cat-100', array('1' => $this->userinfo['rolename'])));
		//组装查询条件
		$where  = $catid ? 'catid=' . $catid : 'modelid=' . $modelid;
		if ($kw && $stype == 0) {
		    $where .= " and title like '%" . $kw . "%'";
		} elseif ($kw && $stype == 1) {
		    $where .= " and username='" . $kw . "' and sysadd=0";
		} elseif ($kw && $stype == 2) {
		    $where .= " and username='" . $kw . "' and sysadd=1";
		}
		if ($recycle) {	//回收站
			$where .= ' and `status`=0';
		} else {	//正常文档
			$where .= ' and `status`=1';
		}
	    $total    = $this->content->_count(null, $where); //统计数量
	    $pagesize = isset($this->site['SITE_ADMIN_PAGESIZE']) && $this->site['SITE_ADMIN_PAGESIZE'] ? $this->site['SITE_ADMIN_PAGESIZE'] : 8;
		//组装URL结构
	    $urlparam = array('page' => '{page}', 'recycle'	=> $recycle);
	    if ($kw)      $urlparam['kw']      = $kw;
	    if ($stype)   $urlparam['stype']   = $stype;
	    if ($catid)   $urlparam['catid']   = $catid;
	    if ($modelid) $urlparam['modelid'] = $modelid;
		//查询数据
	    $data			= $this->content->from(null, 'id,title,updatetime,catid,modelid,username,userid,url,listorder')->where($where)->page_limit($page, $pagesize)->order(array('listorder DESC', 'updatetime DESC'))->select();
	    $count			= array(); //统计各个状态的数据量
		$pagelist		= $pagelist->total($total)->url(url('admin/content/index', $urlparam))->num($pagesize)->page($page)->output();
		if ($recycle) {	//回收站
			$count[0]	= $total;
			$total		= (int)$this->content->_count(null, 'modelid=' . $modelid . ' AND status=1');
		} else {
			$count[0]	= (int)$this->content->_count(null, 'modelid=' . $modelid . ' AND status=0');
		}
		$count[1]		= (int)$this->verify->count('content_' . $this->siteid . '_verify', null, 'modelid=' . $modelid . ' AND status=3');	//待审
		$count[2]		= (int)$this->verify->count('content_' . $this->siteid . '_verify', null, 'modelid=' . $modelid . ' AND status=2');	//拒绝
	    $this->view->assign(array(
	        'kw'		=> $kw,
	        'list'		=> $data,
	        'page'		=> $page,
			'join'		=> $this->getModelJoin($modelid),
	        'catid'		=> $catid,
			'count'		=> $count,
			'total'		=> $total,
			'model'		=> $model[$modelid],
			'modelid'	=> $modelid,
			'recycle'	=> $recycle,
	        'pagelist'	=> $pagelist,
	        'category'	=> $this->tree->get_model_tree($this->cats, 0, null, '|-', $modelid)
	    ));
	    $this->view->display('admin/content_list');
	}
	
	/**
	 * 待审管理
	 */
	public function verifyAction() {
	    if ($this->post('submit') && $this->post('form') == 'search') {	//搜索参数设置
	        $kw    = $this->post('kw');
	        $catid = (int)$this->post('catid');
			$stype = $this->post('stype');
			unset($_GET['page']);
	    } elseif ($this->post('form') == 'del') {	//删除操作
	        foreach ($_POST as $var => $value) {
	            if (strpos($var, 'del_') !== false) {
	                $ids = str_replace('del_', '', $var);
	                list($_id, $_catid) = explode('_', $ids);
					if ($this->verifyPost($this->cats[$_catid]['setting'])) $this->adminMsg(lang('a-mod-219', array('1' => $this->cats[$_catid]['catname'])));
					$this->delverifyAction((int)$_id, 1);
	            }
	        }
	    } elseif ($this->post('submit_status_1') && $this->post('form') == 'status_1') {	//审核通过
			$success = 0;
	        foreach ($_POST as $var => $value) {
	            if (strpos($var, 'del_') !== false) {
	                $ids = str_replace('del_', '', $var);
	                list($_id, $_catid) = explode('_', $ids);
					if ($this->verifyPost($this->cats[$_catid]['setting'])) $this->adminMsg(lang('a-mod-219', array('1' => $this->cats[$_catid]['catname'])));
					$this->content->verify((int)$_id, 1);
					$this->toHtml((int)$_id);
					$success ++;
	            }
	        }
			$this->adminMsg(lang('success') . '(' . $success . ')', '', 3, 1, 1);
	    } elseif ($this->post('submit_status_0') && $this->post('form') == 'status_0') {	//标记未审核
			$success = 0;
	        foreach ($_POST as $var => $value) {
	            if (strpos($var, 'del_') !== false) {
	                $ids = str_replace('del_', '', $var);
	                list($_id, $_catid) = explode('_', $ids);
					if ($this->verifyPost($this->cats[$_catid]['setting'])) $this->adminMsg(lang('a-mod-219', array('1' => $this->cats[$_catid]['catname'])));
	                $this->content->verify((int)$_id, 3);
			        $this->toHtml((int)$_id);
					$success ++;
	            }
	        }
			$this->adminMsg(lang('success') . '(' . $success . ')', '', 3, 1, 1);
	    } elseif ($this->post('submit_status_2') && $this->post('form') == 'status_2') {	//标记拒绝
			$success = 0;
	        foreach ($_POST as $var => $value) {
	            if (strpos($var, 'del_') !== false) {
	                $ids = str_replace('del_', '', $var);
	                list($_id, $_catid) = explode('_', $ids);
					if ($this->verifyPost($this->cats[$_catid]['setting'])) $this->adminMsg(lang('a-mod-219', array('1' => $this->cats[$_catid]['catname'])));
	                $this->content->verify((int)$_id, 2);
			        $this->toHtml((int)$_id);
					$success ++;
	            }
	        }
			$this->adminMsg(lang('success') . '(' . $success . ')', '', 3, 1, 1);
	    }
		//参数接收
	    $kw       = $kw ? $kw : $this->get('kw');
	    $page     = (int)$this->get('page') ? (int)$this->get('page') : 1;
	    $catid    = $catid ? $catid : (int)$this->get('catid');
	    $stype    = isset($stype) ? $stype : (int)$this->get('stype');
		$model    = $this->get_model(); //加载内容模型
		$status   = (int)$this->get('status');
		$status	  = !$status ? 2 : $status;
		$modelid  = (int)$this->get('modelid');
	    $pagelist = $this->instance('pagelist'); //加载分页类
		$pagelist->loadconfig();
		if (empty($modelid)) $this->adminMsg(lang('a-con-1'));
		if (!isset($model[$modelid])) $this->adminMsg(lang('a-con-2', array('1' => $modelid)));
		//模型投稿权限验证
		if ($this->adminPost($model[$modelid]['setting']['auth'])) $this->adminMsg(lang('a-cat-100', array('1' => $this->userinfo['rolename'])));
		//组装查询条件
	    $where    = '`status`=' . $status;
		if ($catids = $this->getVerifyCatid()) {	//角色审核权限
			$where .= ' and catid not in (' . implode(',', $catids) . ')';
		}
		$where   .= $catid ? ' and catid=' . $catid : ' and modelid=' . $modelid;
		if ($kw && $stype == 0) {
		    $where .= " and title like '%" . $kw . "%'";
		} elseif ($kw && $stype == 1) {
		    $where .= " and username='" . $kw . "'";
		} elseif ($kw && $stype == 2) {
		    $where .= " and username='" . $kw . "'";
		}
		$total    = $this->content->count('content_' . $this->siteid . '_verify', null, $where); //统计数量
	    $pagesize = isset($this->site['SITE_ADMIN_PAGESIZE']) && $this->site['SITE_ADMIN_PAGESIZE'] ? $this->site['SITE_ADMIN_PAGESIZE'] : 8;
		$urlparam = array('page' => '{page}', 'status' => $status); //组装URL结构
	    if ($kw)      $urlparam['kw']      = $kw;
	    if ($stype)   $urlparam['stype']   = $stype;
	    if ($catid)   $urlparam['catid']   = $catid;
	    if ($modelid) $urlparam['modelid'] = $modelid;
		//查询数据
	    $data			= $this->verify->from(null, 'id,title,updatetime,catid,modelid,username,userid')->where($where)->page_limit($page, $pagesize)->order('updatetime DESC')->select();
	    $count			= array();	//统计各个状态的数据量
		$pagelist		= $pagelist->total($total)->url(url('admin/content/verify', $urlparam))->num($pagesize)->page($page)->output();
		$count[0]		= (int)$this->content->_count(null, 'modelid=' . $modelid . ' AND status=0');	//回收站
		if ($status == 2) {
			$count[2]	= $total;
			$count[1]	= (int)$this->verify->count('content_' . $this->siteid . '_verify', null, 'modelid=' . $modelid . ' AND status=3');	//待审
		} else {
			$count[1]	= $total;
			$count[2]	= (int)$this->verify->count('content_' . $this->siteid . '_verify', null, 'modelid=' . $modelid . ' AND status=2');	//拒绝
		}
		$total			= (int)$this->content->_count(null, 'modelid=' . $modelid . ' AND status=1');	//全部
	    $this->view->assign(array(
	        'kw'		=> $kw,
	        'page'		=> $page,
			'join'		=> $this->getModelJoin($modelid),
	        'list'		=> $data,
			'total'		=> $total,
			'model'		=> $model[$modelid],
	        'catid'		=> $catid,
			'count'		=> $count,
			'status'	=> $status,
			'modelid'	=> $modelid,
	        'pagelist'	=> $pagelist,
	        'category'	=> $this->tree->get_model_tree($this->cats, 0, null, '|-', $modelid)
	    ));
	    $this->view->display('admin/content_list');
	}
	
	/**
	 * 修改待审
	 */
    public function editverifyAction() {
	    $id       = (int)$this->get('id');
	    $data     = $this->verify->find($id);
	    if (empty($data)) $this->adminMsg(lang('a-con-10'));
		$data	  = string2array($data['content']);
	    $catid    = $data['catid'];
	    $model    = $this->get_model();
	    $modelid  = $data['modelid'];
	    if (!isset($model[$modelid])) $this->adminMsg(lang('a-con-3'));
		if ($this->verifyPost($this->cats[$catid]['setting'])) $this->adminMsg(lang('a-mod-219', array('1' => $this->cats[$catid]['catname'])));
		//模型投稿权限验证
		if ($this->adminPost($model[$modelid]['setting']['auth'])) $this->adminMsg(lang('a-cat-100', array('1' => $this->userinfo['rolename'])));
	    $fields   = $model[$modelid]['fields'];
	    if ($this->post('submit')) {
	        $_data = $data;
	        $data  = $this->post('data');
	        if (empty($data['title'])) $this->adminMsg(lang('a-con-5'));
	        if ($data['catid'] != $catid && $modelid != $this->cats[$data['catid']]['modelid']) $this->adminMsg(lang('a-con-6'));
			//投稿权限验证
			if (!$isUser && $this->adminPost($this->cats[$data['catid']]['setting'])) $this->adminMsg(lang('a-cat-100', array('1' => $this->userinfo['rolename'])));
			$this->checkFields($fields, $data, 1);
			if (isset($_data['inputtime']))	$data['inputtime'] = $_data['inputtime'];
			$data['sysadd']    	= 0;
			$data['userid']		= $_data['userid'];
	        $data['status']     = $data['status'];
	        $data['modelid']    = (int)$modelid;
	        $data['username']	= $_data['username'];
			$data['updatetime'] = $_data['updatetime'];
	        $result				= $this->content->member($id, $model[$modelid]['tablename'], $data);
	        if (!is_numeric($result)) $this->adminMsg($result);
	        if ($this->site['SITE_MAP_AUTO'] == true) $this->sitemap();
	        $this->adminMsg(lang('success'), ($this->post('backurl') ? $this->post('backurl') : url('admin/content/verify')), 3, 1, 1);
	    }
	    $this->view->assign(array(
	        'data'			=> $data,
			'model'			=> $model[$modelid],
			'modelid'		=> $modelid,
			'backurl'		=> isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
	        'category'		=> $this->tree->get_model_tree($this->cats, 0, $catid, '|-', $modelid, null, null, $this->userinfo['roleid']),
	        'data_fields'	=> $this->getFields($fields, $data)
	    ));
	    $this->view->display('admin/content_add');
	}
	
	/**
	 * 删除待审文档
	 */
	public function delverifyAction($id = 0, $all = 0){
	    $id		= $id ? $id : (int)$this->get('id');
	    $all	= $all ? $all : 0;
		$data	= $this->verify->find($id);
		if ($this->verifyPost($this->cats[$data['catid']]['setting'])) $this->adminMsg(lang('a-mod-219', array('1' => $this->cats[$data['catid']]['catname'])));
		$this->content->update(array('status' => 1), 'id=' . $id);
		$this->verify->delete('id=' . $id);
		$all or $this->adminMsg(lang('success'), '', 3, 1, 1);
	}
	
	/**
	 * 发布
	 */
	public function addAction() {
	    $model		= $this->get_model();
	    $modelid	= (int)$this->get('modelid');
	    if (!isset($model[$modelid])) $this->adminMsg(lang('a-con-3'));
		//模型投稿权限验证
		if ($this->adminPost($model[$modelid]['setting']['auth'])) $this->adminMsg(lang('a-cat-100', array('1' => $this->userinfo['rolename'])));
	    $fields		= $model[$modelid]['fields'];
	    if ($this->post('submit')) {
	        $data	= $this->post('data');
		    if (empty($data['catid'])) $this->adminMsg(lang('a-con-4'));
	        if (empty($data['title'])) $this->adminMsg(lang('a-con-5'));
	        if ($this->cats[$data['catid']]['modelid'] != $modelid) $this->adminMsg(lang('a-con-6'));
			//投稿权限验证
			if ($this->adminPost($this->cats[$data['catid']]['setting'])) $this->adminMsg(lang('a-cat-100', array('1' => $this->userinfo['rolename'])));
			$this->checkFields($fields, $data, 1);
			if ($this->post('updatetime') == 2 || $this->post('updatetime') == 1) {
			    $data['updatetime'] = time();
			} elseif ($this->post('updatetime') == 3) {
			    $data['updatetime'] = $data['select_time'];
			}
	        $data['sysadd']		= 1;
	        $data['modelid']	= $modelid;
	        $data['username']	= $this->userinfo['username'];
	        $data['relation']	= formatStr($data['relation']);
			$data['position']	= @implode(',', $data['position']);
	        $data['inputtime']	= time();
			$this->postEvent($data, 'before', 'admin');	//发布前事件
	        $result				= $this->content->set(0, $model[$modelid]['tablename'], $data);
	        if (!is_numeric($result)) $this->adminMsg($result);
	        $data['id']			= $result;
			$this->postEvent($data, 'later', 'admin');	//发布后事件
	        if ($this->site['SITE_MAP_AUTO'] == true) $this->sitemap();
			$this->toHtml($data);
			$this->setPosition($data['position'], $result, $data);
			$msg = '<a href="' . url('admin/content/add', array('catid' => $data['catid'], 'modelid' => $modelid)) . '" style="font-size:14px;">' . lang('a-con-7') . '</a>&nbsp;&nbsp;<a href="' . url('admin/content/index', array('modelid' => $modelid)) . '" style="font-size:14px;">' . lang('a-con-8') . '</a>';
	        $this->adminMsg(lang('a-con-9') . '<div style="padding-top:10px;">' . $msg . '</div>', '', 3, 0, 1);
	    }
		$position			= $this->model('position');
	    $data_fields		= $this->getFields($fields, array());
	    $this->view->assign(array(
			'data'			=> array('catid' => $this->get('catid')),
			'model'			=> $model[$modelid],
			'modelid'		=> $modelid,
			'position'		=> $position->where('site=' . $this->siteid)->select(),
	        'category'		=> $this->tree->get_model_tree($this->cats, 0, $this->get('catid'), '|-', $modelid, null, null, $this->userinfo['roleid']),
	        'data_fields'	=> $data_fields
	    ));
	    $this->view->display('admin/content_add');
	}
	
	/**
	 * 修改
	 */
    public function editAction() {
	    $id       = (int)$this->get('id');
	    $data     = $this->content->get_data($id);
	    if (empty($data)) $this->adminMsg(lang('a-con-10'));
	    $catid    = $data['catid'];
	    $model    = $this->get_model();
	    $modelid  = $data['modelid'];
	    if (!isset($model[$modelid])) $this->adminMsg(lang('a-con-3'));
		//模型投稿权限验证
		if ($this->adminPost($model[$modelid]['setting']['auth'])) $this->adminMsg(lang('a-cat-100', array('1' => $this->userinfo['rolename'])));
	    $fields   = $model[$modelid]['fields'];
		$isUser   = $data['sysadd'] && $data['username'] == $this->userinfo['username'] ? 1 : 0;
	    if ($this->post('submit')) {
		    $posi  = $data['position'];
			$_data = $data;
	        unset($data);
	        $data  = $this->post('data');
	        if (empty($data['title'])) $this->adminMsg(lang('a-con-5'));
	        if ($data['catid'] != $catid && $modelid != $this->cats[$data['catid']]['modelid']) $this->adminMsg(lang('a-con-6'));
			//投稿权限验证
			if (!$isUser && $this->adminPost($this->cats[$data['catid']]['setting'])) $this->adminMsg(lang('a-cat-100', array('1' => $this->userinfo['rolename'])));
			$this->checkFields($fields, $data, 1);
			if ($this->post('updatetime') == 2) {
			    $data['updatetime'] = time();
			} elseif ($this->post('updatetime') == 3) {
			    $data['updatetime'] = $data['select_time'];
			} else {
				$data['updatetime'] = $_data['updatetime'];
			}
			$data['id']			= $id;
	        $data['modelid']    = (int)$modelid;
	        $data['relation']   = formatStr($data['relation']);
			$data['position']   = @implode(',', $data['position']);
			$data['inputtime']  = $_data['inputtime'];
	        $data['id']			= $result = $this->content->set($id, $model[$modelid]['tablename'], $data);
	        if (!is_numeric($result)) $this->adminMsg($result);
	        if ($this->site['SITE_MAP_AUTO'] == true) $this->sitemap();
			$this->toHtml($data);
			$this->setPosition($data['position'], $result, $data, $posi);
	        $this->adminMsg(lang('success'), ($this->post('backurl') ? $this->post('backurl') : url('admin/content/index', array('modelid' => $modelid))), 3, 1, 1);
	    }
	    //附表内容
	    $table       = $this->model($model[$modelid]['tablename']);
	    $table_data  = $table->find($id);
	    if ($table_data) $data = array_merge($data, $table_data); //合并主表和附表
		$position    = $this->model('position');
	    $data_fields = $this->getFields($fields, $data);	//自定义字段
		if ($data['status'] == 3) {	//该文档处于待审时
			$this->view->assign('verify', $this->verify->find($id));
		}
	    $this->view->assign(array(
	        'data'         => $data,
			'model'        => $model[$modelid],
			'modelid'      => $modelid,
			'backurl'      => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
	        'category'     => $this->tree->get_model_tree($this->cats, 0, $catid, '|-', $modelid, null, null, $this->userinfo['roleid']),
			'position'     => $position->where('site=' . $this->siteid)->select(),
	        'data_fields'  => $data_fields,
	        'relation_ids' => ',' . $data['relation']
	    ));
	    $this->view->display('admin/content_add');
	}
	
	/**
	 * 删除
	 */
	public function delAction($id = 0, $catid = 0, $all = 0) {
        if (!auth::check($this->roleid, 'content-del', 'admin')) $this->adminMsg(lang('a-com-0', array('1' => 'content', '2' => 'del')));
	    $id    = $id ? $id : (int)$this->get('id');
	    $all   = $all ? $all : $this->get('all');
	    $catid = $catid ? $catid : (int)$this->get('catid');
		$back  = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : url('admin/content/', array('modelid' => $this->cats[$catid]['modelid']));
		$model = $this->get_model();
		//模型投稿权限验证
		if ($this->adminPost($model[$this->cats[$catid]['modelid']]['setting']['auth'])) $this->adminMsg(lang('a-cat-100', array('1' => $this->userinfo['rolename'])));
	    $this->content->del($id, $catid);
	    $all or $this->adminMsg(lang('success'), $back, 3, 1, 1);
	}
	
	/**
	 * 获取关键字
	 */
	public function ajaxkwAction() {
	    $data = $this->post('data');
	    if (empty($data)) exit('');
	    echo getKw($data);
	}
	
    /**
	 * 标题是否重复检查
	 */
	public function ajaxtitleAction() {
	    $id    = (int)$this->post('id');
	    $title = $this->post('title');
	    if (empty($title)) exit(lang('a-con-11'));
	    $where = $id ? "title='" . $title . "' and id<>" . $id : "title='" . $title . "'";
	    $data  = $this->content->getOne($where); 
	    if ($data) exit(lang('a-con-12'));
	    exit('0');
	}
	
	/**
	 * 加载内容表中的信息
	 */
	public function ajaxloadinfoAction() {
	    $kw     = urldecode($this->get('kw'));
	    $title  = $this->post('title');
		$catid  = (int)$this->post('catid');
	    $select = $this->content->order('updatetime DESC')->limit(0, 20);
		$select->where('`status`=1');
	    if ($title) $select->where('title like "%' . $title . '%"');
		if ($catid && $this->cats[$catid]['arrchilds']) $select->where('catid IN (' . $this->cats[$catid]['arrchilds'] . ')');
		if (empty($title) && $kw) {
		    $kw = explode(',', $kw);
			$i  = 1;
			foreach ($kw as $keyword) {
			    $i ? $select->where('title like "%' . $keyword . '%"') : $select->orwhere('title like "%' . $keyword . '%"');
				$i = 0;
			}
		}
	    $this->view->assign(array(
			'list'     => $select->select(),
		    'category' => $this->tree->get_tree($this->cats, 0, null, '&nbsp;|-', 0)
		));
	    $this->view->display('admin/content_data_load');
	}
	
	/**
	 * 更新url地址
	 */
	public function updateurlAction() {
	    if ($this->isPostForm()) {
			$cats   = null;
			$catids = $this->post('catids');
			if ($catids && !in_array(0, $catids)) {
			    $cats = @implode(',', $catids);
			} else {
			    foreach ($this->cats as $c) {
				    if ($c['typeid'] == 1) $cats[$c['catid']] = $c['catid'];
				}
				$cats = @implode(',', $cats);
			}
			if (empty($cats)) {
			    echo '
				<style type="text/css">div, a { color: #777777;}</style>
			    <div style="font-size:12px;padding-top:0px;">
				<font color=red><b>' . lang('a-con-14') . '<b></font>
				</div>
				';
				exit;
			}
			$url = url('admin/content/updateurl', array('submit' => 1, 'catids' => $cats, 'nums' => $this->post('nums')));
			echo '
			<style type="text/css">div, a { color: #777777;}</style>
			<div style="font-size:12px;padding-top:0px;">
			<a href="' . $url . '">' . lang('a-con-15') . '</a>
			<meta http-equiv="refresh" content="0; url=' . $url . '">
			</div>
			</div>
			';
			exit;
		}
		if ($this->get('submit')) {
			$mark   = 0;
			$cats   = array();
			$catids = $this->get('catids');
			$cats   = @explode(',', $catids);
			$catid  = $this->get('catid') ? $this->get('catid')  : $cats[0];
			$cat    = isset($this->cats[$catid]) ? $this->cats[$catid] : null;
			if (!$cat) {
			    echo '
				<style type="text/css">div, a { color: #777777;}</style>
			    <div style="font-size:12px;padding-top:0px;">
				<font color=green><b>' . lang('a-con-16') . '<b></font>
				</div>
				';
				exit;
			}
		    $page  = $this->get('page') ? $this->get('page') : 1;
			$nums  = $this->get('nums') ? $this->get('nums') : 100;
			$where = 'catid IN (' . $cat['arrchilds'] . ')';
			$count = $this->content->_count(null, $where);
	        $total = ceil($count/$nums);
			$list  = $this->content->where($where)->page_limit($page, $nums)->select();
			if (empty($list)) {
			    $mark = $_catid = 0;
				foreach ($cats as $c) {
					if ($catid == $c) {
						$mark = 1;
						continue;
					}
					if ($mark == 1) {
					    $_catid = $c;
						break;
					}
				}
			    if (!isset($this->cats[$_catid])) {
				    echo '
					<style type="text/css">div, a { color: #777777;}</style>
			        <div style="font-size:12px;padding-top:0px;">
					<font color=green><b>' . lang('a-con-16') . '<b></font>
					</div>
					';
					exit;
				}
				$url = url('admin/content/updateurl', array('submit' => 1, 'nums' => $nums, 'page' => 1, 'catid' => $_catid, 'catids' => $catids));
				echo '
				<style type="text/css">div, a { color: #777777;}</style>
			    <div style="font-size:12px;padding-top:0px;">
				<a href="' . $url . '">' . lang('a-con-17', array('1' => $this->cats[$_catid]['catname'])) . '</a>
				<meta http-equiv="refresh" content="0; url=' . $url . '">
				</div>
				';
				exit;
			} else {
			    foreach ($list as $t) {
                    $this->content->update(array('url' => $this->getUrl($t)), 'id=' . $t['id']);
				}
				$url = url('admin/content/updateurl', array('submit' => 1, 'nums' => $nums, 'page' => $page+1, 'catid' => $catid, 'catids' => $catids));
				echo '
				<style type="text/css">div, a { color: #777777;}</style>
			    <div style="font-size:12px;padding-top:0px;">
				<a href="' . $url . '">' . lang('a-con-18', array('1' => $this->cats[$catid]['catname'], '2' => $page, '3' => $total)) . '</a>
				<meta http-equiv="refresh" content="0; url=' . $url . '">
				</div>
				';
				exit;
			}
		} else {
		    $this->view->assign('category', $this->tree->get_tree($this->cats, 0, null, '&nbsp;|-', true));
			$this->view->display('admin/content_url');
		}
	}
	
	/**
	 * 生成/删除内容页HTML文件
	 */
	private function toHtml($data) {
		if (is_array($data) && isset($data['id'])) $this->createShow($data);
		if (is_numeric($data)) {
			$data = $this->content->find((int)$data);
			$this->createShow($data);
		}
	}
	
	/**
	 * 增加/删除推荐位
	 */
	private function setPosition($insert_ids, $cid, $data, $position = null) {
	    if (empty($cid)) return false;
		$pos   = $this->model('position_data');
	    $arrid = @explode(',', $insert_ids);
		//增加推荐位
		if (is_array($arrid)) {
			foreach ($arrid as $sid) {
				if ($sid) {
					$row = $pos->from(null, 'id')->where('posid=' . $sid . ' and contentid=' . $cid)->select(false);
					if ($row) {
						if (!auth::check($this->roleid, 'position-edit', 'admin')) $this->adminMsg(lang('a-com-0', array('1' => 'position', '2' => 'edit')));
						$set = array(
							'url'         => $data['url'],
							'catid'       => $data['catid'],
							'title'       => $data['title'],
							'thumb'       => $data['thumb'],
							'description' => $data['description']
						);
						$pos->update($set, 'id=' . $row['id']);
					} else {
						if (!auth::check($this->roleid, 'position-add', 'admin')) $this->adminMsg(lang('a-com-0', array('1' => 'position', '2' => 'add')));
						$set = array(
							'url'         => $data['url'],
							'catid'       => $data['catid'],
							'title'       => $data['title'],
							'thumb'       => $data['thumb'],
							'posid'       => $sid,
							'contentid'   => $cid,
							'description' => $data['description']
						);
						$pos->insert($set);
					}
				}
			}
		}
		//删除推荐位
		$old_ids  = @explode(',', $position);
		if (is_array($old_ids)) {
		    foreach ($old_ids as $sid) {
			    if (!in_array($sid, $arrid) && $sid) {
					if (!auth::check($this->roleid, 'position-del', 'admin')) $this->adminMsg(lang('a-com-0', array('1' => 'position', '2' => 'del')));
				    $pos->delete('posid=' . $sid . ' AND contentid=' . $cid);
				}
			}
		}
		//更新缓存
	    $data     = array();
		$pmodel   = $this->model('position');
		$position = $pmodel->where('site=' . $this->siteid)->select();
	    foreach ($position as $t) {
	        $posid	= $t['posid'];
	        $data[$posid] = $pos->where('posid=' . $posid)->order('listorder ASC, id DESC')->select();
	        $data[$posid]['catid']  = $t['catid'];
	        $data[$posid]['maxnum'] = $t['maxnum'];
	    }
	    //写入缓存文件中
	    $this->cache->set('position_' . $this->siteid, $data);
	}
}