<?php


namespace app\common\sms;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use think\facade\Config;

class AliSmsUtil
{
    /**
     * @param $phone
     * @param $code
     */
    public static function sendSmsCode($phone, $code)
    {
        $config = Config::get('Alisms');
        $templateParam = json_encode(['code'=>$code]);

        try {
            AlibabaCloud::accessKeyClient($config['AccessKeyId'], $config['AccessKeySecret'])
                ->regionId($config['regionId']) // replace regionId as you need
                ->asDefaultClient();

            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->options([
                    'query' => [
                        'PhoneNumbers' => $phone,
                        'SignName' => $config['SignName'],
                        'TemplateCode' => $config['TemplateCode'],
                        'TemplateParam' => $templateParam,
                        'RegionId' => $config['regionId'],
                    ],
                ])
                ->request();
            print_r($result->toArray());
        } catch (ClientException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        }
    }
}