<?php
	namespace app\index\Controller;
	use think\Controller;
	use think\Request;
	use think\Session;
	use think\Db;
	use app\index\Model\Article;
	use app\index\Model\Comment;
	use app\index\Model\Message;
	use app\index\Model\Photo;
	use app\index\Model\Whisper;
	use app\index\Model\Whisperpicture;
	
	
	class User extends Controller{
		
		
		public function index()
		{

			$article = new Article();
			$message = new Message();
			
			$request = Request::instance();
			$userid = $request->param('userid');
	
			$userSet = Db::query('select * from user where userid=:id',['id'=>$userid]);
			$articleSet = $article->where('userid',$userid)->paginate(4,false,['query'=>request()->param()]);
			
			$comment = new Comment();
			$commentSet = $comment->where('whisperid',8)
									->alias('c')
									->join('user u','c.userid = u.userid')
									->select();
			//dump($commentSet);
			
			$this->assign('userSet',$userSet);
			$this->assign('articleSet',$articleSet);
			return $this->fetch('index');

		}
		
		public function about()
		{				
			$request = Request::instance();
			$userid = $request->param('userid');			
			$userSet = Db::query('select * from user where userid=:id',['id'=>$userid]);							
			$this->assign('userSet',$userSet);		
			return $this->fetch('about');
		}
		
		public function album(){
			
			$photo = new Photo();
			$request = Request::instance();
			$userid = $request->param('userid');
			
			//echo "id:".$userid."<br/>";
			
			$photoSet = $photo->where('userid',$userid)->paginate(6,false,['query'=>request()->param()]);
			$userSet = Db::query('select * from user where userid=:id',['id'=>$userid]);
			
			$this->assign('photoSet',$photoSet);
			$this->assign('userSet',$userSet);

			return $this->fetch('album');
		}
		
		public function photoUpload(){
			return $this->fetch('photoUpload');
		}
		
		public function whisperWrite(){
			return $this->fetch('whisperWrite');
		}
		

		
		public function details()
		{		
			/* $user = new User();
			
			$comment = new Comment(); */
			$article = new Article();
			$request = Request::instance();
			
			//获得文章id
			$articleid = $request->param('articleid');
			
			$articleSet = $article->where('articleid',$articleid)->find();
				
			
			
			//查询文章主
			$userid = $article->where('articleid',$articleid)->value('userid');					
			$userSet = Db::query('select * from user where userid=:id',['id'=>$userid]);

			$usSet =  Db::table('comment')
						->alias('c')
						->where('articleid',$articleid)
						->join('user u','c.userid = u.userid')
						->select();
			
						
			/*$articleAll = $article->where('userid',$userid)
								  ->select();*/

			$this->assign('userSet',$userSet);
			$this->assign('usSet',$usSet);
			$this->assign('result',$articleSet);
			return $this->fetch('details');
			
			
		}
		
		public function nextDetails(){
			
			$article = new Article();
			$userid = input('post.uid');
			$articleid = input('post.aid');
			$articleAll = $article->where('userid',$userid)
								  ->select();
			$count = count($articleAll);
			$num = rand(0,$count);

			$articleAll = $article->where('userid',$userid)
			 ->select();

			$this->redirect('details',['articleid'=>$articleAll[$num]['articleid']]);
			
		}
			
		
		public function leacots()
		{
			$user = new User();
			$message = new Message();
			 
			$request = Request::instance();
			$userid = $request->param('userid');
			
			
			$userSet = Db::query('select * from user where userid=:id',['id'=>$userid]);
			
			$umSet = Db::table('message')
						->alias('m')
						->where('m.toid',$userid)
						->join('user u','m.userid = u.userid')
						->paginate(5,false,['query'=>request()->param()]);
			
						
						//paginate(6,false,['query'=>request()->param()]);
				
			//dump($userSet);
			$this->assign('userSet',$userSet);
			$this->assign('umSet',$umSet);

			return $this->fetch('leacots');
		}
		
		
		public function whisper()
		{
			//if(!Session::has('user'))$this->success('先登录才能发表心情哦 ~','index/login',null,3);		
			$whisper = new Whisper();
			$request = Request::instance();
			$userid = $request->param('userid');
			
			$userSet = Db::query('select * from user where userid=:id',['id'=>$userid]);
			$whisperSet = Db::table('whisper')
							->alias('w')
							->join('user u','w.userid = u.userid')
							->paginate(2,false,['query'=>request()->param()]);
			
			$this->assign('userSet',$userSet);
			$this->assign('whisperSet', $whisperSet);

			
			//dump($whisperSet);
			return $this->fetch('whisper');
		}
		
		public function getWhisperComment(){
			$request = Request::instance();
			$whisperid = $request->post('whisperid');
			$comment = new Comment();
			
			$commentSet = $comment->where('whisperid',$whisperid)
								  ->alias('c')
								  ->join('user u','c.userid = u.userid')
								  ->select();			
			return $commentSet;
		}
		
		public function JSON($array){
			$this->arrayRecursive($array,'urlencode',true);
			$json = json_encode($array);
			return urldecode($json);
		}
			
		
		
		public function writePage(){
			if(!Session::has('user'))$this->success('先登录才能发表心情哦 ~','index/login',null,3);
			return $this->fetch('writePage');
		}
		

		
		public function articleAdd(){
			
			// 获取表单上传文件 例如上传了001.jpg
			$file = request()->file('image');
			
			//echo "file:".$file;
			
			// 移动到框架应用根目录/public/uploads/ 目录下
			if($file)$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
			else echo "null";
			if($info){
				// 成功上传后 获取上传信息
				$a = $info->getSaveName();
				$imgp = str_replace("\\","/",$a);
				$imgpath = 'uploads/'.$imgp;
			
			}else{
				// 上传失败获取错误信息
				echo $file->getError();
			}
			
			$article = new Article();
			$article->userid = input('post.userid');
			$article->title = input('post.title');
			$article->type = input('post.type');
			$article->content = input('post.content');
			$article->contentimage = $imgpath;
			$article->publictime = date("Y/m/d h:i:s");
			if($article->save()){
				$this->success("心情发表成功，3秒跳回主页~","index/index",null,3);
			}else{
				$this->error("很遗憾，文章发表失败！");
			}
		
		}
		

		
		public function messageAdd(){
			
			if(!Session::has('user'))return $this->success('请先登录再留言噢','index/login',null,2);
			
			$request = Request::instance();
			$userid = $request->param('userid');
			$message = new Message();
			$message->toid = $userid;
			$message->userid = input('post.userid');			
			$message->messagecontent = input('post.messagecontent');
			$message->messagetime = date("Y/m/d h:i:s");
			$message->save();
			//return $this->();
			$this->redirect('leacots',['userid'=>$userid]);
		}
		
		public function commentAdd(){
			if(!Session::has('user'))return $this->success('请先登录再评论噢','index/login',null,3);			
			$comment = new Comment();
			$articleid = input('post.articleid');
			
			$comment->userid = input('post.userid');
			$comment->articleid = $articleid;	
			$comment->commentcontent = input ( 'post.commentcontent' );
			$comment->commenttime = date ( "Y/m/d h:i:s" );
			$comment->save ();
			//return $this->details ();
			$this->redirect('details',['articleid'=>$articleid]);
	}
	
	
	
	public function photoAdd() {
		
		$request = Request::instance();
		// 获取表单上传文件
		$files = request ()->file ( 'image' );
		$userid = input('post.userid');
		$mood = input('post.mood');
		$position = input('post.position');
	
		foreach ( $files as $file ) {
			// 移动到框架应用根目录/public/uploads/ 目录下
			$info = $file->move ( ROOT_PATH . 'public' . DS . 'uploads' );
			if ($info) {					
					// 成功上传后 获取上传信息
					$a = $info->getSaveName();
					$imgp = str_replace("\\","/",$a);
					$imgpath = 'uploads/'.$imgp;
					$phototime = date('Y/m/d h:i:s');
					$data = ['userid' => $userid, 'photopath' => $imgpath, 'position' => $position,'mood' => $mood,'phototime' => $phototime];
					Db::table('photo')->insert($data);
													
				}else{
					// 上传失败获取错误信息
					echo $file->getError();
				}
		}	
		
		$this->success("照片上传成功，3秒跳回你的相册~","album?userid=$userid",null,2);
			
	}
	
	
	
	public function whisperAdd() {
	
		$request = Request::instance();
		$whisper = new Whisper();
		
		// 获取表单上传文件
		$files = request ()->file ( 'image' );
		$userid = input('post.userid');
		
		$whisper->userid = $userid;
		$whisper->whispercontent = input('post.whispercontent');
		$whisper->whispertime = date('Y/m/d H:i:s');
		
		$arr = array();
		//$whisper->save();
		//$whisperid = $whisper->whisperid;

		foreach ( $files as $file ) {
			// 移动到框架应用根目录/public/uploads/ 目录下
			$info = $file->move ( ROOT_PATH . 'public' . DS . 'uploads' );
			if ($info) {
				// 成功上传后 获取上传信息
				$a = $info->getSaveName();
				$imgp = str_replace("\\","/",$a);
				$imgpath = 'uploads/'.$imgp;
				array_push($arr, $imgpath);
				//$imgpath = $imgpath.";";
				//$data = ['whisperid' => $whisperid, 'picturepath' =>$imgpath];
				//Db::table('whisperpicture')->insert($data);					
			}else{
				// 上传失败获取错误信息
				echo $file->getError();
			}
		}
		$imagepath = implode('-',$arr);
		$whisper->picturepath = $imagepath;
		$whisper->save();
		$this->success("微语发布成功，3秒跳回首页~","whisper?userid=$userid",null,3);


	}
	
	public function whisperCommentAdd(){
		if(!Session::has('user'))return $this->success('请先登录再评论噢','index/login',null,3);
		$comment = new Comment();
		$whisper = new Whisper();
		$userid = input('post.userid');
		$whisperid = input('post.whisperid');
		
		
		$comment->userid = $userid;
		$comment->whisperid = $whisperid;
		$comment->articleid = 0;
		$comment->commentcontent = input ( 'post.commentcontent' );
		$comment->commenttime = date ( "Y/m/d h:i:s" );
		$comment->save ();
		
		$whisper->where('whisperid',$whisperid)
				->setInc('commentcount');
		
		$this->redirect('whisper',['userid'=>$userid]);
	}
	
	
	public function loveAdd(){
		$request = Request::instance();
		$whisperid = $request->post('whisperid');
		$love = $request->post('loved');
		$whisper = new Whisper();
		$whisper->where('whisperid',$whisperid)
				->update(['love' => $love]);
	}

	
	
	
	
	
	


}
?>