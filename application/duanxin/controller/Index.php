<?php
namespace app\duanxin\controller;
use think\Controller;
use think\Db;
class Index extends Controller
{
    public function typelist(){
	return '<li class="list-group-item">
		<form method="get" action="/duanxin/search" class="bs-example bs-example-form" role="form">
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
		<div class="panel-group" id="accordion">
			<div class="panel panel-default">
				<div class="panel-heading">
					<a data-toggle="collapse" data-parent="#accordion" 
						href="/#collapseOne">
						【祝福无限】
					</a>
					<a data-toggle="collapse" data-parent="#accordion" 
						href="/#collapseTwo">
						【节日语录】
					</a>
					<a data-toggle="collapse" data-parent="#accordion" 
						href="/#collapseThree">
						【经典语录】
					</a>
					<a data-toggle="collapse" data-parent="#accordion" 
						href="/#collapseFour">
						【爱情密语】
					</a>
				</div>
				<div id="collapseOne" class="panel-collapse collapse collapse" in>
			<ul class="breadcrumb list-group-item">
			<li><a href="/duanxin/36">问候</a></li>
			<li><a href="/duanxin/37">思念</a></li>
			<li><a href="/duanxin/38">感谢</a></li>
			<li><a href="/duanxin/39">祝福</a></li>
			<li><a href="/duanxin/10">生日</a></li>
			<li><a href="/duanxin/11">纪念</a></li>
			</ul>
				</div>
				<div id="collapseTwo" class="panel-collapse collapse">
			<ul class="breadcrumb list-group-item">
			<li><a href="/duanxin/12">元旦</a></li>
			<li><a href="/duanxin/13">春节</a></li>
			<li><a href="/duanxin/14">元宵节</a></li>
			<li><a href="/duanxin/15">情人节</a></li>
			<li><a href="/duanxin/16">妇女节</a></li>
			<li><a href="/duanxin/17">愚人节</a></li>
			<li><a href="/duanxin/18">植树节</a></li>
			<li><a href="/duanxin/19">清明节</a></li>
			<li><a href="/duanxin/20">劳动节</a></li>
			<li><a href="/duanxin/21">青年节</a></li>
			<li><a href="/duanxin/22">母亲节</a></li>
			<li><a href="/duanxin/23">儿童节</a></li>
			<li><a href="/duanxin/24">父亲节</a></li>
			<li><a href="/duanxin/25">端午节</a></li>
			<li><a href="/duanxin/26">建党节</a></li>
			<li><a href="/duanxin/27">建军节</a></li>
			<li><a href="/duanxin/28">七夕节</a></li>
			<li><a href="/duanxin/29">教师节</a></li>
			<li><a href="/duanxin/30">中秋节</a></li>
			<li><a href="/duanxin/31">国庆节</a></li>
			<li><a href="/duanxin/32">重阳节</a></li>
			<li><a href="/duanxin/33">万圣节</a></li>
			<li><a href="/duanxin/34">感恩节</a></li>
			<li><a href="/duanxin/35">圣诞节</a></li>
			</ul>
				</div>
				<div id="collapseThree" class="panel-collapse collapse">
			<ul class="breadcrumb list-group-item">
			<li><a href="/duanxin/40">道歉</a></li>
			<li><a href="/duanxin/41">励志格言</a></li>
			<li><a href="/duanxin/48">经典台词</a></li>
			<li><a href="/duanxin/9">调侃能手</a></li>
			<li><a href="/duanxin/49">大话星仔</a></li>
			<li><a href="/duanxin/3">短信幽默</a></li>
			<li><a href="/duanxin/7">整人专家</a></li>
			<li><a href="/duanxin/8">谷话俚语</a></li>
			</ul>
				</div>
				<div id="collapseFour" class="panel-collapse collapse">
			<ul class="breadcrumb list-group-item">
			<li><a href="/duanxin/42">求爱</a></li>
			<li><a href="/duanxin/43">热恋</a></li>
			<li><a href="/duanxin/44">网恋</a></li>
			<li><a href="/duanxin/45">求婚</a></li>
			<li><a href="/duanxin/46">分手</a></li>
			<li><a href="/duanxin/47">爱情密码</a></li>
			</ul>
				</div>
			</div>
		</div>
		点击文字即可复制到剪切板
	</li>';
    }
    public function index($id=''){
		if ($id){
			$data=db::table('duanxin_content')->where('type',$id)->paginate(15);
		}else{
			$data=db::table('duanxin_content')->paginate(15);
		}
		$list='';
		foreach($data as $row){
			$list.='
				<li class="list-group-item">
					<h4 data-clipboard-text="'.$row['content'].'">'.$row['id'].'.'.$row['content'].'</h4>
				</li>';
		}
		$view=['title'=>'短信','content'=>$list,'type'=>$this->typelist(),'other'=>''];
		$this->assign('view', $view);
		$this->assign('pages', $data);
		return $this->fetch();
    }
    public function search()
    {
		$keyword=input('keyword')??'的';
		$data=db::table('duanxin_content')->where('content','like','%'.$keyword.'%')->paginate(15,false,['query'=>['keyword'=>$keyword]]);
		$list='';
		foreach($data as $row){
			$list.='
				<li class="list-group-item">
					<h4 data-clipboard-text="'.$row['content'].'">'.$row['id'].'.'.$row['content'].'</h4>
				</li>';
		}
		$view=['title'=>$keyword.' 短信频道搜索结果','content'=>$list,'type'=>$this->typelist(),'other'=>''];
		$this->assign('view', $view);
		$this->assign('pages', $data);
		return $this->fetch('index');
    }
}