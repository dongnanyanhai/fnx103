<?php

class PluginController extends Admin {
    
    private $dir;
    private $plugin;
    
    public function __construct() {
		parent::__construct();
		$this->dir    = PLUGIN_DIR;
		$this->plugin = $this->model('plugin');
	}
	
	/**
	 * 本地插件
	 */
	public function indexAction() {
	    $data = file_list::get_file_list($this->dir); //扫描插件目录
	    $list = array();
		if ($data) {
			foreach ($data as $id => $dir) {
				if (!in_array($dir, array('.', '..', '.svn', '', DIRECTORY_SEPARATOR)) && is_dir($this->dir . $dir)) {
					$file = $this->dir . $dir . DIRECTORY_SEPARATOR . 'config.php';
					if (file_exists($file) && filesize($file) != 0) {
						$setting = require $file;
						$setting['dir'] = $dir;
						$row    = $this->plugin->where('dir=?', $dir)->select(false);
						$list[] = $row ? $row : $setting;
					} else {
						$list[] = array('name' => '<font color="#FF0000">' . lang('a-plu-2') . '</font>', 'dir' => $dir);
					}
				}
			}
		}
	    $this->view->assign('list', $list);
	    $this->view->display('admin/plugin_list');
	}
	
	/**
	 * 插件配置
	 */
	public function setAction() {
	    $pluginid = $this->get('pluginid');
	    $data     = $this->plugin->find($pluginid);
	    if (empty($data)) $this->adminMsg(lang('a-plu-3'));
	    if ($this->post('submit')) {
	        $setting = $this->post('data');
	        $setting = array2string($setting);
	        $this->plugin->update(array('setting' => $setting), 'pluginid=' . $pluginid);
	        $this->adminMsg(lang('success'), url('admin/plugin/set/', array('pluginid' => $pluginid)), 3, 1, 1);
	    }
	    $setting = string2array($data['setting']);
	    $set     = $this->load_plugin_setting($data['dir']);
		$field   = array('data' => $set['fields']);
	    $fields  = $this->getFields($field, $setting);
	    $show    = empty($set['fields']) ? 1 : 0;
	    $this->view->assign(array(
	        'data'   => $data,
	        'show'   => $show,
	        'fields' => $fields
	    ));
	    $this->view->display('admin/plugin_set');
	}
	
	/**
	 * 安装插件
	 */
	public function addAction() {
	    $dir    = $this->get('dir');
	    $file   = $this->dir . $dir . DIRECTORY_SEPARATOR . 'config.php';
	    if (!file_exists($file)) $this->adminMsg(lang('a-plu-4'));
	    $config = require $file;
	    if ($config['typeid'] == 1) {
	        //包含控制器的插件    
	        $install = $this->dir . $dir . DIRECTORY_SEPARATOR . 'install.php';
	        if (!file_exists($install)) $this->adminMsg(lang('a-plu-5'));
	        $data = require $install;
	        if ($data) {
	            //数据表安装
	            if (is_array($data)) {
	                foreach ($data as $sql) {
	                    $this->plugin->query(str_replace('{prefix}', $this->plugin->prefix, $sql));
	                }
	            } else {
	                $this->plugin->query(str_replace('{prefix}', $this->plugin->prefix, $data));
	            }
	        }
	    }
	    //代码调用插件，直接添加表中记录
	    $config['dir']     = $dir;
	    $config['setting'] = addslashes(var_export($config['fields'], true));
		if (file_exists($this->dir . $dir . DIRECTORY_SEPARATOR . 'mark.txt')) $config['markid'] = (int)file_get_contents($this->dir . $dir . DIRECTORY_SEPARATOR . 'mark.txt');
	    $this->plugin->insert($config);
	    $this->adminMsg($this->getCacheCode('plugin') . lang('a-plu-6'), url('admin/plugin/index'), 3, 0, 1);
	}
	
	/**
	 * 卸载插件
	 */
	public function delAction() {
	    $pluginid = $this->get('pluginid');
	    $result   = $this->get('result');
	    $data     = $this->plugin->find($pluginid);
	    if (empty($data)) $this->adminMsg(lang('a-plu-3'));
		if (empty($result)) {
		    $html = lang('a-plu-7') . '<div style="padding-top:10px;text-align:center">
			<a href="' . url('admin/plugin/del', array('pluginid' => $pluginid, 'result' => 1)) . '" style="font-size:14px;">' . lang('a-plu-8') . '</a>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a href="' . url('admin/plugin/index') . '" style="font-size:14px;">' . lang('a-plu-9') . '</a></div>';
			$this->adminMsg($html, '', 3, 1, 2);
		}
	    if ($data['typeid'] == 1) {
	        //包含控制器的插件
	        $uninstall = $this->dir . $data['dir'] . DIRECTORY_SEPARATOR . 'uninstall.php';
	        if (!file_exists($uninstall)) $this->adminMsg(lang('a-plu-10'));
	        $data = require $uninstall;
	        if ($data) {
	            //数据表
	            if (is_array($data)) {
	                foreach ($data as $sql) {
	                    $this->plugin->query(str_replace('{prefix}', $this->plugin->prefix, $sql));
	                }
	            } else {
	                $this->plugin->query(str_replace('{prefix}', $this->plugin->prefix, $data));
	            }
	        }
	    }
	    //代码调用插件，直接删除表中记录
	    $this->plugin->delete('pluginid=' . $pluginid);
	    $this->adminMsg($this->getCacheCode('plugin') . lang('a-plu-11'), url('admin/plugin/index'), 1, 0, 1);
	}
	
	/**
	 * 硬盘删除插件
	 */
	public function unlinkAction() {
	    $dir      = $this->get('dir');
	    $result   = $this->get('result');
		if (empty($result)) {
		    $html = lang('a-plu-13') . '<div style="padding-top:10px;text-align:center">
			<a href="' . url('admin/plugin/unlink', array('dir' => $dir, 'result' => 1)) . '" style="font-size:14px;">' . lang('a-plu-12') . '</a>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a href="' . url('admin/plugin/index') . '" style="font-size:14px;">' . lang('a-plu-9') . '</a></div>';
			$this->adminMsg($html, '', 3, 1, 2);
		}
	    $data    = $this->plugin->getOne('dir=?', $dir);
		if ($data) {
			if ($data['typeid'] == 1) {
				//包含控制器的插件
				$uninstall = $this->dir . $data['dir'] . DIRECTORY_SEPARATOR . 'uninstall.php';
				if (!file_exists($uninstall)) $this->adminMsg(lang('a-plu-10'));
				$sqldata   = require $uninstall;
				if ($sqldata) {
					//数据表
					if (is_array($sqldata)) {
						foreach ($sqldata as $sql) {
							$this->plugin->query(str_replace('{prefix}', $this->plugin->prefix, $sql));
						}
					} else {
						$this->plugin->query(str_replace('{prefix}', $this->plugin->prefix, $sqldata));
					}
				}
			}
	        //代码调用插件，直接删除表中记录
	        $this->plugin->delete('pluginid=' . $data['pluginid']);
		}
		//删除硬盘数据
		if (is_dir($this->dir . $dir)) {
		    $this->delDir($this->dir . $dir);
			$this->adminMsg($this->getCacheCode('plugin') . lang('a-plu-14'), url('admin/plugin/index'), 3, 1, 1);
		} else {
		    $this->adminMsg(lang('a-plu-15'), url('admin/plugin/index'));
		}
	}
	
    /**
	 * 禁用/启用
	 */
	public function disableAction() {
	    $pluginid = $this->get('pluginid');
	    $data     = $this->plugin->find($pluginid);
	    if (empty($data)) $this->adminMsg(lang('a-plu-3'));
	    $disable  = $data['disable'] == 1 ? 0 : 1;
	    $this->plugin->update(array('disable' => $disable), 'pluginid=' . $pluginid);
	    $this->adminMsg($this->getCacheCode('plugin') . lang('success'), url('admin/plugin/index/'), 3, 1, 1);
	}
	
	/**
	 * 插件缓存
	 */
	public function cacheAction($show=0) {
	    $data = $this->plugin->where('disable=0')->select();
	    $row  = array();
	    foreach ($data as $t) {
	        $row[$t['dir']] = $t;
	        $row[$t['dir']]['setting'] = string2array($t['setting']);
	    }
	    $this->cache->set('plugin', $row);
	    $show or $this->adminMsg(lang('a-update'), '', 3, 1, 1);
	}
	
    /**
	 * 加载模板调用代码
	 */
	public function ajaxviewAction() {
	    $pluginid = $this->get('pluginid');
	    $data     = $this->plugin->find($pluginid);
	    if (empty($data)) exit(lang('a-plu-3'));
	    $msg  = "<textarea id='p_" . $pluginid . "' style='font-size:12px;width:100%;height:60px;overflow:hidden;'>";
	    $msg .= "{plugin('" . $data['dir'] . "')}" . PHP_EOL . "<!--将代码放到index.html" . PHP_EOL . "或者footer.html最底部-->";
	    $msg .= "</textarea>";
	    echo $msg;
	}
	
	/**
	 * 测试插件是否包含在模板中
	 */
	public function ajaxtestpAction() {
	    $id    = $this->post('id');
	    $data  = $this->plugin->find($id);
		if (empty($data)) exit('<font color=red>' . lang('a-plu-16') . '</font>');
		$code1 = "{plugin('" . $data['dir'] . "')}";
		$code2 = '{plugin("' . $data['dir'] . '")}';
		$file1 = @file_get_contents(VIEW_DIR . SYS_THEME_DIR . 'footer.html');
		$file2 = @file_get_contents(VIEW_DIR . SYS_THEME_DIR . 'index.html');
		if (strpos($file1, $code1) !== false || strpos($file1, $code2) !== false)  exit('<font color=green>√</font>');
		if (strpos($file2, $code1) !== false || strpos($file2, $code2) !== false)  exit('<font color=green>√</font>');
		exit('<font color=red>' . lang('a-plu-17') . '</font>');
	}
	
	/**
	 * 测试插件更新情况
	 */
	public function ajaxupdateAction() {
	    $id   = (int)$this->post('id');
	    $data = $this->plugin->find($id);
		if (empty($data))   exit('<font color=red>' . lang('a-plu-16') . '</font>');
		if (fn_check_url()) exit('<font color=red>' . lang('a-plu-18') . '</font>');
		if (empty($data['markid'])) exit('<font color=red>' . lang('a-plu-19') . '</font>');
		$version = fn_geturl('http://app.finecms.net/index.php?c=my&a=version&markid=' . $data['markid']);
		if (empty($version))  exit('<font color=red>' . lang('a-plu-20') . '</font>');
		$result  = $this->check_version($version, $data['version']);
		if ($result == 1) {
		    exit('<font color=red>' . lang('a-plu-21') . 'v' . $version . '</font>');
		} else {
		    exit('<font color=green>√</font>');
		}
	}
	
	/**
	 * 在线插件中心
	 */
	public function onlineAction() {
	    $name = urlencode($this->site['SITE_NAME']);
		$site = urlencode(SITE_URL);
		$list = file_list::get_file_list($this->dir); //扫描插件目录
	    $data = array();
		if ($list) {
			foreach ($list as $id => $dir) {
				if (!in_array($dir, array('.', '..', '.svn')) && is_dir($this->dir . $dir)) {
					$file = $this->dir . $dir . DIRECTORY_SEPARATOR . 'config.php';
					$mark = $this->dir . $dir . DIRECTORY_SEPARATOR . 'mark.txt';
					if (file_exists($file) && file_exists($mark)) {
						$setting = require $file;
						$markid  = (int)file_get_contents($mark);
						$data[$markid] = $setting['version'];
					}
				}
			}
		}
		$data = base64_encode(json_encode($data));
		$this->view->assign('url', 'http://app.finecms.net/?v=3&admin=' . ADMIN_NAMESPACE . '&site=' . $site . '&name=' . $name . '&data=' . $data . '&version=' . CMS_VERSION);
	    $this->view->display('admin/plugin_online');
	}
	
	/**
	 * 下载插件
	 */
	public function downAction() {
	    if (!@is_writable(PLUGIN_DIR)) $this->adminMsg(lang('a-plu-22', array('1' => $this->site['PLUGIN_DIR'])));
	    $down = base64_decode($this->get('url'));
		$dir  = $this->get('dir');
		$mark = $this->get('markid');
		$install = (int)$this->get('install');
		if (empty($down) || empty($dir) || empty($mark)) $this->adminMsg(lang('a-plu-23'));
		if ($result = fn_check_url()) {
		    $this->adminMsg($result . '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://bbs.finecms.net/forum.php?mod=viewthread&tid=100&extra=" target="_blank" style="font-size:14px">' . lang('a-plu-24') . '</a>');exit;
		}
		if (empty($install) && is_dir(PLUGIN_DIR . $dir)) $this->adminMsg(lang('a-plu-25', array('1' => $dir)));
		if ($install) {
		    //升级信息检测
			$data = $this->plugin->getOne('dir=?', $dir);
			if (empty($data))             $this->adminMsg(lang('a-plu-26', array('1' => $dir)));
			if ($data['markid'] != $mark) $this->adminMsg(lang('a-plu-27', array('1' => $dir)));
		}
		$path = APP_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'down' . DIRECTORY_SEPARATOR;
		if (!is_dir($path)) {
		    //创建下载文件临时目录
		    mkdir($path, 0777);
		}
		//保存到本地地址
		$zip_path     = $path . $dir . '.zip';
		//测试目录
		$testzip_path = $path . $dir;
		//下载压缩包
		@file_put_contents($zip_path, fn_geturl($down));
		if (filesize($zip_path) == 0) $this->adminMsg(lang('a-plu-28'));
		//解压缩
		$zip  = $this->instance('pclzip');
		$zip->PclFile($zip_path);
		if ($zip->extract(PCLZIP_OPT_PATH, $testzip_path, PCLZIP_OPT_REPLACE_NEWER) == 0) {
			@unlink($zip_path);
			$this->delDir($testzip_path);
			$this->adminMsg('Error : ' . $zip->errorInfo(true));
		} else {
		    if (!file_exists($testzip_path . DIRECTORY_SEPARATOR . 'config.php')) {
			    @unlink($zip_path);
				$this->delDir($testzip_path);
			    $this->adminMsg(lang('a-plu-29'));
			}
			//配置文件验证
			if (filesize($testzip_path . DIRECTORY_SEPARATOR . 'config.php') == 0) {
				@unlink($zip_path);
				$this->delDir($testzip_path);
				$this->adminMsg(lang('a-plu-30'));
			}
			//md5文件校验
			$md5_file = $testzip_path . 'md5.php';
			if (file_exists($md5_file)) {
				if (filesize($md5_file) == 0) {
					@unlink($zip_path);
					$this->delDir($testzip_path);
					$this->adminMsg(lang('a-plu-31'));
				}
				$md5s = require $md5_file;
				if (is_array($md5s)) {
					foreach ($md5s as $md5 => $file) {
						if (file_exists($testzip_path . $file)) {
							if (strtolower(md5(file_get_contents($testzip_path . $file))) != $md5) {
						        @unlink($zip_path);
							    $this->delDir($testzip_path);
								$this->adminMsg(lang('a-plu-31'));
							}
						}
					}
				} else {
					@unlink($zip_path);
					$this->delDir($testzip_path);
					$this->adminMsg(lang('a-plu-31'));
				}
			}
			$this->delDir($testzip_path);
		}
		//解压升级包
		if($zip->extract(PCLZIP_OPT_PATH, PLUGIN_DIR . $dir, PCLZIP_OPT_REPLACE_NEWER) == 0) {
			@unlink($zip_path);
			$this->adminMsg('Error : ' . $zip->errorInfo(true) . '<br>' . lang('a-plu-32'));
		}
		if (empty($install)) {
		    @unlink($zip_path);
			if (is_dir(PLUGIN_DIR . $dir) && is_file(PLUGIN_DIR . $dir . DIRECTORY_SEPARATOR . 'config.php')) {
			    $this->adminMsg(lang('a-plu-33'), url('admin/plugin/'), 3, 1, 1);
			} else {
			    $this->adminMsg(lang('a-plu-34'));
			}
		}
		//升级插件
		if (!@is_writable($this->dir . $dir . DIRECTORY_SEPARATOR . 'config.php')) $this->adminMsg(lang('a-plu-35'));
		$config = require $this->dir . $dir . DIRECTORY_SEPARATOR . 'config.php';
		$update = array(
		    'author'      => $config['author'],
			'version'     => $config['version'],
			'description' => $config['description']
		);
		$this->plugin->update($update, 'pluginid=' . $data['pluginid']);
		$this->adminMsg(lang('a-plu-36'), url('admin/plugin/'), 3, 1, 1);
	}
	
	/*
	 * 版本号比较
	 */
	private function check_version($v1, $v2) {
		$leng = max(substr_count($v1, '.'), substr_count($v2, '.'));
		$arr1 = explode('.', $v1);
		$arr2 = explode('.', $v2);
		$maxk = 0;
		for ($i = 0; $i <= $leng; $i ++) {
			$arr1[$i] = isset($arr1[$i]) ? $arr1[$i] : 0;
			$arr2[$i] = isset($arr2[$i]) ? $arr2[$i] : 0;
		}
		for ($i = $leng; $i >= 0; $i --) {
			if ($arr1[$i] > $arr2[$i]) {
				$maxk = 1;
			} elseif ($arr1[$i] < $arr2[$i]) {
				$maxk = 2;
			}
		}
		return $maxk;
	}
}