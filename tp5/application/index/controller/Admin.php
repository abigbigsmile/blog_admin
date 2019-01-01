<?php
namespace app\index\controller;
use think\Controller;
use think\Model;
use think\Request;
use think\Session;
use think\Db;
use app\index\Model\User;
use app\index\Model\Article;
use app\index\Model\Comment;
use app\index\Model\Reply;
use app\index\Model\Photo;

class Admin extends Controller
{
	public function index(){
		return $this->fetch('index');
	}
	
	public function article_list(){
		$admin = Session::get('user');
		$user = new User();
		$article = new Article();
		$comment = new Comment();
		$articleSet = $article->alias('a')
							  ->where('a.userid',$admin['userid'])							  
							  ->paginate(3);
		$this->assign('articleSet',$articleSet);
		return $this->fetch('article_list');	
	}
	
	public function picture_list(){
		$admin = Session::get('user');
		echo $admin['userid'];
		$user = new User();
		$photo = new Photo();

		$photoSet = $photo->alias('p')
							->where('p.userid',$admin['userid'])
							->paginate(3);
		$this->assign('photoSet',$photoSet);
		return $this->fetch('picture_list');
	}
	
	public function about(){
		return $this->fetch('about');
	}
		
	public function info(){
		return $this->fetch('info');
	}
		
	public function picture_show(){
		$admin = Session::get('user');
		$user = new User();
		$photo = new Photo();
		
		$photoSet = $photo->alias('p')
						  ->where('p.userid',$admin['userid'])
						  ->select();
		$this->assign('photoSet',$photoSet);
		return $this->fetch('picture_show');
	}
	
	public function article_add(){
		return $this->fetch('article_add');
	}
	
	public function articleAdd(){

		// 获取表单上传文件 例如上传了001.jpg
		$file = request()->file('image');

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
			$this->success("心情发表成功，3秒跳回主页~","article_list",null,3);
		}else{
			$this->error("很遗憾，文章发表失败！");
		}
	
	}
	
	public function articleAllow(){
		$request = Request::instance();
		$articleid = $request->param('articleid');
		$articlestate = $request->param('articlestate');
		if((int)$articlestate==1)$after=0;
		else $after=1;
		$article = new Article();
		$article->where('articleid',$articleid)
				->update(['articlestate'=>$after]);
		$this->redirect('article_list');
	}
	
	public function articleEditPage(){
		$request = Request::instance();
		$articleid = $request->param('articleid');
		$articlestate = $request->param('articlestate');
		$article = new Article();
		$articleSet = $article->where('articleid',$articleid)->find();
		$this->assign('articleSet',$articleSet);
		return $this->fetch('article_edit');
	}
	
	public function articleEdit(){
	// 获取表单上传文件 例如上传了001.jpg
		$file = request()->file('image');
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
		$articleid = input('post.articleid');
		$userid = input('post.userid');
		$title = input('post.title');
		$type = input('post.type');
		$content = input('post.content');
		$contentimage = $imgpath;
		$publictime = date("Y/m/d h:i:s");
		
		$isSuccess = $article->where('articleid',$articleid)
				->update(['userid'=>$userid,'title'=>$title,'type'=>$type,'content'=>$content,'contentimage'=>$contentimage,'publictime'=>$publictime]);
		
		if($isSuccess){
			$this->success("编辑成功，3秒跳回主页~","article_list",null,3);
		}else{
			$this->error("很遗憾，文章编辑失败！");
		}
	}
	
	public function articleDelete(){
		$request = Request::instance();
		$articleid = $request->param('articleid');
		$article = new Article();
		$isSuccess = $article->where('articleid',$articleid)->delete();
		if($isSuccess){
			$this->success("删除成功，3秒跳回主页~","article_list",null,2);
		}else{
			$this->error("很遗憾，文章删除失败！");
		}
	}
		
	public function picture_add() {
		return $this->fetch('picture_add');
		
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
		
		$this->success("照片上传成功，3秒跳回你的相册~","picture_list",null,2);
			
	}
	
	public function photoBatchDelete(){
		
		$images = input('post.photosid/a');
		
		$this->deleteInServer($images);		
		$photo = new Photo();
		$photo->destroy($images);
		$this->redirect("picture_show");				
	}
	
	public function deleteInServer($array){
		$photo = new Photo();
		foreach($array as $value){
			$path = $photo->where('photosid',$value)->column('photopath');
			$filename = ROOT_PATH . 'public' . DS . "$path[0]";
			//dump($filename);
			if(file_exists($filename))unlink($filename);
		}

	}

	public function photoAllow(){
		$request = Request::instance();
		$photosid = $request->param('photosid');
		$photostate = $request->param('photostate');
		if((int)$photostate==1)$after=0;
		else $after=1;
		$photo = new Photo();
		$photo->where('photosid',$photosid)
			  ->update(['photostate'=>$after]);
		$this->redirect('picture_list');
	}

	public function photoDelete($filename){
		$request = Request::instance();
		$photosid = $request->param('photosid');
		$photo = new Photo();
		$pho = $photo->where('photosid',$photosid)->find();
		
		$filename = ROOT_PATH . 'public' . DS . $pho["photopath"];
		if(file_exists($filename))unlink($filename);	
		
		$photo->where('photosid',$photosid)->delete();
		$this->redirect("picture_list");

	}
	
	public function infoEdit(){
		
		$userid = input('post.userid');
		$username = input('post.username');
		$sex = input('post.sex');
		$email = input('post.email');
		$work = input('post.work');
		$address = input('post.address');
		$motto = input('post.motto');
		$words = input('post.words');
		$imgpath = input('post.imgpath');
		$spaimgpath = input('post.spaimgpath');

		echo $imgpath."<br/>";
		echo $spaimgpath."<br/>";
		
		
		$file = request()->file('image');
		$info = null;
		if($file)$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
		if($info!=null){
			//if(file_exists($imgpath))unlink($imgpath);
			$a = $info->getSaveName();
			$imgp = str_replace("\\","/",$a);
			$imgpath = 'uploads/'.$imgp;
		}
		
		$file = request()->file('spaceimage');			
		if($file)$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
		if($info!=null){
			if(file_exists($spaimgpath))unlink($spaimgpath);
			$a = $info->getSaveName();
			$imgp = str_replace("\\","/",$a);
			$spaimgpath = 'uploads/'.$imgp;
				
		}
		
		$user = new User();
		$info = [
				'username'=>$username,
				'address'=>$address,
				'work'=>$work,
				'sex'=>$sex,
				'email'=>$email,
				'image'=>$imgpath,
				'spaceimage'=>$spaimgpath,
				'motto'=>$motto,
				'words'=>$words,
		];
		$isSuccess = $user->where('userid',$userid)->update($info);	
		
		
		echo $imgpath."<br/>";
		echo $spaimgpath."<br/>";
		/*
		if($isSuccess){
			$this->success("信息修改成功，3秒跳回主页~","about",null,3);
		}else{
			$this->error("很遗憾，修改失败失败！");
		}
		*/
		
	}
	
	public function passwordEdit(){
		
		$old = input('post.old');
		$new = input('post.new');
		$user = new User();
		$admin = Session::get('user');
		$userid = $admin['userid'];
		$info = $user->where('userid',$userid)->find();
		
		if($old != $info['password']){
			$this->error("原密码输入错误！！");
		}else{
			$user->where('userid',$userid)->update(['password'=>$new]);
			$this->success("密码修改成功","about",null,2);
		}
	
							
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	

}