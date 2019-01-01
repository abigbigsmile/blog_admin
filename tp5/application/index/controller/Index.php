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


class Index extends Controller
{
	
	//设置前置操作
	protected $beforeActionList = [
			'init' => ['index'],//注意：方法名要小写
			'init' => ['single'],
	];
	

	public function init(){
		$article = new Article();
		
		
		$newArticleSet = $article->limit(6)
		->order('publictime', 'desc')
		->select();
		$hotArticleSet = $article->orderRaw('rand()')->limit(6)->select();
		$this->assign('newArticleSet',$newArticleSet);
		$this->assign('hotArticleSet',$hotArticleSet);
	}
	
    public function index() 
    {
    	  
    	$article = new Article();
    	$articleSet = $article::paginate(4);   
    	$this->assign('articleSet',$articleSet);
    	return $this->fetch('index');
    }
    
    public function search(){
    	
    	$article = new Article();
    	$title = input('post.title');
    	$searchSet = $article->where('title','like','%'.$title.'%')
    	->select();
    	if($searchSet){
    		$this->assign("searchSet",$searchSet);
    		return $this->fetch('search');
    	}else{
    		$this->success("很遗憾，没有搜索到您希望看到的文章~","index",null,2);
    	}
    	
    }
    
    public function register()
    {
    	return $this->fetch('register');
    }
    
    public function login(){
    	return $this->fetch('login');
    }
    
    public function about()
    {
    	return $this->fetch('about');
    }
    
    public function contact()
    {
    	return $this->fetch('contact');
    }
    
    public function single()
    {  
    	
    	$user = new User();
    	$article = new Article();
    	$comment = new Comment(); 	
    	
    	$request = Request::instance();
    	$articleid = $request->param('articleid');
    	
    	//查询文章
    	$articleSet = $article->where('articleid',$articleid)
    	->find();
    	
    	
    	
    	//查询文章的作者
    	$userSet = $user->where('userid',$articleSet['userid'])
    	->find();
    	

    	
		$ucSet = Db::table('comment')
					->alias('c')
					->where('c.articleid',$articleid)
					->join('user u','u.userid = c.userid')
					->join('reply r','c.commentid = r.commentidd','left')
					->paginate(3);
					//->select();
		
		
		$this->assign('userSet',$userSet);
    	$this->assign('articleSet',$articleSet);
    	$this->assign('ucSet',$ucSet);
    	return $this->fetch('single'); 
    	
    }
    
    
    public function doRegister(){
    	$user = new user();
    	$user->username = input('post.username');
    	$user->password = input('post.password');
    	$user->email = input('post.email');
    	if($user->save()){
    		$this->success("注册成功","index");
    	}else{
    		$this->error("注册失败");
    	}
    }
    

    //登录检查
    public function doLogin(){
    	
    	$user = new User();
    	$username = input('post.username');
    	$password = input('post.password');
    	$result = $user->where('username',$username)
    				   ->where('password',$password)
    				   ->find();
    	 
    	
    	if($result){
    		Session::set('user',$result->getData());
    		$this->success("登录成功","index",null,3);
    		 
    	}else{
    		$this->error("用户名或密码错误！");
    	}
    	
    }
    
    public function doLogout(){
    	// 清除session（当前作用域）
    	Session::clear();
    	return $this->index();
    }
    
    
    
    public function commentAdd(){ 
    	
    	if(!Session::has('user'))return $this->success('请先登录再评论噢','login',null,3);

    	$comment = new Comment();
    	$comment->userid = input('post.userid');
    	$articleid = input('post.articleid');
    	$comment->articleid = $articleid;
    	$comment->commentcontent = input('post.commentcontent');
    	$comment->commenttime = date("Y/m/d h:i:s");
    	$comment->save();
    	$this->redirect('single',['articleid'=>$articleid]);

    }
    
    
    public function replyAdd(){
    	 
    	if(!Session::has('user'))return $this->success('请先登录再回复噢','login',null,3);
    	$articleid = input('post.articleid');
    	
    	$reply = new Reply();
    	$reply->commentidd = input('post.commentid');
    	echo input('post.commentid');
    	
    	$reply->realid = input('post.realid');
    	$reply->realname = input('post.realname');
    	$reply->realimage = input('post.realimage');
    	$reply->replycontent = input('post.replycontent');
    	$reply->replytime = date("Y/m/d h:i:s");
    	$reply->save();
    	$this->redirect('single',['articleid'=>$articleid]);
    
    }

    
}


/*
 $request = Request::instance();
 // 获取当前域名
 echo 'domain: ' . $request->domain() . '<br/>';
 // 获取当前入口文件
 echo 'file: ' . $request->baseFile() . '<br/>';
 // 获取当前URL地址 不含域名
 echo 'url: ' . $request->url() . '<br/>';
 // 获取包含域名的完整URL地址
 echo 'url with domain: ' . $request->url(true) . '<br/>';
 // 获取当前URL地址 不含QUERY_STRING
 echo 'url without query: ' . $request->baseUrl() . '<br/>';
 // 获取URL访问的ROOT地址
 echo 'root:' . $request->root() . '<br/>';
 // 获取URL访问的ROOT地址
 echo 'root with domain: ' . $request->root(true) . '<br/>';
 // 获取URL地址中的PATH_INFO信息
 echo 'pathinfo: ' . $request->pathinfo() . '<br/>';
 // 获取URL地址中的PATH_INFO信息 不含后缀
 echo 'pathinfo: ' . $request->path() . '<br/>';
 // 获取URL地址中的后缀信息
 echo 'ext: ' . $request->ext() . '<br/>';
 */

?>
