<?php
/**
 * 电信服务模块微站定义
 *
 * @author 
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Wo_tricityModuleSite extends WeModuleSite {

	//加载手机页面
	public function doMobileIndex() {
		
		global $_GPC,$_W;
		//判断是否有id
		$id = $_GPC['id'];
		$where = "";
		$params = array();
		if (!empty($id)) {
			//查取区域信息
			$ar = pdo_fetch("SELECT * FROM ".tablename("telecom_area")." WHERE id=:id",array("id"=>$id));

			$where.=' WHERE area LIKE "%'.$ar['name'].'%"';
		}
		//查取维修网线信息
		$index = pdo_fetchall("SELECT * FROM ".tablename("telecom_maintain"));
		//查找营业厅的信息
		$listen = pdo_fetchall("SELECT * FROM ".tablename("telecom_business").$where,$params);
		
		//查取区域信息
		$area = pdo_fetchall("SELECT * FROM ".tablename("telecom_area"));
		
		include $this->template("index",$listen,$area);
	}
	//加载手机活动页面
	public function doMobileEvent(){
		global $_W,$_GPC;
		
		//接收数据
		$id = $_GPC['id'];
		// var_dump($id);
		//查取营业厅的信息
		$store = pdo_fetch("SELECT * FROM ".tablename("telecom_business")." WHERE id=:id",array("id"=>$id));
		//判断是否为单独的活动
		if ($store['type'] == 0) {
			
			//查找活动信息
			$event = pdo_fetch("SELECT * FROM ".tablename("telecom_content")." WHERE pid=:id ORDER BY id DESC LIMIT 1",array("id"=>$id));
		}else{

			$event = pdo_fetch("SELECT * FROM ".tablename("telecom_content")." WHERE id=:id ORDER BY id DESC LIMIT 1",array("id"=>$store['type']));
			
		}
		
		$event['content'] = htmlspecialchars_decode($event['content']);
		$event['starttime'] = date("Y-m-d ",$event['starttime']);
		$event['endtime'] = date("Y-m-d ",$event['endtime']);
		$store = pdo_fetch("SELECT * FROM ".tablename("telecom_business")." WHERE id=:id",array("id"=>$id));
		// var_dump($event);
		// die;
		if (empty($event['content'])) {
		
			message("此店面最近无活动",$this->createMobileUrl("index"),"error");
		}else{

			//加载页面
			include $this->template("event",$store);
		}
		
	}
	//加载手机端维修店面信息
	public function doMobileMaintain(){

		global $_GPC,$_W;
		//接收数据
		$mid = $_GPC['id'];
		//查找维修网店信息
		$maintain = pdo_fetch("SELECT * FROM ".tablename("telecom_maintain")." WHERE id=:id",array("id"=>$mid));
		//加载页面
		include $this->template("maintain");

	}

	public function doWebSta() {
		//这个操作被定义用来呈现 规则列表
	}
	public function doWebOffice() {
		//这个操作被定义用来呈现 管理中心导航菜单
		global $_W,$_GPC;
		$action = 'office';
		$url = $this->createWebUrl($action, array('op' => 'list'));
		//判断是否为空
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';

		if ($operation == "list") {
			
			//设置条件
			$where = '';
			$params = array();
			//判断是否有搜索
			if(isset($_GPC['keyword']) && !empty($_GPC['keyword'])){

				$where.=' WHERE `name` LIKE :keyword OR `tell` LIKE :keywords OR `position` LIKE :keywordl';
				$params[':keywords'] = "%{$_GPC['keyword']}%";
				$params[':keyword'] = "%{$_GPC['keyword']}%";
				$params[':keywordl'] = "%{$_GPC['keyword']}%";
				
			}
			
			// //查取数据
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('telecom_business').$where);
			$pager = pagination($total, $pindex, $psize,$params);
			//查取用户信息
			$office_list = pdo_fetchall("SELECT * FROM ".tablename('telecom_business').$where." ORDER BY id ASC LIMIT ". ($pindex - 1) * $psize . ',' . $psize,$params);

			//加载页面
			include $this->template("office_list");

		}else if ($operation == "add") {
			
			//判断是否为添加
			if ($_W['ispost']) {
				
				if(empty($_GPC['name'])){

						message('营业厅名称不能为空');
				}
				if(empty($_GPC['principal'])){

						message('营业厅负责人姓名不能为空');
				}
				if(empty($_GPC['wechat'])){

						message('店长微信不能为空');
				}
				if(empty($_GPC['tell'])){

						message('电话不能为空');
				}
				if(empty($_GPC['photo'])){

						message('店面图不能为空');
				}
				if(empty($_GPC['starttime'])){

						message('营业时间不能为空');
				}
				if(empty($_GPC['position'])){

						message('地址不能为空');
				}
				if(empty($_GPC['area'])){

						message('所属区域不能为空');
				}
				if(empty($_GPC['lng'])){

						message('经度不能为空');
				}
				if(empty($_GPC['lat'])){

						message('纬度不能为空');
				}
				//接收数据
				$add['name'] = $_GPC['name']; 
				$add['area'] = $_GPC['area']; 
				$add['wechat'] = $_GPC['wechat']; 
				$add['principal'] = $_GPC['principal']; 
				$add['tell'] = $_GPC['tell']; 
				$add['position'] = $_GPC['position']; 
				$add['starttime'] = $_GPC['starttime']; 
				$add['photo'] = $_GPC['photo']; 
				$add['lng'] = $_GPC['lng']; 
				$add['lat'] = $_GPC['lat']; 
				$add['time'] = date("Y-m-d H:i:s",time()); 

				//添加数据
				$result = pdo_insert("telecom_business",$add);
				//判断
				if (!empty($result)) {
					
					message("添加网点成功",$url);
				}else{

					message("添加失败");

				}
				
			}else{
				
				//加载添加页面
				include $this->template("office_add");
			}
			

		}else if ($operation == "details") {
			
			//查取信息
			$office_details = pdo_fetch("SELECT * FROM ".tablename("telecom_business")." WHERE id=:id",array("id"=>$_GPC['id']));
			//加载页面
			include $this->template("office_details");

		}else if ($operation == "edit") {
			
			// echo "nice";
			// die;
			//查取信息
			$office_edit = pdo_fetch("SELECT * FROM ".tablename("telecom_business")." WHERE id=:id",array("id"=>$_GPC['id']));
			// var_dump($office_edit);
			//加载页面
			include $this->template("office_edit");

		}else if ($operation == "delete") {
			
			// echo "nice";
			// die;
			//查取信息
			$result1 = pdo_delete("telecom_business",array("id"=>$_GPC['id']));
			// var_dump($office_edit);
			//加载页面
			if (!empty($result1)) {
				
				message("删除成功",$url);
			}else{

				message("删除失败");
			}
		}else if ($operation == "update") {
			
			if ($_W['ispost']) {
				
				$update['name'] = $_GPC['name'];
				$update['area'] = $_GPC['area'];
				$update['wechat'] = $_GPC['wechat'];
				$update['principal'] = $_GPC['principal'];
				$update['tell'] = $_GPC['tell'];
				$update['position'] = $_GPC['position'];
				$update['starttime'] = $_GPC['starttime'];
				$update['photo'] = $_GPC['photo'];
				$update['lng'] = $_GPC['lng'];
				$update['lat'] = $_GPC['lat'];
 				$update['time'] = date("Y-m-d H:i:s",time());
 				//执行修改
 				$res = pdo_update("telecom_business",$update,array("id"=>$_GPC['id']));
 				if (!empty($res)) {
 					
 					message("修改成功",$url);
 				}else{
 					message("修改失败");
 				}
			}
		}

	}
	public function doWebMaintain() {
		//这个操作被定义用来呈现 管理中心导航菜单
		global $_W,$_GPC;
		$action = 'maintain';
		$url = $this->createWebUrl($action, array('op' => 'list'));
		//判断是否为空
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';

		if ($operation == "list") {
			
			//设置条件
			$where = '';
			$params = array();
			//判断是否有搜索
			if(isset($_GPC['keyword']) && !empty($_GPC['keyword'])){

				$where.=' WHERE `name` LIKE :keyword OR `tell` LIKE :keywords OR `position` LIKE :keywordl OR `address` LIKE :keywordlo';
				$params[':keywords'] = "%{$_GPC['keyword']}%";
				$params[':keyword'] = "%{$_GPC['keyword']}%";
				$params[':keywordl'] = "%{$_GPC['keyword']}%";
				$params[':keywordlo'] = "%{$_GPC['keyword']}%";
				
			}
			
			// //查取数据
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('telecom_maintain').$where);
			$pager = pagination($total, $pindex, $psize,$params);
			//查取用户信息
			$maintain_list = pdo_fetchall("SELECT * FROM ".tablename('telecom_maintain').$where." ORDER BY id ASC LIMIT ". ($pindex - 1) * $psize . ',' . $psize,$params);

			//加载页面
			include $this->template("maintain_list");

		}else if ($operation == "add") {
			
			//判断是否为添加
			if ($_W['ispost']) {
				
				if(empty($_GPC['name'])){

						message('维修网点名称不能为空');
				}
				if(empty($_GPC['start'])){

						message('营业时间不能为空');
				}
				if(empty($_GPC['tell'])){

						message('联系方式不能为空');
				}
				if(empty($_GPC['address'])){

						message('网点地址不能为空');
				}
				if(empty($_GPC['position'])){

						message('所属区县不能为空');
				}
				if(empty($_GPC['photo'])){

						message('店面图不能为空');
				}
				if(empty($_GPC['lng'])){

						message('经度不能为空');
				}
				if(empty($_GPC['lat'])){

						message('纬度不能为空');
				}
				//接收数据
				$add['name'] = $_GPC['name']; 
				$add['tell'] = $_GPC['tell']; 
				$add['position'] = $_GPC['position']; 
				$add['address'] = $_GPC['address']; 
				$add['photo'] = $_GPC['photo']; 
				$add['start'] = $_GPC['start']; 
				$add['lng'] = $_GPC['lng']; 
				$add['lat'] = $_GPC['lat']; 
				// $add['photo'] = $_GPC['photo']; 
				$add['time'] = date("Y-m-d H:i:s",time()); 

				//添加数据
				$result = pdo_insert("telecom_maintain",$add);
				//判断
				if (!empty($result)) {
					
					message("添加网点成功",$url);
				}else{

					message("添加失败");

				}
				
			}else{
				
				//加载添加页面
				include $this->template("maintain_add");
			}
			

		}else if ($operation == "details") {
			
			//查取信息
			$maintain_details = pdo_fetch("SELECT * FROM ".tablename("telecom_maintain")." WHERE id=:id",array("id"=>$_GPC['id']));
			//加载页面
			include $this->template("maintain_details");

		}else if ($operation == "edit") {
			
			// echo "nice";
			// die;
			//查取信息
			$maintain_edit = pdo_fetch("SELECT * FROM ".tablename("telecom_maintain")." WHERE id=:id",array("id"=>$_GPC['id']));
			// var_dump($office_edit);
			//加载页面
			include $this->template("maintain_edit");

		}else if ($operation == "delete") {
			
			// echo "nice";
			// die;
			//查取信息
			$result1 = pdo_delete("telecom_maintain",array("id"=>$_GPC['id']));
			// var_dump($office_edit);
			//加载页面
			if (!empty($result1)) {
				
				message("删除成功",$url);
			}else{

				message("删除失败");
			}
		}else if ($operation == "update") {
			
			if ($_W['ispost']) {
				
				$update['name'] = $_GPC['name'];
				$update['tell'] = $_GPC['tell'];
				$update['position'] = $_GPC['position'];
				$update['address'] = $_GPC['address'];
				$update['start'] = $_GPC['start'];
				$update['photo'] = $_GPC['photo'];
				$update['lng'] = $_GPC['lng'];
				$update['lat'] = $_GPC['lat'];
				// $update['photo'] = $_GPC['photo'];
 				$update['time'] = date("Y-m-d H:i:s",time());
 				//执行修改
 				$res = pdo_update("telecom_maintain",$update,array("id"=>$_GPC['id']));
 				if (!empty($res)) {
 					
 					message("修改成功",$url);
 				}else{
 					message("修改失败");
 				}
			}
		}
	}
	public function doWebWork() {
		//这个操作被定义用来呈现 管理中心导航菜单
		global $_W,$_GPC;
		$action = 'work';
		$url = $this->createWebUrl($action, array('op' => 'list'));
		//判断是否为空
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';

		if ($operation == "list") {
			
			//设置条件
			$where = '';
			$params = array();
			//判断是否有搜索
			if(isset($_GPC['keyword']) && !empty($_GPC['keyword'])){

				$where.=' WHERE `name` LIKE :keyword OR `content` LIKE :keywords OR `time` LIKE :keywordl OR `starttime` LIKE :keywordla OR `endtime` LIKE :keywordle';
				$params[':keywords'] = "%{$_GPC['keyword']}%";
				$params[':keyword'] = "%{$_GPC['keyword']}%";
				$params[':keywordl'] = "%{$_GPC['keyword']}%";
				$params[':keywordla'] = "%{$_GPC['keyword']}%";
				$params[':keywordle'] = "%{$_GPC['keyword']}%";
				
			}
			
			// //查取数据
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('telecom_content').$where);
			$pager = pagination($total, $pindex, $psize,$params);
			//查取用户信息
			$work_list = pdo_fetchall("SELECT * FROM ".tablename('telecom_content').$where." ORDER BY id ASC LIMIT ". ($pindex - 1) * $psize . ',' . $psize,$params);
			// var_dump($work_list);
			// die;
			foreach ($work_list as $key => $value) {
				
				$work_list[$key]['int'] = pdo_fetch("SELECT * FROM ".tablename("telecom_business")." WHERE id=:id",array("id"=>$value['pid']));
				$work_list[$key]['content'] = htmlspecialchars_decode($value['content']);
				$work_list[$key]['starttime'] = date("Y-m-d H:i:s",$value['starttime']);
				$work_list[$key]['endtime'] = date("Y-m-d H:i:s",$value['endtime']);
			}
			//加载页面
			include $this->template("work_list");

		}else if ($operation == "add") {
			
			//判断是否为添加
			if ($_W['ispost']) {
				// var_dump($_GPC);
				// die;
				if(empty($_GPC['name'])){

						message('活动主题不能为空');
				}
				if(empty($_GPC['content'])){

						message('活动内容不能为空');
				}
				if(empty($_GPC['picture'])){

						message('活动宣传图不能为空');
				}
				//接收数据
				$add['name'] = $_GPC['name']; 
				$add['pid'] = $_GPC['pid']; 
				$add['content'] = $_GPC['content']; 
				$add['picture'] = $_GPC['picture']; 
				$add['kind'] = $_GPC['kind']; 
				$add['starttime'] = strtotime($_GPC['datelimit']['start']); 
				$add['endtime'] = strtotime($_GPC['datelimit']['end']); 
				// $add['photo'] = $_GPC['photo']; 
				$add['time'] = date("Y-m-d H:i:s",time()); 
				
				//添加数据
				$result = pdo_insert("telecom_content",$add);
				$uid = pdo_insertid();
				//判断是否为全局
				if ($_GPC['kind'] == 1) {
					
					//执行修改
					pdo_update("telecom_business",array("type"=>$uid));
				}
				//判断
				if (!empty($result)) {
					
					message("添加网点成功",$url);
				}else{

					message("添加失败");

				}
				
			}else{
				//查取店面信息
				$work_add = pdo_fetchall("SELECT * FROM ".tablename("telecom_business"));
				//加载添加页面
				include $this->template("work_add");
			}
			

		}else if ($operation == "details") {
			
			//查取信息
			$work_details = pdo_fetch("SELECT * FROM ".tablename("telecom_content")." WHERE id=:id",array("id"=>$_GPC['id']));
			$work_details['content'] = htmlspecialchars_decode($work_details['content']);
			$work_details['int'] = pdo_fetch("SELECT * FROM ".tablename("telecom_business")." WHERE id=:id",array("id"=>$work_details['pid']));
			$work_details['starttime'] = date("Y-m-d H:i:s",$value['starttime']);
				$work_details['endtime'] = date("Y-m-d H:i:s",$value['endtime']);
			//加载页面
			include $this->template("work_details");

		}else if ($operation == "edit") {
			
			//查取信息
			$work_edit = pdo_fetch("SELECT * FROM ".tablename("telecom_content")." WHERE id=:id",array("id"=>$_GPC['id']));
			$activity = pdo_fetchall("SELECT * FROM ".tablename("telecom_business"));
			//加载页面
			include $this->template("work_edit",$activity);

		}else if ($operation == "delete") {
			
			// echo "nice";
			// die;
			//查取信息
			$result1 = pdo_delete("telecom_content",array("id"=>$_GPC['id']));
			// var_dump($office_edit);
			//加载页面
			if (!empty($result1)) {
				
				message("删除成功",$url);
			}else{

				message("删除失败");
			}
		}else if ($operation == "update") {
			
			if ($_W['ispost']) {
				// var_dump($_POST);
				// die;
				$update['name'] = $_GPC['name']; 
				$update['pid'] = $_GPC['pid']; 
				$update['kind'] = $_GPC['kind']; 
				$update['content'] = $_GPC['content']; 
				$update['picture'] = $_GPC['picture']; 
				$update['starttime'] = strtotime($_GPC['datelimit']['start']); 
				$update['endtime'] = strtotime($_GPC['datelimit']['end']);
 				$update['time'] = date("Y-m-d H:i:s",time());
 				//执行修改
 				// var_dump($update);
 				$res = pdo_update("telecom_content",$update,array("id"=>$_GPC['id']));
 				//判断是否修改为全部
 				if ($_GPC['kind'] == 0) {
 					
 					pdo_update("telecom_business",array("type"=>0));
 					pdo_update("telecom_business",array("type"=>$_GPC['id']),array("id"=>$_GPC['pid']));
 				}
 				if ($_GPC['kind'] == 1) {
 					
 					// pdo_update("telecom_business",array("type"=>0));
 					pdo_update("telecom_business",array("type"=>$_GPC['id']));
 				}
 				if (!empty($res)) {
 					
 					message("修改成功",$url);
 				}else{
 					message("修改失败");
 				}
			}
		}

	}
	public function doWebArea() {
		//这个操作被定义用来呈现 管理中心导航菜单
		global $_W,$_GPC;
		$action = 'area';
		$url = $this->createWebUrl($action, array('op' => 'list'));
		//判断是否为空
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';

		if ($operation == "list") {
			
			//设置条件
			$where = '';
			$params = array();
			//判断是否有搜索
			if(isset($_GPC['keyword']) && !empty($_GPC['keyword'])){

				$where.=' WHERE `name` LIKE :keyword';

				$params[':keyword'] = "%{$_GPC['keyword']}%";
				
			}
			
			// //查取数据
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('telecom_area').$where);
			$pager = pagination($total, $pindex, $psize,$params);
			//查取用户信息
			$area_list = pdo_fetchall("SELECT * FROM ".tablename('telecom_area').$where." ORDER BY id ASC LIMIT ". ($pindex - 1) * $psize . ',' . $psize,$params);
			// var_dump($area_list);
			//加载页面
			include $this->template("area_list");

		}else if ($operation == "add") {
			
			//判断是否为添加
			if ($_W['ispost']) {
				
				if(empty($_GPC['name'])){

						message('区域名称不能为空');
				}
				
				//接收数据
				$add['name'] = $_GPC['name']; 
				$add['time'] = date("Y-m-d H:i:s",time()); 

				//添加数据
				$result = pdo_insert("telecom_area",$add);
				//判断
				if (!empty($result)) {
					
					message("添加区域成功",$url);
				}else{

					message("添加失败");

				}
				
			}else{
				
				//加载添加页面
				include $this->template("area_add");
			}
			

		}else if ($operation == "details") {
			
			//查取信息
			$area_details = pdo_fetch("SELECT * FROM ".tablename("telecom_area")." WHERE id=:id",array("id"=>$_GPC['id']));
			//加载页面
			include $this->template("area_details");

		}else if ($operation == "edit") {
			
			// echo "nice";
			// die;
			//查取信息
			$area_edit = pdo_fetch("SELECT * FROM ".tablename("telecom_area")." WHERE id=:id",array("id"=>$_GPC['id']));
			// var_dump($office_edit);
			//加载页面
			include $this->template("area_edit");

		}else if ($operation == "delete") {
			
			// echo "nice";
			// die;
			//查取信息
			$result1 = pdo_delete("telecom_area",array("id"=>$_GPC['id']));
			// var_dump($office_edit);
			//加载页面
			if (!empty($result1)) {
				
				message("删除成功",$url);
			}else{

				message("删除失败");
			}
		}else if ($operation == "update") {
			
			if ($_W['ispost']) {
				
				$update['name'] = $_GPC['name'];
				
 				$update['time'] = date("Y-m-d H:i:s",time());
 				//执行修改
 				$res = pdo_update("telecom_area",$update,array("id"=>$_GPC['id']));
 				if (!empty($res)) {
 					
 					message("修改成功",$url);
 				}else{
 					message("修改失败");
 				}
			}
		}

	}


}