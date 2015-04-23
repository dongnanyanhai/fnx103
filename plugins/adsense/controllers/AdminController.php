<?php

class AdminController extends Plugin {
    
    public function __construct() {
        parent::__construct();
        //Admin控制器进行登录验证
        if (!$this->session->is_set('user_id') || !$this->session->get('user_id')) $this->adminMsg('请登录以后再操作', url('admin/login'));
        App::auto_load('fields'); //加载字段操作函数库
    }
    
	/**
     * 广告位置
     */
    public function indexAction() {
	    if ($this->isPostForm()) {
		    $data = $this->post('data');
			if ($data && is_array($data)) {
				foreach ($data as $id) {
					if ($id) {
					    $this->adsense->delete('id=' . $id);
					    $this->adsense_data->delete('aid=' . $id);
					}
				}
			}
	    }
        $page = (int)$this->get('page');
		$page = (!$page) ? 1 : $page;
	    $pagelist = $this->instance('pagelist');
		$pagelist->loadconfig();
	    $total    = $this->adsense->count('adsense');
	    $pagesize = 8;
	    $url      = url('adsense/admin/index', array('page'=>'{page}'));
	    $data     = $this->adsense->page_limit($page, $pagesize)->select();
	    $pagelist = $pagelist->total($total)->url($url)->num($pagesize)->page($page)->output();
	    $this->assign(array(
	        'list'     => $data,
	        'pagelist' => $pagelist,
	    ));
	    $this->display('admin_list');
    }
    
	/**
     * 添加广告位
     */
    public function addAction() {
        if ($this->isPostForm()) {
            $data = $this->post('data');
			unset($data['id']);
            $data['adname'] or $this->adminMsg('广告位名称不能为空');
			if (empty($data['width']) || empty($data['height'])) $this->adminMsg('广告位高宽不能为空！');
            $this->adsense->insert($data);
            $this->adminMsg('操作成功', url('adsense/admin'), 3, 1, 1);
        }
        $this->display('admin_add');
    }
	
	/**
     * 修改广告位
     */
    public function editAction() {
        $id   = $this->get('id');
        $data = $this->adsense->find($id);
        if (empty($data)) $this->adminMsg('广告位不存在');
        if ($this->isPostForm()) {
            unset($data);
            $data = $this->post('data');
            $data['adname'] or $this->adminMsg('广告位名称不能为空');
			if (empty($data['width']) || empty($data['height'])) $this->adminMsg('广告位高宽不能为空！');
            $this->adsense->update($data, 'id=' . $id);
            $this->adminMsg('操作成功', url('adsense/admin'), 3, 1, 1);
        }
        $this->assign(array(
            'data'   => $data,
        ));
        $this->display('admin_add');
    }
	
	/**
     * 广告管理
     */
	public function alistAction() {
	    if ($this->isPostForm()) {
		    if ($this->post('form') == 'del') {
				$data = $this->post('data');
				if ($data && is_array($data)) {
					foreach ($data as $id) {
						if ($id) {
							$this->adsense_data->delete('id=' . $id);
						}
					}
				}
			} elseif ($this->post('form') == 'order') {
				$data = $this->post('order');
				if ($data && is_array($data)) {
					foreach ($data as $id=>$order) {
						if ($id) {
							$this->adsense_data->update(array('listorder'=>$order), 'id=' . $id);
						}
					}
				}
			}
			
	    }
		$aid  = (int)$this->get('aid');
        $page = (int)$this->get('page');
		$page = (!$page) ? 1 : $page;
	    $pagelist = $this->instance('pagelist');
		$pagelist->loadconfig();
	    $total    = $this->adsense_data->count('adsense_data', 'id', 'aid=' . $aid);
	    $pagesize = 8;
	    $url      = url('adsense/admin/alist', array('page'=>'{page}'));
	    $data     = $this->adsense_data->page_limit($page, $pagesize)->where('aid=' . $aid)->order('listorder ASC,addtime DESC')->select();
	    $pagelist = $pagelist->total($total)->url($url)->num($pagesize)->page($page)->output();
	    $this->assign(array(
	        'list'     => $data,
	        'pagelist' => $pagelist,
			'aid'      => $aid,
	        'type'     => $this->getType(),
	    ));
	    $this->display('admin_alist');
    }
	
	/**
     * 添加广告
     */
	public function addaAction() {
	    $aid = (int)$this->get('aid');
		if (empty($aid)) $this->adminMsg('广告位id不能为空！');
        if ($this->isPostForm()) {
            $data = $this->post('data');
            $data['name']   or $this->adminMsg('广告名称不能为空');
            $data['typeid'] or $this->adminMsg('显示类型不能为空');
            $data['addtime']   = time();
			$data['aid']       = $aid;
            $setting           = array();
            foreach ($data as $name=>$t) {
                if (substr($name, 0, 7) == 'setting') {
                   $setting[$name] = $t; 
                }
            }
            $data['setting']   = array2string($setting);
            $this->adsense_data->insert($data);
            $this->adminMsg('操作成功', purl('admin/alist', array('aid'=>$aid)), 3, 1, 1);
        }
		$this->assign(array(
	        'type' => $this->getType(),
			'aid'  => $aid,
	    ));
        $this->display('admin_adda');
    }
    
	/**
     * 修改广告
     */
    public function editaAction() {
        $id   = $this->get('id');
        $data = $this->adsense_data->find($id);
        if (empty($data)) $this->adminMsg('广告不存在');
		$aid  = $data['aid'];
        if ($this->post('submit')) {
            unset($data);
            $data = $this->post('data');
            $data['name']   or $this->adminMsg('广告名称不能为空');
            $data['typeid'] or $this->adminMsg('广告类型不能为空');
            $data['addtime']   = time();
            $setting           = array();
            foreach ($data as $name=>$t) {
                if (substr($name, 0, 7) == 'setting') {
                   $setting[$name] = $t; 
                }
            }
            $data['setting']   = array2string($setting);
            $this->adsense_data->update($data, 'id=' . $id);
            $this->adminMsg('操作成功', purl('admin/alist', array('aid'=>$aid)), 3, 1, 1);
        }
        $list    = $this->getType();
        $type    = $list[$data['typeid']]['fields'];
        if (empty($type)) $this->adminMsg('广告类型不存在');
        $setting = string2array($data['setting']);
        $fields  = $this->getFields(array('data'=>$type), $setting);
        $this->assign(array(
            'data'   => $data,
            'type'   => $list,
            'fields' => $fields,
			'aid'    => $data['aid']
        ));
        $this->display('admin_adda');
    }
    
	/**
     * 删除广告位
     */
    public function delAction() {
        $id = $this->get('id');
        $this->adsense->delete('id=' . $id);
		$this->adsense_data->delete('aid=' . $id);
        $this->adminMsg('操作成功', url('adsense/admin'), 3, 1, 1);
    }
    
	/**
     * 禁用广告
     */
    public function disabledAction() {
        $id   = $this->get('id');
        $data = $this->adsense_data->find($id);
        if (empty($data)) $this->adminMsg('广告不存在');
        $set  = $data['disabled'] ? 0 : 1;
        $this->adsense_data->update(array('disabled'=>$set), 'id=' . $id);
        $this->adminMsg('操作成功', url('adsense/admin/alist', array('aid'=>$data['aid'])), 3, 1, 1);
    }
    
    /**
	 * 加载调用代码
	 */
	public function ajaxviewAction() {
        $id   = $this->get('id');
        $data = $this->adsense->find($id);
	    if (empty($data)) exit('该广告(#' . $id . ')不存在');
	    $msg  = "<style>body{text-align:center;}</style><textarea id='ads_" . $id . "' style='font-size:12px;width:100%;height:90px;overflow:hidden;'>";
	    $msg .= "<!-- 广告:" . $data['adname'] . " 开始-->" . PHP_EOL;
	    $msg .= "<script language=\"javascript\" src=\"" . url('adsense/index/get', array('id'=>$id)) . "\"></script>" . PHP_EOL;
	    $msg .= "<!-- 广告:" . $data['adname'] . " 结束-->";
	    $msg .= "</textarea>";
	    echo $msg;
	}
	
	/**
	 * 缓存
	 */
	public function cacheAction() {
	    $list = $this->adsense->findAll();
	    $data = array();
	    foreach ($list as $t) {
	        $data[$t['id']] = $t;
			$data[$t['id']]['data'] = $this->adsense_data->where('aid=' . $t['id'])->order('listorder ASC,addtime DESC')->select();
	    }
	    $this->cache->set('adsense', $data);
	    $this->adminMsg('广告缓存更新成功');
	}
    
    /**
     * 广告类型
     */
    private function getType() {
        $list    = array();
        $list[1] = array(
            'name'   => '图片广告',
            'fields' => array(
                array('field'=>'setting_thumb', 'name'=>'上传图片', 'tips'=>'', 'formtype'=>'image', 'setting'=>"array('size'=>'300')", 'isshow'=>1),
                array('field'=>'setting_url',   'name'=>'链接地址', 'tips'=>'', 'formtype'=>'input', 'setting'=>"array('size'=>'300')", 'isshow'=>1),
            ),
        );
        $list[2] = array(
            'name'   => '代码广告',
            'fields' => array(
                array('field'=>'setting_content', 'name'=>'广告代码', 'tips'=>'', 'formtype'=>'textarea', 'setting'=>"array('width'=>430,'height'=>130)", 'isshow'=>1),
            ),
        );
        return $list;
    }
    
    /**
     * 动态调用广告类型字段
     */
    public function ajaxfieldAction() {
        $tid    = $this->get('tid');
        $list   = $this->getType();
        $fields = $list[$tid]['fields'];
        if (empty($fields)) exit('');
        echo $this->getFields(array('data'=>$fields));
    }
	
}