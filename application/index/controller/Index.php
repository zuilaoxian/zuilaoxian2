<?php
namespace app\index\controller;
use think\Controller;
class Index extends Controller
{
    public function index()
    {
		return $this->fetch('index');
    }
    public function wtp()
    {
		return $this->fetch('index/wtp');
    }
}