<?php


namespace app\index\controller;


use app\api\controller\v1\Pythondata;
use app\common\jsonResponse\JsonResponse;
use think\Request;

class Videos extends Base
{
    use JsonResponse;

    public function index(){
        return $this->fetch();
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function videoDetail(Request $request){
        $info = $request->param();
        $id =$info['id'];
        $data =[
            'id'=>$id
        ];
        $video_detail = action('api/v1.Pythondata/getVideoDetailById',$data);
        $this->assign('video_detail',$video_detail);
        return $this->fetch();
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function play(Request $request){
        $info = $request->param();
        $id =$info['id'];
        $url = $info['url'];
        $name = $info['name'];
        $data =[
            'id'=>$id
        ];
        $video_detail = action('api/v1.Pythondata/getVideoDetailById',$data);
        $this->assign('video_detail',$video_detail);
        $this->assign('url',$url);
        $this->assign('name',$name);
        $this->assign('movieId',$id);
        return $this->fetch();
    }

}