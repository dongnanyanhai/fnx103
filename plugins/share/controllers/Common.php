<?php

/**
 * 插件控制器公共类
 */

class Plugin extends Common {
    
	protected $data;     //插件数据
    protected $plugin;   //插件模型
	protected $viewpath; //视图目录
    
    public function __construct() {
        parent::__construct();
		$this->plugin   = $this->model('plugin');
        $this->data     = $this->plugin->where('dir=?', $this->namespace)->select(false);
		if (empty($this->data))     $this->adminMsg('插件尚未安装', url('admin/plugin'));
		if ($this->data['disable']) $this->adminMsg('插件尚未开启', url('admin/plugin'));
		date_default_timezone_set(SYS_TIME_ZONE);	//设置时区
		$this->cache	= new cache_file($this->data['dir']);	//实例化插件缓存对象
		$this->viewpath = SITE_PATH . $this->site['PLUGIN_DIR'] . '/' . $this->data['dir'] . '/views/';	//视图目录
		$this->assign(array(
		    'viewpath'  => $this->viewpath,
            'pluginid'  => $this->data['pluginid'],	
		));
    }
}