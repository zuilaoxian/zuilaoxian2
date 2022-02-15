<?php
namespace app\index\controller;
use app\Common\controller\Base;
class QrDecode extends Base
{
    public function index()
    {
		return $this->fetch('index/QrDecode');
    }
}