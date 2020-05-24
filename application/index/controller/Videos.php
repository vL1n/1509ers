<?php


namespace app\index\controller;


use app\api\controller\v1\Pythondata;
use app\common\handleSign\handleSign;
use app\common\jsonResponse\JsonResponse;
use think\Request;

class Videos extends Base
{
    use JsonResponse;
    use handleSign;

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
        $video = new Pythondata();
        $request_data =[
            'id'=>$id
        ];
        $data = $this->handleData($request_data);
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
        $video = new Pythondata();
        $request_data =[
            'id'=>$id
        ];
        $data = $this->handleData($request_data);
        $video_detail = action('api/v1.Pythondata/getVideoDetailById',$data);
        $this->assign('video_detail',$video_detail);
        $this->assign('url',$url);
        $this->assign('name',$name);
        return $this->fetch();
    }

    public function getVideoList(Request $request){
        $info = $request->param();
        $data = $this->handleData($info);
        $videoList = action('api/v1.Pythondata/getVideoListByName',$data);
        return $videoList;
    }

}