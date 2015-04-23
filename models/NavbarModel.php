<?php

/**
* Navbar 模型
*/
class NavbarModel extends Model{
	
	public function get_primary_key(){
		return $this->primary_key = "navid";
	}

	public function set($navid, $data){
		$data['site'] = APP::get_site_id();
		if($navid){
			$this->update($data, 'navid=' . $navid);
			return true;
		}
		$this->insert($data);
		if($this->get_insert_id()) return true;
		return false;
	}

	public function del($navid){
		$this->delete('navid='.$navid.' AND site=' . APP::get_site_id());
		$table = $this->prefix.'navbar_data';
		$this->query('delete from '.$table . ' where navid='.$navid);
	}
}