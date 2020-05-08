<?php

namespace app\common\model;

use app\common\error\apiErrCode;
use app\common\jsonResponse\JsonResponse;
use think\Exception;
use think\Model;

class User extends Model
{
    use JsonResponse;

    /**
     * @param $info
     * 通过用户名查找
     * @return string
     */
    public function getUserInfoByUserName($username){
        try {
            $res = $this->where('username',$username)->findOrEmpty()->toArray();
            if(empty($res)){
                return $this->jsonData(apiErrCode::USR_NOEXIST[0],apiErrCode::USR_NOEXIST[1]);
            }
        }
        catch (Exception $e){
            return $this->jsonData(apiErrCode::ERR_UNKNOWN[0],$e->getMessage());
        }
        return $this->jsonData(apiErrCode::SUCCESS[0],apiErrCode::SUCCESS[1],$res);
    }

    /**
     * @param $id
     * 通过用户id查询
     * @return string
     */
    public function getUserInfoById($id){
        try {
            $res = $this->where('id',$id)->findOrEmpty()->toArray();
            if(empty($res)){
                return $this->jsonData(apiErrCode::USR_ID_NOEXIST[0],apiErrCode::USR_ID_NOEXIST[1]);
            }
        }
        catch (Exception $e){
            return $this->jsonData(apiErrCode::ERR_UNKNOWN[0],$e->getMessage());
        }
        return $this->jsonData(apiErrCode::SUCCESS[0],apiErrCode::SUCCESS[1],$res);
    }

    /**
     * 维护最近登陆时间
     * @param $id
     * @param $param
     */
    public function updateUserInfoById($id, $param)
    {
        try {
            $res = $this->where('id', $id)->update($param);
        } catch (\Exception $e) {
            return $this->jsonData(apiErrCode::ERR_UNKNOWN[0],$e->getMessage());
        }
        return $res;
    }

    /**
     * @param $id
     * @param $path
     * 上传用户头像
     * @return false|string
     */
    public function updateUserAvatar($id,$path){
        try {

            $res = $this->where('id',$id)->update(['avatar_path'=>$path]);

        } catch (\Exception $e) {

            return $this->jsonData(apiErrCode::ERR_UNKNOWN[0],$e->getMessage());
        }

        return $this->jsonData(apiErrCode::SUCCESS[0],apiErrCode::SUCCESS[1],$res);
    }

    /**
     * 根据所给索引查询信息
     * @param $field
     * @param $value
     * @return false|string
     */
    public function searchUserInfoByField($field,$value){
        try{
            $res = $this->where($field,$value)->findOrEmpty()->toArray();
            if(!empty($res)){
                return $this->jsonData(apiErrCode::EXIST_USER_INFO[0],apiErrCode::EXIST_USER_INFO[1]);
            }
        } catch (\Exception $e){
            return $this->jsonData(apiErrCode::ERR_UNKNOWN[0],$e->getMessage());
        }
        return $this->jsonSuccess();
    }
}
