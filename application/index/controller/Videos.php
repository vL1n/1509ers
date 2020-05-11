<?php


namespace app\index\controller;


use app\api\controller\v1\Video;
use app\common\jsonResponse\JsonResponse;
use think\Request;

class Videos extends Base
{
    use JsonResponse;

    public function index(){
        return $this->fetch();
    }

    public function videoDetail(Request $request){
        $info = $request->param();
        $id =$info['id'];
        $video = new Video();
        $video_detail = $video->getVideoDetailById($id);
        $video_json_parsed_detail = json_decode($video_detail,true);

        $this->assign('video_detail',$video_detail);
        return $this->fetch();
    }
    public function play(Request $request){
        $info = $request->param();
        $id =$info['id'];
        $url = $info['url'];
        $video = new Video();
        $video_detail = $video->getVideoDetailById($id);
        $this->assign('video_detail',$video_detail);
        $this->assign('url',$url);
        return $this->fetch();
    }
}