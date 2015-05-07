<?php

class CategoryController extends Admin {
    
    private $tree;
    private $category_block;
    private $category_block_type;
    
    public function __construct() {
		parent::__construct();
		$this->tree	= $this->instance('tree');
		$this->tree->config(array('id' => 'catid', 'parent_id' => 'parentid', 'name' => 'catname'));
		$this->category_block = $this->model('category_block');
		$this->category_block_type  = array(1=>lang('a-blo-0'), 2=>lang('a-blo-1'), 3=>lang('a-blo-2'),4=>lang('a-fnx-17'),5=>lang('a-fnx-27'));
		$this->view->assign('type', $this->category_block_type);
	}
	
	/**
	 * 栏目列表
	 */
	public function indexAction() {
	    if ($this->post('submit')) {
	        foreach ($_POST as $var => $value) {
	            if (strpos($var, 'order_') !== false) {
	                $this->category->update(array('listorder'=>$value), 'catid=' . (int)str_replace('order_', '', $var));
	            }
	        }
			$this->adminMsg($this->getCacheCode('category') . lang('success'), url('admin/category/index'), 3, 1, 1);
	    }
		if ($this->post('delete')) {
			$ids = $this->post('ids');
			if ($ids) {
			    foreach($ids as $catid) {
				    $this->delAction($catid, 1);
				}
			}
			$this->adminMsg($this->getCacheCode('category') . lang('success'), url('admin/category/index'), 3, 1, 1);
	    }
		$data = $this->category->getData();
	    $this->view->assign(array(
			'model' => $this->get_model(),
			'list'  => $this->tree->get_tree_data($data)
		));
		$this->view->display('admin/category_list');
	}
	
	/**
	 * 添加栏目
	 */
	public function addAction() {
	    if ($this->post('submit')) {
	        $data = $this->post('data');
	        if ($data['typeid'] == 1) {
	            if (empty($data['modelid'])) $this->adminMsg(lang('a-cat-0'));
	        } elseif ($data['typeid'] == 2) {
	            if (empty($data['content'])) $this->adminMsg(lang('a-cat-1'));
	        } elseif ($data['typeid'] == 3) {
	            if (empty($data['urlpath'])) $this->adminMsg(lang('a-cat-2'));
	        } else {
	            $this->adminMsg(lang('a-cat-3'));
	        }
	        if ($this->post('addall')) {
			    $names  = $this->post('names');
				if (empty($names)) $this->adminMsg(lang('a-cat-4'));
				$names	= explode(chr(13), $names);
				$y = $n = 0;
				foreach ($names as $val) {
				    list($catname, $catdir) = explode('|', $val);
					$catdir = $catdir ? $catdir : word2pinyin($catname);
					if ($data['typeid'] != 3 && $this->category->check_catdir(0, $catdir)) $catdir .= rand(0, 9);
					$data['catdir']  = $catdir;
					$data['catname'] = $catname;
				    $data['setting'] = $this->post('setting');
				    $catid = $this->category->set(0, $data);
					if (!is_numeric($catid)) {
					    $n++;
					} else {
					    $this->category->url($catid, $this->getCaturl($data));
						$y++;
					}
				}
				$this->adminMsg($this->getCacheCode('category') . lang('a-cat-5', array('1' => $y, '2' => $n)), url('admin/category/index'), 3, 1, 1);
			} else {
				if (empty($data['catname'])) $this->adminMsg(lang('a-cat-4'));
				if ($data['typeid'] != 3 && $this->category->check_catdir(0, $data['catdir'])) $this->adminMsg(lang('a-cat-6'));
				$data['setting'] = $this->post('setting');
				$result = $this->category->set(0, $data);
				if (!is_numeric($result)) $this->adminMsg($result);
				$data['catid'] = $result;
				$this->category->url($result, $this->getCaturl($data));
				$this->adminMsg($this->getCacheCode('category') . lang('success'), url('admin/category/index'), 3, 1, 1);
			}
	    }
	    $model  = $this->get_model();
	    $catid  = (int)$this->get('catid');
		$json_m = json_encode($model);
	    $this->view->assign(array(
			'add'				=> 1,
	        'model'				=> $model,
			'rolemodel'			=> $this->user->get_role_list(),
	        'json_model'		=> $json_m ? $json_m : '""',
			'membergroup'		=> $this->cache->get('membergroup'),
			'membermodel'		=> $this->membermodel,
	        'category_select'	=> $this->tree->get_tree($this->cats, 0, $catid)
	    ));
	    $this->view->display('admin/category_add');
	}
	
	/**
	 * 修改栏目
	 */
    public function editAction() {
	    if ($this->post('submit')) {
	        $catid = (int)$this->post('catid');
            if (empty($catid)) $this->adminMsg(lang('a-cat-7'));
	        $data  = $this->post('data');
	        if (empty($data['catname'])) $this->adminMsg(lang('a-cat-4'));
	        if ($this->post('typeid') == 1 && $this->category->check_catdir($catid, $data['catdir'])) $this->adminMsg(lang('a-cat-6'));
	        $data['typeid']  = $this->post('typeid');
			$data['setting'] = $this->post('setting');
			//var_dump($data);
	        $result = $this->category->set($catid, $data);
	        if (is_numeric($result)) {
				$data['catid'] = $result;
				$this->category->url($result, $this->getCaturl($data));
	            $this->adminMsg($this->getCacheCode('category') . lang('success'), url('admin/category/index'), 3, 1, 1);
	        } else {
	            $this->adminMsg(lang('a-cat-8'));
	        }
	    }
        $catid   = (int)$this->get('catid');
        if (empty($catid)) $this->adminMsg(lang('a-cat-7'));
		if (!isset($this->cats[$catid])) $this->adminMsg(lang('m-con-9', array('1' => $catid)));
        $data    = $this->category->find($catid);
	    $model   = $this->get_model();
		$json_m  = json_encode($model);
	    $this->view->assign(array(
	        'data'				=> $data,
	        'model'				=> $model,
	        'catid'				=> $catid,
			'setting'			=> string2array($data['setting']),
			'rolemodel'			=> $this->user->get_role_list(),
	        'json_model'		=> $json_m ? $json_m : '""',
			'membergroup'		=> $this->cache->get('membergroup'),
			'membermodel'		=> $this->membermodel,
	        'category_select'	=> $this->tree->get_tree($this->cats, 0, $data['parentid'])
	    ));
	    $this->view->display('admin/category_add');
	}
	
	/**
	 * 删除栏目
	 */
	public function delAction($catid=0, $all=0) {
        if (!auth::check($this->roleid, 'category-del', 'admin')) $this->adminMsg(lang('a-com-0', array('1' => 'category', '2' => 'del')));
	    $all   = $all   ? $all   : $this->get('all');
	    $catid = $catid ? $catid : (int)$this->get('catid');
        if (empty($catid)) $this->adminMsg(lang('a-cat-7'));
		if (!isset($this->cats[$catid])) $this->adminMsg(lang('m-con-9', array('1' => $catid)));
        $result= $this->category->del($catid);
	    if ($result) {
	        $all or $this->adminMsg($this->getCacheCode('category') . lang('success'), url('admin/category/index'), 3, 1, 1);
	    } else {
	        $all or $this->adminMsg(lang('a-cat-8'));
	    }
	}
	
	/**
	 * 批量URL规则
	 */
	public function urlAction() {
	    if ($this->post('submit')) {
			$count  = 0;
	        $catids = $this->post('catids');
            if (empty($catids)) $this->adminMsg(lang('a-cat-9'));
	        foreach ($catids as $catid) {
			    if ($catid && isset($this->cats[$catid])) {
				    $setting = $this->cats[$catid]['setting'];
					$setting['url'] = $this->post('url');
					$setting = array2string($setting);
					$this->category->update(array('setting' => $setting), 'catid=' . $catid);
					$count ++;
				}
			}
			$this->adminMsg($this->getCacheCode('category') . lang('a-cat-10', array('1' => $count)), url('admin/category'), 3, 1, 1);
	    }
	    $this->view->assign('category', $this->tree->get_tree($this->cats));
	    $this->view->display('admin/category_url');
	}
	
	/**
	 * 调用父级栏目url规则
	 */
	public function ajaximportAction() {
	    $catid		= (int)$this->get('catid');
		if (empty($catid)) exit(json_encode(array('status' => 0)));
		$data		= $this->category->find($catid);
		if (empty($data))  exit(json_encode(array('status' => 0)));
		$setting	= string2array($data['setting']);
		$return		= array(
			'list'      => isset($setting['url']['list']) ? $setting['url']['list'] : '',
			'show'      => isset($setting['url']['show']) ? $setting['url']['show'] : '',
		    'status'    => 1,
			'catjoin'   => isset($setting['url']['catjoin']) ? $setting['url']['catjoin'] : '/',
			'show_page' => isset($setting['url']['show_page']) ? $setting['url']['show_page'] : '',
			'list_page'	=> isset($setting['url']['list_page']) ? $setting['url']['list_page'] : ''
		);
		exit(json_encode($return));
	}
	
	/**
	 * 更新栏目缓存
	 * array(
	 *     '栏目ID' => array(
	 *                     ...栏目信息
	 *                     ...模型表名称
	 *                 ),
	 * );
	 */
	public function cacheAction($show=0, $site_id=0) {
	    $this->category->repair(); //递归修复栏目数据
		$site_id   = $site_id ? $site_id : $this->siteid;
	    $model     = $this->get_model('content', $site_id);
	    $data      = $this->category->getData($site_id); //数据库查询最新数据
		$siteid    = $this->category->getSiteId($site_id);
	    $category  = $category_dir = $count = array();
	    $cb_data   = $this->category_block->where('site=' . $this->siteid)->order(array('id DESC'))->select();
	    foreach ($data as $t) {
	        $catid = $t['catid'];
	        $category[$catid] = $t;
	        if ($t['typeid'] == 1) {
	            $category[$catid]['tablename'] = $model[$t['modelid']]['tablename'];
	            $category[$catid]['modelname'] = $model[$t['modelid']]['modelname'];
	        }
			$category[$catid]['arrchilds'] = $catid; //所有子栏目集,默认当前栏目ID
	        if ($t['typeid'] != 3) {
				if ($t['child']) $category[$catid]['arrchilds'] = $this->category->child($catid) . $catid;
				//统计数据
	            //$count[$catid]['items'] = (int)$this->content->_count($site_id, 'catid IN (' . $category[$catid]['arrchilds'] . ') and `status`<>0');
	            // 把副栏目的文章也计算进来
	            $count[$catid]['items'] = (int)$this->content->_count($site_id, '(catid IN (' . $category[$catid]['arrchilds'] . ') OR find_in_set(' . $t['catid'] . ',catid2) ) and `status`<>0');
	            if ($site_id == $siteid) {
					$category[$catid]['items'] = $count[$catid]['items'];
					$this->category->update(array('items' => $count[$catid]['items']), 'catid=' . $catid);
				}
	        }
	        //把预定义的 HTML 实体转换为字符
	        $category[$catid]['content'] = htmlspecialchars_decode($category[$catid]['content']);
			//转换setting
			$category[$catid]['setting'] = string2array($category[$catid]['setting']);
			//更新分页数量
			if (empty($t['pagesize'])) {
			    $pcat = $this->category->getParentData($catid);
			    $category[$catid]['pagesize'] = $pcat['pagesize'] ? $pcat['pagesize'] : $this->site['SITE_SEARCH_PAGE'];
				$this->category->update(array('pagesize' => $category[$catid]['pagesize']), 'catid=' . $catid);
			}
	    }
		//更新URL与栏目模型id集合
		foreach ($data as $t) {
			$category[$t['catid']]['url'] = $url = $this->getCaturl($t);
			$this->category->update(array('url' => $url), 'catid=' . $t['catid']);
			$category_dir[$t['catdir']]   = $t['catid'];
			if ($t['child'] == 0) {
				$category[$t['catid']]['arrmodelid'][]	= $t['modelid'];
			} else {
				$category[$t['catid']]['arrmodelid']	= array();
				$ids = _catposids($t['catid'], null, $category);
				$ids = explode(',', $ids);
				foreach ($ids as $id) {
					if ($id && $id != $t['catid']) {
						$category[$t['catid']]['arrmodelid'][] = $category[$id]['modelid'];
					}
				}
			}
			$category[$t['catid']]['arrmodelid'] = array_unique($category[$t['catid']]['arrmodelid']);
			// 阿海新增，给单页面栏目增加外部链接功能
	        if ($t['typeid'] == 1 || $t['typeid'] == 2 ) {
	        	if (!empty($t['urlpath'])){
	        		$category[$t['catid']]['ourl'] = $t['url'];
	        		$category[$t['catid']]['url'] = $t['urlpath'];
	        	}
	        }
	        foreach ($cb_data as $cb) {
	        	if($cb['catid'] == $t['catid']){
				    if ($cb['type']==4){
				    	$block_link =  string2array($cb['content']);
				    	if (is_array($block_link)){
				    		foreach ($block_link as $key => $value) {
				    			$block_link[$key] = htmlspecialchars_decode($value);
				    		}
				    		$category[$t['catid']][$cb['fieldname']] = $block_link;
				    	}
				    }else{
				    	$category[$t['catid']][$cb['fieldname']] = $cb['content'];
				    }
	        	}
	        }
	    }
	    //保存到缓存文件
		if ($site_id == $siteid) {
			$this->cache->set('category_' . $siteid,  $category);
		} else {
			$this->cache->set('category_' . $site_id, $count);
		}
	    $this->cache->set('category_dir_' . $site_id, $category_dir);
	    $show or $this->adminMsg(lang('a-update'), url('admin/category/index'), 3, 1, 1);
	}

	// 阿海新增，栏目自定义字段管理，代码主要来自系统默认block功能
	public function categoryblockAction() {
		$catid = $this->get('catid');
		if ($this->post('submit_del')) {
	        foreach ($_POST as $var=>$value) {
	            if (strpos($var, 'del_')!==false) {
	                $id = (int)str_replace('del_', '', $var);
	                $this->delAction($id, 1);
	            }
	        }
			$this->adminMsg($this->getCacheCode('block') . lang('success'), url('admin/category/categoryblock/',array('catid'=>$this->get('catid'))), 3, 1, 1);
	    }
	    $page     = (int)$this->get('page');
		$page     = (!$page) ? 1 : $page;
	    $pagelist = $this->instance('pagelist');
		$pagelist->loadconfig();
	    $total    = $this->category_block->count('block', null, 'site=' . $this->siteid);
	    $pagesize = isset($this->site['SITE_ADMIN_PAGESIZE']) && $this->site['SITE_ADMIN_PAGESIZE'] ? $this->site['SITE_ADMIN_PAGESIZE'] : 8;
	    $data     = $this->category_block->where('site=' . $this->siteid)->where('catid=' . $this->get('catid'))->page_limit($page, $pagesize)->order(array('id DESC'))->select();
	    $pagelist = $pagelist->total($total)->url(url('admin/category/categoryblock', array('catid'=>$this->get('catid'),'page'=>'{page}')))->num($pagesize)->page($page)->output();
	    $this->view->assign(array(
	        'list'     => $data,
	        'pagelist' => $pagelist,
	    ));
	    $this->view->display('admin/category_block_list');
    }
    
    public function cbaddAction() {
        if ($this->post('submit')) {
            $data = $this->post('data');
            if (empty($data['type'])) $this->adminMsg(lang('a-blo-3'));
            // 阿海新增，Block-链接类型
			if ($data['type'] == 4){
				$data['content'] = array2string($data['content_' . $data['type']]);
			}else{
				$data['content'] = $data['content_' . $data['type']];
			}
            if (empty($data['name']) || empty($data['content'])) $this->adminMsg(lang('a-blo-4'));
			$data['site'] = $this->siteid;
			$data['catid'] = $this->get('catid');
            $this->category_block->insert($data);
            $this->adminMsg($this->getCacheCode('block') . lang('success'), url('admin/category/categoryblock',array('catid'=>$this->get('catid'))), 3, 1, 1);
        }
        $this->view->display('admin/category_block_add');
    }
    
    public function cbeditAction() {
        $id   = (int)$this->get('id');
        $data = $this->category_block->find($id);
        if ($data['type'] == 4){
        	$data['content'] = string2array($data['content']);
        }
        if (empty($data)) $this->adminMsg(lang('a-blo-5'));
        if ($this->post('submit')) {
            unset($data);
            $data = $this->post('data');
            if (empty($data['type'])) $this->adminMsg(lang('a-blo-3'));
            // 阿海新增，Block-链接类型
			if ($data['type'] == 4){
				$data['content'] = array2string($data['content_' . $data['type']]);
			}else{
				$data['content'] = $data['content_' . $data['type']];
			}
            if (empty($data['name']) || empty($data['content'])) $this->adminMsg(lang('a-blo-4'));
			$data['site'] = $this->siteid;
			$data['catid'] = $this->get('catid');
            $this->category_block->update($data, 'id=' . $id);
            $this->adminMsg($this->getCacheCode('block') . lang('success'), url('admin/category/categoryblock',array('catid'=>$this->get('catid'))), 3, 1, 1);
        }
        $this->view->assign('data', $data);
        $this->view->display('admin/category_block_add');
    }
    
    public function cbdelAction($id=0, $all=0) {
        if (!auth::check($this->roleid, 'block-del', 'admin')) $this->adminMsg(lang('a-com-0', array('1'=>'block', '2'=>'del')));
	    $id  = $id  ? $id  : (int)$this->get('id');
	    $all = $all ? $all : $this->get('all');
	    $this->category_block->delete('site=' . $this->siteid . ' AND id=' . $id);
	    $all or $this->adminMsg($this->getCacheCode('block') . lang('success'), url('admin/category/categoryblock',array('catid'=>$this->get('catid'))), 3, 1, 1);
	}
	/**
	 * 加载调用代码
	 */
	public function cbajaxviewAction() {
	    $id   = (int)$this->get('id');
	    $data = $this->category_block->find($id);
	    if (empty($data)) exit(lang('a-blo-5'));
	    //var_dump($data);
	    if ($data['type'] == 4){
		    $msg  = "<textarea id='block_" . $id . "' style='font-size:12px;width:100%;height:80px;overflow:hidden;'>";
		    $msg .= "<!--" . $data['name'] . "-->\n{php \$anchor = \$cats[".$data['catid']."]['".$data['fieldname']."'];}\n<!--" . $data['name'] . "-->";
		    $msg .= "\n<!-- 调用方式{\$anchor['title']} --></textarea>";
	    }else{
		    $msg  = "<textarea id='block_" . $id . "' style='font-size:12px;width:100%;height:50px;overflow:hidden;'>";
		    $msg .= "<!--" . $data['name'] . "-->\n{\$cats[".$data['catid']."]['".$data['fieldname']."']}\n<!--" . $data['name'] . "-->";
		    $msg .= "</textarea>";
	    }

	    echo $msg;
	}
}