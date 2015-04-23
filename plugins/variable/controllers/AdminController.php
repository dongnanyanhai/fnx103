<?php

class AdminController extends Plugin {
    
    public function __construct() {
        parent::__construct();
        //Admin控制器进行登录验证
        if (!$this->session->is_set('user_id') || !$this->session->get('user_id')) $this->adminMsg('请登录以后再操作', url('admin/login'));
        App::auto_load('fields'); //加载字段操作函数库
        $this->view->theme = false;
	    $this->view->view_dir = PLUGIN_DIR . App::get_plugin_id() . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR;
    }
    public function indexAction(){
	    $data     = $this->variable->select();
	    $this->view->assign(array(
	        'data'     => $data,
	    ));
	    $this->view->display('admin_list');
    }
	//添加变量
	public function addAction(){
		if ($this->isPostForm()) {
            $data = $this->post('data');
            $data['name'] or exit('0变量名不能为空');
            //判断变量名是否存在
            $data['name'] = 'var_'.$data['name'];
            $result = $this->variable->getOne('name=?',$data['name']);
            $result and exit('0变量名已存在');
            $this->variable->insert($data);
            exit('1添加成功');
        }
        $this->view->display('admin_add');
	}
	//修改变量
	public function editAction(){
		$id   = (int)$this->get('id');
        if ($this->isPostForm()) {
            $data = $this->post('data');
            unset($data['name']);
            $this->variable->update($data, 'id=' . $id);
            exit('1修改成功');
        }
        $data = $this->variable->getOne('id='.$id);
        $this->view->assign(array(
            'data'   => $data,
            'id'	=> $id,
        ));
        $this->view->display('admin_edit');
	}
	//删除变量
	public function delAction(){
		$id = (int)$this->get('id');
        $this->variable->delete('id=' . $id);
		echo "1操作成功";die();
	}
	//更新缓存
	public function cacheAction(){
		$body = "<?php" . PHP_EOL . "if (!defined('IN_FINECMS')) exit();" . PHP_EOL . PHP_EOL . "/**" . PHP_EOL . " * " . $data['SITE_NAME'] . "配置" . PHP_EOL . " */" . PHP_EOL . "return array(" . PHP_EOL . PHP_EOL;
		$data = $this->variable->findAll();
		foreach($data as $v=>$t){
			switch($t['type']){
				case '字符型':
					$body .= "	'" . $t['name'] . "'  => '" . $t['content'] . "',  //". $t['info'] . PHP_EOL;
					break;
				case '数值型':
					$body .= "	'" . $t['name'] . "'  => " . $t['content'] .",	//". $t['info'] . PHP_EOL;
					break;
				case '布尔型':
					$body .= "	'" . $t['name'] . "'  => " . $t['content'] .",	//". $t['info'] . PHP_EOL;
					break;
				case '数组':
					$body .= "	'" . $t['name'] . "'  => " . htmldecode($t['content']) .",	//". $t['info'] . PHP_EOL;
					break;
				case '对象':
					$body .= "	'" . $t['name'] . "'  => " . htmldecode($t['content']) .",	//". $t['info'] . PHP_EOL;
					break;
				default:
					$body .= "	'" . $t['name'] . "'  => '" . $t['content'] . "',  //". $t['info'] . PHP_EOL;	
			}
		}
		$body .= PHP_EOL . ");";
		file_put_contents(CONFIG_DIR . 'variable.ini.php', $body);
		exit('1更新成功');
	}
}