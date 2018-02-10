<?php

namespace Api\Controller;


class VersionController extends ApiController{

	/*客户端软件升级接口实现*/
	public function check(){
                #file_put_contents('/tmp/teemo.txt',1,FILE_APPEND);
		$data['version']  = I('get.version');
		$data['platform'] = $this->client['platform'];
		$data['app_type'] = $this->client['appType'];
		#file_put_contents('/tmp/teemo.txt',json_encode($data),FILE_APPEND);
		$this->checkParam($data);
                #file_put_contents('/tmp/teemo.txt',json_encode($data));
		$result = M('Update')->field('force,version,url,app_type')
				->where(array(  'status' => array('eq', 1),
						        'platform' => array('eq', $data['platform']),
								'app_type' => array('eq', $data['app_type'])
						))
				->order(' version DESC ')->find();
		$result['isforce'] = 0;
		//在第一个版本低于第二个时，version_compare()  返回 -1；如果两者相等，返回 0；第二个版本更低时则返回 1
        if(version_compare($result['version'],$data['version']) > 0){
			$result['url'] = $result['url'];
//			$result['url'] = C('SITE_URL.www') . '/' . $result['url'];
			$result['isforce'] = $result['force'];
			unset($result['force']);
			$result['update'] = 1;
			$result['message'] = '发现新版本';
			$this->api_return('',$result);
		}else{
			$result['update'] = 0;
			$result['message'] = '不需要更新';
			$this->api_return('',$result);
		}
	}

}
