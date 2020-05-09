<?php


namespace app\api\controller\v1;


use app\common\jsonResponse\JsonResponse;
use think\Controller;
use think\Request;

class Video extends Controller
{
    use JsonResponse;

    /**
     * type: 1为获取影片列表,2为获取影片详细信息
     * @param Request $request
     * @return string
     */
    public function getVideoListByName(Request $request){

        $info = $request->param();
        $param = $info['videoName'];

        $data = exec('python /www/wwwroot/Tsin/1509/application/api/lib/Pythons/getListByName.py '.$param);
        $json_parsed_data = json_decode($data,true);
        return $this->jsonSuccess($json_parsed_data);
    }

    /**
     * type: 1为获取影片列表,2为获取影片详细信息
     * @param Request $request
     * @return string
     */
    public function getVideoDetailById(Request $request){

        $info = $request->param();
        $param = $info['videoId'];

        $data = exec('python /www/wwwroot/Tsin/1509/application/api/lib/Pythons/getDetailById.py '.$param);
        $json_parsed_data = json_decode($data,true);

        return $this->jsonSuccess($json_parsed_data);
    }
}