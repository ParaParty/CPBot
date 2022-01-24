<?php
namespace Workerman\Bot;

use Workerman\Worker;
use Workerman\Connection\AsyncTcpConnection;

Class Bot{
	public $ws_url;
	
	public $api;
	public $event;
	
	public function __construct($ws_url){
		$this->ws_url = $ws_url;

		Worker::$pidFile = __DIR__ . '/../../../../workerman.pid';
		Worker::$logFile = __DIR__ . '/../../../../workerman.log';

		$worker = new Worker("");

		$worker->onWorkerStart = function($worker){
			onWorkerStart($worker);
			#---------------------api----------------------
			$this->api = new AsyncTcpConnection($this->ws_url . "/api");
			echo "[api]连接CQHTTP中...\n";

			$this->api->onConnect = function($connection)
			{
				echo "[api]连接成功，可以开始调用\n";
				$connection->status = true;
				new Data($connection);
				onConnectApi();
			};

			$this->api->onMessage = function($connection, $data) {
				onMessageApi(json_decode($data, true));
			};
			
			$this->api->onClose = function($connection) {
				echo "[api]失去与主机的连接，5秒后尝试重连...\n";
				$connection->status = false;
				$connection->reConnect(5);
				onCloseApi();
			};

			$this->api->connect();
			#---------------------event----------------------
			$this->event = new AsyncTcpConnection($this->ws_url . "/event");
			echo "[event]连接CQHTTP中...\n";

			$this->event->onConnect = function($connection)
			{
				echo "[event]连接成功，开始接收信息\n";
				onConnectEvent();
			};

			//信息回调参数请访问go-cqhttp标准: https://docs.go-cqhttp.org/api
			$this->event->onMessage = function($connection, $data) {
				$data = json_decode($data, true);
				switch($data['post_type'])
				{
					case 'message'://消息事件
						if($data['message_type'] == 'private') {
							PrivateMessageEvent($data, $data['time'], $data['self_id'], $data['post_type'], $data['message_type'],
							$data['sub_type'], $data['message_id'], $data['user_id'], $data['message'], $data['raw_message'],
							$data['font'], $data['sender']);
						}else if($data['message_type'] == 'group') {
							GroupMessageEvent($data, $data['time'], $data['self_id'], $data['post_type'], $data['message_type'],
							$data['sub_type'], $data['message_id'], $data['group_id'], $data['user_id'], $data['anonymous'],
							$data['message'], $data['raw_message'], $data['font'], $data['sender']);
						}
						break;
					case 'notice'://通知事件
						switch($data['notice_type'])
						{
							case 'group_upload'://群文件上传
								//GroupFileUploadEvent();
								break;
							case 'group_admin'://群管理员变动
								GroupAdminEvent($data['time'], $data['self_id'], $data['post_type'], $data['notice_type'],
									$data['sub_type'], $data['group_id'], $data['user_id']);
								break;
							case 'group_decrease'://群成员减少
								//do some thing
								break;
							case 'group_increase'://群成员增加
								//do some thing
								break;
							case 'group_ban'://群禁言
								GroupBanEvent($data['time'], $data['self_id'], $data['post_type'], $data['notice_type'],
									$data['sub_type'], $data['operator_id'], $data['group_id'], $data['user_id'], $data['duration']);
								break;
							case 'friend_add'://好友添加
								FriendAddEvent($data['time'], $data['self_id'], $data['post_type'], $data['notice_type'],
									$data['user_id']);
								break;
							case 'group_recall'://群消息撤回
								//do some thing
								break;
							case 'friend_recall'://好友消息撤回
								//do some thing
								break;
							case 'notify'://群通知
								switch($data['sub_type'])
								{
									case 'poke'://戳一戳
										//do some thing
										break;
									case 'lucky_king'://红包运气王
										//do some thing
										break;
									case 'honor'://群荣誉
										//do some thing
										break;
								}
								break;
						}
						break;
					case 'request'://请求事件
						if($data['request_type'] == 'friend') {//加好友请求
							FriendRequestEvent($data, $data['time'], $data['self_id'], $data['post_type'], $data['request_type'], $data['user_id'], $data['comment'], $data['flag']);
						}else if($data['request_type'] == 'group') {//加群请求or邀请
							GroupMemberAddRequestEvent($data, $data['time'], $data['self_id'], $data['post_type'], $data['request_type'], $data['sub_type'], $data['group_id'], $data['user_id'], $data['comment'], $data['flag']);
						}
						break;
					case 'meta_event'://元事件
                        //do some thing
						break;
					case 'CQLifecycleMetaEvent'://ws生命周期
						//do some thing
						break;
					case 'CQHeartbeatMetaEvent'://ws心跳
						//do some thing
						break;
					default:
						print_r($data);
						break;
				}
			};
			
			$this->event->onClose = function($connection) {
				echo "[event]失去与主机的连接，5秒后尝试重连...\n";
				$connection->reConnect(5);
				onCloseEvent();
			};

			$this->event->connect();
		};

		Worker::runAll();
	}
}
