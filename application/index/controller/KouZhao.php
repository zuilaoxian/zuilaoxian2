<?php
namespace app\index\controller;
use app\Common\controller\Base;
class KouZhao extends Base
{
    public function index()
    {
		return $this->fetch('index/KouZhao');
    }
}