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
        return $this->jsonSuccess($res);
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
        return $this->jsonSuccess($res);
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
        return $this->jsonSuccess($res);
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

        return $this->jsonSuccess($res);
    }

    /**
     * 根据所给索引查询信息是否存在
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

    /**
     * 通过传入的字段直接查询
     * @param $field
     * @param $value
     * @return false|string
     */
    public function getUserInfoByField($field,$value){
        try {
            $res = $this->where($field,$value)->findOrEmpty()->toArray();
            if(empty($res)){
                return $this->jsonData(apiErrCode::USR_NOEXIST[0],apiErrCode::USR_NOEXIST[1]);
            }
        }
        catch (Exception $e){
            return $this->jsonData(apiErrCode::ERR_UNKNOWN[0],$e->getMessage());
        }
        return $this->jsonSuccess($res);
    }

    /**
     * 获取所有用户信息
     * @return false|string
     */
    public function getAllUser(){
        try {
            $res = $this->where('id','<>','0')->findOrEmpty()->toArray();
            if (empty($res)){
                return $this->jsonApiError(apiErrCode::NO_USER);
            }
        }
        catch (Exception $e){
            return $this->jsonData(apiErrCode::ERR_UNKNOWN[0],$e->getMessage());
        }
        return $this->jsonSuccess($res);
    }

    public function getUsers($limit, $where)
    {

        try {

            $res = $this->where($where)->order('id', 'esc')->paginate($limit);

        }catch (\Exception $e) {

            return modelReMsg(-1, '', $e->getMessage());
        }

        return modelReMsg(0, $res, 'ok');
    }

    public function addUser($user){
        try {

            $has = $this->where('username', $user['username'])->findOrEmpty()->toArray();
            if(!empty($has)) {
                return modelReMsg(-2, '', '管理员名已经存在');
            }

            $this->insert($user);
        }catch (\Exception $e) {

            return modelReMsg(-1, '', $e->getMessage());
        }

        return modelReMsg(0, '', '添加管理员成功');
    }

    public function editUser($user)
    {
        try {

            $has = $this->where('username', $user['username'])->where('id', '<>', $user['id'])
                ->findOrEmpty()->toArray();
            if(!empty($has)) {
                return modelReMsg(-2, '', '用户已经存在');
            }

            $this->save($user, ['id' => $user['id']]);
        }catch (\Exception $e) {

            return modelReMsg(-1, '', $e->getMessage());
        }

        return modelReMsg(0, '', '编辑用户成功');
    }

    public function delUser($userId)
    {
        try {
            $ext = $this->where('id',$userId)->find();
            if($ext['is_admin'] == 1){
                return modelReMsg(-2, '', '管理员用户不可删除');
            }


            $this->where('id', $userId)->delete();
        } catch (\Exception $e) {

            return modelReMsg(-1, '', $e->getMessage());
        }

        return modelReMsg(0, '', '删除成功');
    }

    public function getUserById($userId)
    {
        try {

            $info = $this->where('id', $userId)->findOrEmpty()->toArray();
        }catch (\Exception $e) {

            return modelReMsg(-1, '', $e->getMessage());
        }

        return modelReMsg(0, $info, 'ok');
    }

}
