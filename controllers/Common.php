<?php

/**
 * Common.php
 * 控制器公共类 
 */

if (!defined('IN_FINECMS')) exit();

class Common extends Controller {
    
	protected $cache;
	protected $siteid;
	protected $action;
	protected $session;
	protected $namespace;
	protected $controller;
	
	protected $member;
	protected $memberinfo;
	protected $memberedit;
	protected $membermodel;
	protected $membergroup;
	protected $memberconfig;
	
	protected $cats;
	protected $site;
	protected $content;
	protected $cats_dir;
	protected $category;
    
    public function __construct() {
        parent::__construct();
		if (!file_exists(APP_ROOT . './cache/install.lock')) $this->redirect(url('install/'));
        $system_cms			= $this->load_config('version');
        $this->session		= $this->instance('session');
        $this->cache		= new cache_file();
        $this->namespace	= App::get_namespace_id();
        $this->controller	= App::get_controller_id();
        $this->action		= App::get_action_id();
        $this->site			= App::get_config();
        $this->siteid		= App::get_site_id();
		$this->category		= $this->model('category');
		$this->cats			= $this->get_category();
		$this->cats_dir		= $this->get_category_dir();
		if (!is_file(MODEL_DIR . 'Content_' . $this->siteid . 'Model.php'))	App::display_error(lang('app-14', array(1 => $this->siteid)));
		$this->content		= $this->model('content_' . $this->siteid);
		//定义网站常量
        define('SITE_PATH',		self::get_base_url());
		define('SITE_URL',		self::get_server_name() . self::get_base_url());
        define('CMS_NAME',		$system_cms['name']);
        define('CMS_VERSION',	$system_cms['version']);
        define('CMS_UPDATE',	$system_cms['update']);
        define('SITE_THEME',	self::get_theme_url());
		define('ADMIN_THEME',	SITE_PATH . basename(VIEW_DIR) . '/admin/');
		define('EXT_PATH',		SITE_PATH . EXTENSION_PATH . '/');
		define('LANG_PATH',		SITE_PATH . EXTENSION_PATH . '/language/' . SYS_LANGUAGE . '/');
		//禁止访问
		$ips = $this->cache->get('ip');
		$uip = client::get_user_ip();
		if ($uip && $ips && is_array($ips)) {
		    if (isset($ips[$uip]) && (empty($ips[$uip]['endtime']) || ($ips[$uip]['endtime'] - $ips[$uip]['addtime']) >= 0)) $this->adminMsg(lang('a-aip-6'));
			foreach ($ips as $ip => $t) {
			    if (empty($ip) || strpos($ip, '*') === false) continue;
				if (preg_match('/^' . str_replace(array('*', '.'), array('[0-9]+', '\.'), $ip) . '$/', $uip)) $this->adminMsg(lang('a-aip-6'));
			}
			unset($ips, $ip);
		}
		//载入会员系统缓存
		if (is_dir(CONTROLLER_DIR . 'member')) {
			$this->member       = $this->model('member');
			$this->membergroup  = $this->cache->get('membergroup');
			$this->membermodel  = $this->cache->get('model_member');
			$this->memberconfig	= $this->cache->get('member');
			if ($this->memberconfig['uc_use'] == 1 && $this->namespace != 'admin') {
				include EXTENSION_DIR . 'ucenter' . DIRECTORY_SEPARATOR . 'config.inc.php';
				include EXTENSION_DIR . 'ucenter' . DIRECTORY_SEPARATOR . 'uc_client' . DIRECTORY_SEPARATOR . 'client.php';
			}
			$this->memberinfo	= $this->getMember();
			$this->view->assign(array(
				'memberinfo'	=> $this->memberinfo,
				'membergroup'	=> $this->membergroup,
				'membermodel'	=> $this->membermodel,
				'memberconfig'	=> $this->memberconfig
			));
		}
		define('IS_ADMIN', $this->session->get('user_id'));
		$this->view->assign($this->site);
		$this->view->assign(array(
			's'			=> $this->namespace,
			'c'			=> $this->controller,
			'a'			=> $this->action,
			'cats'		=> $this->cats,
			'param'		=> $this->getParam(),
			'sites'		=> App::get_site(),
			'siteid'	=> $this->siteid,
			'is_admin'	=> IS_ADMIN,
		));
		//加载系统函数库和自定义函数库
        App::auto_load('function');
		App::auto_load('custom');
		date_default_timezone_set(SYS_TIME_ZONE);
    }
	
	/**
	 * 获取会员信息
	 */
	protected function getMember() {
	    if (cookie::is_set('member_id') && cookie::is_set('member_code')) {
            $uid  = (int)cookie::get('member_id');
			$code = cookie::get('member_code');
		    if (!empty($uid) && $code == substr(md5(SITE_MEMBER_COOKIE . $uid), 5, 20)) {
			    $_memberinfo    = $this->member->find($uid);
				$member_table   = $this->membermodel[$_memberinfo['modelid']]['tablename'];
				if (is_file(MODEL_DIR . ucfirst(strtolower($member_table)) . 'Model.php') && $_memberinfo && $member_table) {
				    $_member    = $this->model($member_table);
				    $memberdata = $_member->find($uid);
					if ($memberdata) {
					    $_memberinfo		= array_merge($_memberinfo, $memberdata);
						$this->memberedit	= 1; //不需要完善会员资料
					}
					if ($this->memberconfig['uc_use'] == 1 && function_exists('uc_api_mysql')) {
					    $uc = uc_api_mysql('user', 'get_user', array('username'=> $_memberinfo['username']));
						if ($uc != 0) $_memberinfo['uid'] = $uc[0];
					}
					return $_memberinfo;
				}
			}
        }
		return false;
	}
    
    /**
     * 后台提示信息
	 * msg    消息名称
	 * url    返回地址
	 * time   等待时间
	 * i      是否显示返回文字
	 * result 返回结果是否成功
     */
    protected function adminMsg($msg, $url = '', $time = 3, $i = 1, $result = 0) {
	    $this->view->assign(array(
			'i'      => $i,
		    'msg'    => $msg,
			'url'    => $url,
			'time'   => $time,
			'result' => $result
		));
		$tpl = 'admin/msg';
		if ($this->namespace != 'admin') $tpl = '../' . $tpl;
        $this->view->display($tpl);
        exit;
    }
	
	/**
     * 会员提示信息
	 * msg    消息名称
	 * url    返回地址
	 * result 返回结果是否成功
	 * time   等待时间
     */
    protected function memberMsg($msg, $url = '', $result = 0, $time = 3) {
        $this->view->assign(array(
		    'msg'    => $msg,
			'url'    => $url,
			'time'   => $time,
			'result' => $result
		));
        $this->view->display('member/msg');
        exit;
    }
    
    /**
     * 前台提示信息
	 * msg    消息名称
	 * url    返回地址
	 * i      是否显示返回文字
	 * time   等待时间
     */
    protected function msg($msg, $url = '', $i = 0, $time = 3) {
        $this->view->assign(array(
			'i'    => $i,
		    'msg'  => $msg,
			'url'  => $url,
			'time' => $time
		));
        $this->view->display('msg');
        exit;
    }
    
    /**
     * 栏目URL
     */
    protected function getCaturl($data, $page = 0) {
         return getCaturl($data, $page);
    }
    
    /**
     * 内容页URL
     */
    protected function getUrl($data, $page = 0) {
        return getUrl($data, $page);
    }
    
    /**
     * 递归创建目录
     */
    protected function mkdirs($dir) {
	    if (empty($dir)) return false;
        if (!is_dir($dir)) {
            $this->mkdirs(dirname($dir));
            mkdir($dir);
        }
    }
     
	/**
	* 加载自定义字段
	* fields 字段数组
	* data   字段默认值
	* auth   字段权限（是否必填）
	*/
    protected function getFields($fields, $data = array()) {
	    App::auto_load('fields');
	    $data_fields = '';
	    if (empty($fields['data'])) return false;
	    foreach ($fields['data'] as $t) {
		    if ($this->namespace != 'admin' && !$t['isshow']) continue;
			if (!@in_array($t['field'], $fields['merge']) && !in_array($t['formtype'], array('merge', 'fields')) && empty($t['merge'])) {
			    //单独显示的字段。
			    $data_fields .= '<tr id="fine_' . $t['field'] . '">';
				$data_fields .= isset($t['not_null']) && $t['not_null'] ? '<th><font color="red">*</font> ' . $t['name'] . '：</th>' : '<th>' . $t['name'] . '：</th>';
				$data_fields .= '<td>';
				$func         = 'content_' . $t['formtype'];
				//防止出错，把字段内容转换成数组格式
				$content      = array($data[$t['field']]);
				$content      = var_export($content, true);
				$field_config = var_export($t, true);
				if (function_exists($func)) eval("\$data_fields .= " . $func . "(" . $t['field'] . ", " . $content . ", " . $field_config . ");");
				$data_fields .= $t['tips'] ? '<div class="onShow">' . $t['tips'] . '</div>' : '';
				$data_fields .= '<span id="ck_' . $t['field'] . '"></span>';
				$data_fields .= '</td>';
				$data_fields .= '</tr>';
			} elseif ($t['formtype'] == 'merge') {
			    $data_fields .= '<tr id="fine_' . $t['field'] . '">';
				$data_fields .= '<th>' . $t['name'] . '：</th>';
				$data_fields .= '<td>' ;
				$setting      = string2array($t['setting']);
				$string       = $setting['content'];
				$regex_array  = $replace_array = array();
				if ($t['data']) {
                    foreach ($t['data'] as $field) {
                        $zhiduan  = $fields['data'][$field];
                        $str      = '';
                        $func     = 'content_' . $zhiduan['formtype'];
                        //防止出错，把字段内容转换成数组格式
                        $content	= array($data[$field]);
                        $content	= var_export($content, true);
                        $field_config = var_export($zhiduan, true);
                        if (function_exists($func)) eval("\$str = " . $func . "(" . $field . ", " . $content . ", " . $field_config . ");");
                        $regex_array[]	= '{' . $field . '}';
                        $replace_array[] = $str;
                    }
                }
				$data_fields .= str_replace($regex_array, $replace_array, $string);
				$data_fields .= '</td>';
				$data_fields .= '</tr>';
			} elseif ($t['formtype'] == 'fields') {
			    $data_fields .= '<tr id="fine_' . $t['field'] . '">';
				$data_fields .= '<th>' . $t['name'] . '：</th><td>';
				$data_fields .= '<script type="text/javascript" src="' . ADMIN_THEME . 'js/jquery-ui.min.js"></script>';
				$data_fields .= '<div class="fields-list" id="list_' . $t['field'] . '_fields"><ul id="' . $t['field'] . '-sort-items">';
				$merge_string = null;
				$contentdata  = empty($data[$t['field']]) ? array(0 => array()) : string2array($data[$t['field']]);
				$setting      = string2array($t['setting']);
				$string       = htmlspecialchars_decode($setting['content']);
				foreach ($contentdata as $i => $cdata) {
				    $data_fields .= '<li id="li_' . $t['field'] . '_' . $i . '_fields">';
				    $regex_array  = $replace_array = $o_replace_array = array();
					foreach ($fields['data'] as $field => $value) {
						if (in_array($value['field'], $t['data'])) {
							$str  = $o_str  = '';
							$func = 'content_' . $value['formtype'];
							//防止出错，把字段内容转换成数组格式
							$content	= array($cdata[$field]);
							$content	= var_export($content, true);
							$field_config	= var_export($value, true);
							if (function_exists($func)) eval("\$str = " . $func . "(" . $field . ", " . $content . ", " . $field_config . ");");
							if (empty($merge_string) && function_exists($func)) eval("\$o_str = " . $func . "(" . $field . ", null, " . $field_config . ");");
							$regex_array[]		= '{' . $field . '}';
							$replace_array[]	= str_replace('data[' . $field . ']', 'data[' . $t['field'] . '][' . $i . '][' . $field . ']', $str);
							$o_replace_array[]	= str_replace('data[' . $field . ']', 'data[' . $t['field'] . '][{finecms_block_id}][' . $field . ']', $o_str);
						}
					}
					if (empty($merge_string)) {
					    $merge_string = '<li id="li_' . $t['field'] . '_{finecms_block_id}_fields">' . str_replace($regex_array, $o_replace_array, $string) . '<div class="option"><a href="javascript:;" onClick="$(\'#li_' . $t['field'] . '_{finecms_block_id}_fields\').remove()">' . lang('a-mod-129') . '</a> <a href="javascript:;" style="cursor:move;" title="' . lang('a-mod-131') . '">' . lang('a-mod-130') . '</a></div></li>';
						$merge_string = str_replace(array("\r", "\n", "\t", chr(13)), array('', '', '', ''), $merge_string);
					}
					$data_fields .= str_replace($regex_array, $replace_array, $string);
					$data_fields .= '<div class="option"><a href="javascript:;" onClick="$(\'#li_' . $t['field'] . '_' . $i . '_fields\').remove()">' . lang('a-mod-129') . '</a> <a href="javascript:;" style="cursor:move;" title="' . lang('a-mod-131') . '">' . lang('a-mod-130') . '</a></div></li>';
				}
				$data_fields .= '</ul>
				<div class="bk10"></div>
				<div class="picBut cu"><a href="javascript:;" onClick="add_block_' . $t['field'] . '()">' . lang('a-add') . '</a></div> 
				<script type="text/javascript">
				function add_block_' . $t['field'] . '() {
				    var json  = ' . json_encode(array('echo' => $merge_string)) . ';
					var c = json["echo"];
					var id = parseInt(Math.random()*1000);
					c = c.replace(/{finecms_block_id}/ig, id);
					$("#' . $t['field'] . '-sort-items").append(c);
				}
				$("#' . $t['field'] . '-sort-items").sortable();
				</script>
				</td>';
				$data_fields .= '</tr>';
			}
	    }
	    return $data_fields;
    }
	
	/**
     *	发布文章执行的动作
	 *	$data			发布的数据
	 *	$event			事件名称，before表示之前，later表示之后
	 *	$action			动作名称，member表示会员，admin表示管理员
	 *	缓存文件格式	post_event_事件名称
	 *	缓存数据格式	array('动作名称'=>'函数文件', ... )
     */
	protected function postEvent($data, $event, $action) {
		$Events = $this->cache->get('post_event_' . $event);	//加载缓存
		if (empty($Events) || empty($Events[$action])) return true;
		foreach ($Events[$action] as $name => $file) {
			if (is_file($file)) {
				include_once $file;	//加载函数文件
				$function = $name . '_' . $event;
				if (function_exists($function)) {
					eval("\$result = " . $function . "('" . array2string($data) . "');");
					if ($result && $event == 'before') {	//发布前，有返回信息
						$action == 'member' ? $this->memberMsg($result) : $this->adminMsg($result);
					}
				}
			}
		}
		return true;
	}
	
	/**
     * 验证自定义字段
     */
	protected function checkFields($fields, $data, $msg = 1) {
	    if (empty($fields)) return false;
		foreach ($fields['data'] as $t) {
		    if ($this->namespace != 'admin' && !$t['isshow']) continue;
			if ($t['formtype'] != 'merge' && isset($t['not_null']) && $t['not_null']) {
			    if (is_null($data[$t['field']]) || $data[$t['field']] == '') {
				    if ($msg == 1) {
					    $this->adminMsg(lang('com-0', array('1' => $t['name'])));
					} elseif ($msg == 2) {
					    $this->memberMsg(lang('com-0', array('1' => $t['name'])));
					} elseif ($msg == 3) {
					    return lang('com-0', array('1' => $t['name']));
					}
				}
				if (isset($t['pattern']) && $t['pattern']) {
					$pattern = substr($t['pattern'], 0, 1) == '/' ? (substr(substr($t['pattern'], 1), -1, 1) == '/' ? substr(substr($t['pattern'], 1), 0, -1) : substr($t['pattern'], 1)) : $t['pattern'];
				    if (!preg_match('/' . $pattern . '/', $data[$t['field']])) {
					    $showmsg = isset($t['errortips']) && $t['errortips'] ? $t['errortips'] : lang('com-1', array('1' => $t['name']));
					    if ($msg == 1) {
					        $this->adminMsg($showmsg);
						} elseif ($msg == 2) {
							$this->memberMsg($showmsg);
						} elseif ($msg == 3) {
							return $showmsg;
						}
					}
				} 
			}
	    }
	}
    
    /**
     * 生成水印图片
     */
    protected function watermark($file) {
        if (!$this->site['SITE_WATERMARK'] || $this->site['SITE_THUMB_TYPE']) return false;
        $image = $this->instance('image_lib');
        if ($this->site['SITE_WATERMARK'] == 1) {
            $image->set_watermark_alpha($this->site['SITE_WATERMARK_ALPHA']);
            $image->make_image_watermark($file, $this->site['SITE_WATERMARK_POS'], $this->site['SITE_WATERMARK_IMAGE']);
        } else {
            $image->set_text_content($this->site['SITE_WATERMARK_TEXT']);
            $image->make_text_watermark($file, $this->site['SITE_WATERMARK_POS'], $this->site['SITE_WATERMARK_SIZE']);
        }
    }
    
    /**
     * 生成网站地图
     */
    protected function sitemap() {
        sitemap_xml();
    }
	
	/**
     * 删除目录及文件
     */
    protected function delDir($filename) {
        if (empty($filename)) return false;
        if (is_file($filename) && file_exists($filename)) {
            unlink($filename);
        } else if ($filename != '.' && $filename!='..' && is_dir($filename)) {
            $dirs = scandir($filename);
            foreach ($dirs as $file) {
                if ($file != '.' && $file != '..') {
                    $this->delDir($filename . '/' . $file);
                }
            }
            rmdir($filename);
        }
    }
	
	/**
     * 用户是否能够查看未审核信息
     */
	protected function userShow($data) {
	    if ($data['status'] != 0) return true;
	    if ($this->session->is_set('user_id') && $this->session->get('user_id')) return true;
		if (cookie::is_set('member_id') && cookie::get('member_id') == $data['userid'] && $data['sysadd'] == 0) return true;
		return false;
	}
	
    /**
     * 验证验证码
     */
	protected function checkCode($value) {
	    $code  = $this->session->get('captcha');
		$value = strtolower($value);
		$this->session->delete('captcha');
		return $code == $value ? true : false;
	}
	
	/**
     * 模型栏目
     */
	protected function getModelCategory($modelid) {
	    $data = array();
		foreach ($this->cats as $cat) {
		    if ($modelid == $cat['modelid'] && $cat['typeid'] == 1 && $cat['child'] == 0) $data[$cat['catid']] = $cat;
		}
		return $data;
	}
	
	/**
     * 模型的关联表单
     */
	protected function getModelJoin($modelid) {
	    if (empty($modelid)) return null;
		$data   = $this->get_model('form');
		$return = null;
		if ($data) {
		    foreach ($data as $t) {
			    if ($t['joinid'] == $modelid) $return[] = $t;
			}
		}
		return $return;
	}
	
	/**
     * 可在会员中心显示的表单
     */
	protected function getFormMember() {
		$data   = $this->get_model('form');
		$join   = $this->cache->get('model_join_' . $this->siteid);
		$return	= null;
		if ($data) {
		    foreach ($data as $id=>$t) {
				if (isset($t['setting']['auth']['siteuser']) && $t['setting']['auth']['siteuser'] && $t['setting']['auth']['site'] && in_array($this->siteid, $t['setting']['auth']['site'])) continue;
			    if (isset($t['setting']['form']['member']) && $t['setting']['form']['member'] && !$this->memberPost($t['setting']['auth'])) {
				    $t['joinname'] = isset($join[$t['joinid']]['modelname']) && $join[$t['joinid']]['modelname'] ? $join[$t['joinid']]['modelname'] : '';
				    $return[$id]   = $t;
				}
			}
		}
		return $return;
	}
	
	/**
     * 格式化字段数据
     */
	protected function getFieldData($model, $data) {
	    if (!isset($model['fields']['data']) || empty($model['fields']['data']) || empty($data)) return $data;
	    foreach ($model['fields']['data'] as $t) {
			if (!isset($data[$t['field']])) continue;
			if ($t['formtype'] == 'editor') {
			    //把编辑器中的HTML实体转换为字符
				$data[$t['field']] = htmlspecialchars_decode($data[$t['field']]);
			} elseif (in_array($t['formtype'], array('checkbox', 'files', 'fields'))) {
				//转换数组格式
				$data[$t['field']] = string2array($data[$t['field']]);
			}
		}
		return $data;
	}
	
	/**
     * 检查文件/目录名称是否规范
     */
	protected function checkFileName($file) {
		if (strpos($file, '../') !== false || strpos($file, '..\\') !== false) return true;
		return false;
	}
	
	/**
     * 会员投稿权限判断
     */
	protected function memberPost($data) {
		if (isset($data['siteuser']) && $data['siteuser'] && $data['site'] && in_array($this->siteid, $data['site'])) return true;
		if (isset($data['memberpost']) && $data['memberpost']) {
		    if (isset($data['modelpost']) && in_array($this->memberinfo['modelid'], $data['modelpost'])) return true;
			if (isset($data['grouppost']) && in_array($this->memberinfo['groupid'], $data['grouppost'])) return true;
		}
		return false;
	}
	
	/**
	 * 生成内容html
	 */
	protected function createShow($data, $page = 1) {
	    if (empty($data)) return false;
		ob_start();
		$id		= $data['id'];
	    $catid	= $data['catid'];
	    $cat	= $this->cats[$catid];
		if ($cat['setting']['url']['use'] == 0 || $cat['setting']['url']['tohtml'] == 0  || $cat['setting']['url']['show'] == '') return false;
	    $table	= $this->model($cat['tablename']);
	    $_data	= $table->find($id);
	    $data	= array_merge($data, $_data);
	    $model	= $this->get_model();
	    $data	= $this->getFieldData($model[$cat['modelid']], $data);
	    $data	= $this->get_content_page($data, 1, $page);
	    $url	= substr($this->getUrl($data, $page), strlen(self::get_base_url())); //去掉域名部分
	    if (substr($url, -5) != '.html') { 
			$file	= 'index.html'; //文件名 
			$dir	= $url; //目录
		} else {
			$file	= basename($url);
			$dir	= str_replace($file, '', $url);
		}
		$this->mkdirs($dir);
		$dir		= substr($dir, -1) == '/' ? substr($dir, 0, -1) : $dir;
		$htmlfile	= $dir ? $dir . '/' . $file : $file;
		if ($data['status'] == 0) {
		    @unlink($htmlfile);
			if (isset($pagelist) && is_array($pagelist)) {
		        foreach ($pagelist as $p=>$u) {
				    $file = str_replace(self::get_base_url(), '', $u);
				    @unlink($file);
				}
			}
			return false;
		}
		$prev_page	= $this->content->getOne("`catid`=$catid AND `id`<$id AND `status`<>0 ORDER BY `id` DESC", null, 'title,url,hits');
		$next_page	= $this->content->getOne("`catid`=$catid AND `id`>$id AND `status`<>0", null, 'title,url,hits');
	    $data['content'] = relatedlink($data['content']);
	    $this->view->assign(array(
	        'cat'		=> $cat,
		    'cats'		=> $this->cats,
	        'page'		=> $page,
	        'pageurl'	=> urlencode(getUrl($data, '{page}')),
	        'prev_page'	=> $prev_page,
	        'next_page'	=> $next_page
	    ));
	    $this->view->assign($data);
	    $this->view->assign(showSeo($data, $page));
		if ($this->namespace == 'admin') $this->view->setTheme(true);
	    $this->view->display(substr($cat['showtpl'], 0, -5));
		if ($this->namespace == 'admin') $this->view->setTheme(false);
		if (!file_put_contents($htmlfile, ob_get_clean(), LOCK_EX)) $this->adminMsg(lang('a-com-11', array('1' => $htmlfile)));
		$htmlfiles		= $this->cache->get('html_files');
		$htmlfiles[]	= $htmlfile;
		$this->cache->set('html_files', $htmlfiles);
		if (isset($_data['content']) && strpos($_data['content'], '{-page-}') !== false) {
			$content	= explode('{-page-}', $_data['content']);
			$pageid		= $page <= 0 ? 1 : $page;
			$nextpage	= $pageid + 1;
			if ($nextpage <= count($content)) $this->createShow($data, $nextpage);
		}
		return true;
	}
	
	/**
	 * 生成表单html
	 */
	protected function createForm($mid, $data) {
	    if (empty($data) || empty($mid)) return false;
		if ($data['status']	!= 1) return false;
		$url	= substr(form_show_url($mid, $data), strlen(self::get_base_url())); //去掉域名部分
	    if (substr($url, -5) != '.html') { 
			$file	= 'index.html'; //文件名 
			$dir	= $url; //目录
		} else {
			$file	= basename($url);
			$dir	= str_replace($file, '', $url);
		}
		$this->mkdirs($dir);
		$dir		= substr($dir, -1) == '/' ? substr($dir, 0, -1) : $dir;
		$htmlfile	= $dir ? $dir . '/' . $file : $file;
		ob_start();
		$model = $this->get_model('form');
		$form  = $model[$mid];
	    if (empty($form)) return false;
		if (!isset($form['setting']['form']['url']['tohtml']) || empty($form['setting']['form']['url']['tohtml'])) return false;
	    if (isset($form['fields']) && $form['fields']) $data = $this->getFieldData($form, $data);
		$this->view->assign($data);
	    $this->view->assign(array(
			'table'				=> $form['tablename'],
			'modelid'			=> $mid,
			'form_name'			=> $form['modelname'],
	        'meta_title'		=> $form['setting']['form']['meta_title'],
	        'meta_keywords'		=> $form['setting']['form']['meta_keywords'], 
	        'meta_description'	=> $form['setting']['form']['meta_description']
	    ));
		if ($this->namespace == 'admin') $this->view->setTheme(true);
		$this->view->display(substr($form['showtpl'], 0, -5));
		if ($this->namespace == 'admin') $this->view->setTheme(false);
		if (!file_put_contents($htmlfile, ob_get_clean(), LOCK_EX)) $this->adminMsg(lang('a-com-11', array('1' => $htmlfile)));
		$htmlfiles		= $this->cache->get('html_files');
		$htmlfiles[]	= $htmlfile;
		$this->cache->set('html_files', $htmlfiles);
		return true;
	}
	
	/**
	 * 获取栏目缓存
	 */
	protected function get_category() {
		$cats = $this->cache->get('category_' . $this->siteid);
		if ($this->site['SITE_EXTEND_ID']) {
			$data = $this->cache->get('category_' . $this->site['SITE_EXTEND_ID']);
			if (empty($data)) return $cats;
			foreach ($data as $catid => $t) {
				$t['items']	  = $cats[$catid]['items'];
				$cats[$catid] = $t;
			}
		}
		return $cats;
	}
	
	/**
	 * 获取栏目缓存目录名称
	 */
	protected function get_category_dir() {
		$cats = $this->cache->get('category_dir_' . $this->siteid);
		if ($this->site['SITE_EXTEND_ID']) {
			$data = $this->cache->get('category_dir_' . $this->site['SITE_EXTEND_ID']);
			return empty($data) ? $cats : $data;
		}
		return $cats;
	}
	
	/**
	 * 获取模型缓存(非会员模型)
	 */
	protected function get_model($name = 'content', $site = 0) {
		return get_model_data($name, $site);
	}
	
	/**
	 * 检查会员名是否符合规定
	 */
	protected function is_username($username) {
		$strlen  = strlen($username);
		$pattern = $this->memberconfig['username_pattern'] ? $this->memberconfig['username_pattern'] : '/^[a-zA-Z0-9_][a-zA-Z0-9_]+$/';
		if(!preg_match($pattern, $username)){
			return false;
		} elseif ( 20 < $strlen || $strlen < 2 ) {
			return false;
		}
		return true;
    }
	
	/**
	 * 内容分页
	 */
	protected function get_content_page($data, $type, $page) {
		if (strpos($data['content'], '{-page-}') !== false) {	//内容分页判断
			$content  = explode('{-page-}', $data['content']);
			$pageid   = count($content) >= $page ? ($page - 1) : (count($content) - 1);
			$page_id  = 1;
			$pagelist = $pagename = array();
			foreach ($content as $k => $t) {
				if (preg_match('/\[stitle\](.*)\[\/stitle\]/', $t, $stitle)) {	//子标题判断
					$content[$k] = str_replace($stitle[0], '', $t);
					$pagename[$page_id]	= $stitle[1];
					if ($k == $pageid) $data['stitle'] = $stitle[1];
				} else {
					$pagename[$page_id]	= $page_id;
				}
				$pagelist[$page_id] = $type == 0 ? getCaturl($data, $page_id) : getUrl($data, $page_id);
				$page_id ++ ;
			}
			$data['content'] = $content[$pageid];
			$this->view->assign(array(
				'page'			=> $page,
				'contentname'	=> $pagename,
				'contentpage'	=> $pagelist
			));
		}
		return $data;
	}
	
	/**
	 * 更新登录信息
	 */
	public function update_login_info($data) {
		$userip	= client::get_user_ip();
		if (empty($data['loginip']) || $data['loginip'] != $userip) {	//如果会员表中的登录ip不一致，则重新记录
			$update = array(
				'lastloginip'   => $data['loginip'], 
				'lastlogintime' => $data['logintime'],
				'loginip'       => $ip,
				'logintime'     => time(),
			);
			$this->member->update($update, 'id=' . $data['id']);
		}
	}
	
}