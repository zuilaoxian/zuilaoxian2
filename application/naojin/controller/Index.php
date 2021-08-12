<?php
namespace app\naojin\controller;
use think\Controller;
use think\Db;
class Index extends Controller
{
	
    public function typelist(){
	return '<li class="list-group-item">
		<form method="get" action="/naojin/search" class="bs-example bs-example-form" role="form">
			<div class="row">
				<div class="col-lg-6">
					<div class="input-group">
						<input type="text" name="keyword" id="keyword" class="form-control">
						<span class="input-group-btn">
							<button class="btn btn-default" type="submit">
								确定
							</button>
						</span>
					</div>
				</div>
			</div>
		</form>
	</li>
	';
    }
    public function otherhtml(){
		return '
	<script>
	;!function(){
		$("a#answer").click(function (){
			$(this).next().slideToggle()
		})
	}();
	</script>
	';
    }
    public function index(){
		$data=db::table('nao_jin')->paginate(15);
		$list='';
		foreach($data as $row){
			$list.='
				<li class="list-group-item">
					<h4>'.$row['id'].'.'.$row['title'].'</h4>
					<p>
						<a id="answer" class="btn btn-default">答案</a>
							<span style="display:none">
								<font color="red">'.$row['content'].'</font>
							</span>
					</p>
				</li>';
		}
		$view=['title'=>'脑筋急转弯','content'=>$list,'type'=>$this->typelist(),'other'=>$this->otherhtml()];
		$this->assign('view', $view);
		$this->assign('pages', $data);
		return $this->fetch('xhy');
    }
    public function search()
    {
		$keyword=input('keyword')??'的';
		$data=db::table('nao_jin')->where('title','like','%'.$keyword.'%')->paginate(15,false,['query'=>['keyword'=>$keyword]]);
		$list='';
		foreach($data as $row){
			$list.='
				<li class="list-group-item">
					<h4>'.$row['id'].'.'.$row['title'].'</h4>
					<p>
						<a id="answer" class="btn btn-default">答案</a>
							<span style="display:none">
								<font color="red">'.$row['content'].'</font>
							</span>
					</p>
				</li>';
		}
		$view=['title'=>$keyword.' 脑筋急转弯搜索结果','content'=>$list,'type'=>$this->typelist(),'other'=>$this->otherhtml()];
		$this->assign('view', $view);
		$this->assign('pages', $data);
		return $this->fetch('xhy');
    }
}