<?php


namespace app\api\controller\v1;


use app\common\jsonResponse\JsonResponse;
use think\Controller;
use think\Request;

class Pythondata extends Controller
{
    use JsonResponse;

    private $python_lib = '/www/wwwroot/Tsin/1509/application/api/lib/Pythons/';

    /**
     * 所有的通过python爬虫的接口全部都通过这里
     * @param $pythonFileName
     * @param $param
     * @return mixed
     */
    private function getDataFromPython($pythonFileName,$param){

        $data = exec('python '.$this->python_lib.$pythonFileName.'.py '.$param);
        return json_decode($data,true);
    }

    /**
     * 获取影片列表api
     * @param Request $request
     * @return string
     */
    public function getVideoListByName(Request $request){

        $info = $request->param();
        $param = $info['videoName'];

        $pythonName = 'getListByName';
        $data = $this->getDataFromPython($pythonName,$param);
        return $this->jsonSuccess($data);
    }

    /**
     * 获取影片详细内容api
     * @param Request $request
     * @return string
     */
    public function getVideoDetailById($id){

        $pythonName = 'getDetailById';
        $data = $this->getDataFromPython($pythonName,$id);
        return $this->jsonSuccess($data);
    }

    /**
     * 搜题api
     * @param Request $request
     * @return string
     */
    public function searchQuestion(Request $request){

        $info = $request->param();
        $param = $info['keyword'];

        $pythonName = 'searchQuestion';
        $data = $this->getDataFromPython($pythonName,$param);
        return $this->jsonSuccess($data);
    }

    /**
     * 航班信息查询api
     * @param Request $request
     * @return array
     */
    public function getAirTicket(Request $request){

        $info = $request->param();
        $dep = $info['dep'];
        $arr = $info['arr'];
        $date = $info['date'];
        $output = exec('python '.$this->python_lib.'getAirTicket.py '.$dep.' '.$arr.' '.$date);
        //这里返回的是一个json数据，想要解析为数组可以用json_decode($output,1)
        $msg = '';
        $code = 0;
        $data = json_decode($output,true);
        if(isset($data['status'])){
            if($data['status'] == -1){
                $code = 0;
                $msg = $data['errorMsg'];
            }else{
                $code = 1;
            }
        }elseif(isset($data['error'])){
            if($data['error'] == -1){
                $code = 0;
                $msg = $data['errorMsg'];
            }else{
                $code = 1;
            }
        }else{
            $code = 1;
        }
        return ['code'=>$code,'info'=>$data,'msg'=>$msg];

    }
}