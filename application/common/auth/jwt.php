<?php


namespace app\common\auth;


use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\ValidationData;

class jwt
{
    private $token;
    private $decodeToken;
    private static $instance;

    /**
     * @return jwtInstance
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * claim Issuer
     * @var string
     */
    private $issuer = '1509ers.com';

    /**
     * 观众，相当于是客户端
     * 网站为web，安卓为Android，苹果为iOS
     * @var string
     */
    private $audience = 'api.1509ers.com';

    /**
     * user id
     * 用户id
     * @var
     */
    private $uid;

    /**
     * secrect
     * @var string
     */
    private $secrect = '$$$this_is_naip%%%';



    /**
     * 私有化构造函数
     * JWT constructor.
     */
    private function __construct(){}

    /**
     * 私有化拷贝函数
     */
    private function __clone(){}

    /**
     * 编码token
     * @time 签发时间
     * @last_time 有效时间
     *
     */
    public function encode(){
        $time = time();
        $last_time = 604800;
        $this->token = (new Builder())->setHeader('alg','HS256')
            ->setIssuer($this->issuer)
            ->setAudience($this->audience)
            ->setIssuedAt($time)
            ->setExpiration($time+$last_time)
            ->set('uid',$this->uid)
            ->sign(new Sha256(),$this->secrect)
            ->getToken();
        return $this;
    }

    /**
     * 转化为字符串
     */
    public function getTOken(){
        return (string)$this->token;
    }

    /**
     * @param $token 传入的token，用来进行token鉴权
     */
    public function setToken($token){
        $this->token = $token;
        return $this;
    }

    /**
     * @param $uid
     * 设置传入的uid
     * @return $this
     */
    public function setUid($uid){
        $this->uid = $uid;
        return $this;
    }

    /**
     * 获取uid
     * @return null
     */
    public function getUid(){
        if(isset($this->uid)){
            return $this->uid;
        }else{
            return null;
        }
    }

    /**
     * @return mixed
     */
    public function decode(){
        if( !isset($this->decodeToken) ){
            $this->decodeToken = (new Parser())->parse((string)$this->token);
            $this->uid = $this->decodeToken->getClaim('uid');
        }
        return $this->decodeToken;
    }

    /**
     * @return boolean
     */
    public function verify(){
        $result = $this->decode()->verify(new Sha256(),$this->secrect);
        return $result;
    }

    /**
     * @return boolean
     *
     */
    public function validate(){
        $now = time();
        $data = new ValidationData();
        $data->setIssuer($this->issuer);
        $data->setAudience($this->audience);
        $data->setCurrentTime($now);
        return $this->decode()->validate($data);
    }


}