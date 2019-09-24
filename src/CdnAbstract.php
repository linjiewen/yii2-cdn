<?php

namespace haotong\cdn;

use Exception;
use yii\base\Component;

/**
 * cdn抽象类
 *
 * Class TargetAbstract
 * @package haotong\cdn
 */
abstract class CdnAbstract extends Component implements CdnInterface
{
    /**
     * @var string $host cdn域名
     */
    public $host;

    /**
     * @var string [$lastError = null] 错误信息
     */
    protected $lastError = null;

    /**
     * @var object $client cdn-client
     */
    protected $client;

    /**
     * 初始化
     *
     * @throws Exception
     */
    public function init()
    {
        parent::init();
        if( empty($this->bucket) ) throw new Exception("Cdn bucket cannot be blank");

        if( empty($this->host) ) throw new Exception("Cdn host cannot be blank");

        if (stripos($this->host, 'http://') !== 0 && stripos($this->host, 'https://') !== 0  && stripos($this->host, '//') !== 0) {
            throw new Exception("host must begin with http://, https:// or //");
        }

        if( $this->host[strlen($this->host) - 1] !== '/' ){
            $this->host .= '/';
        }
    }

    /**
     * 错误处理
     *
     * @return mixed|string
     */
    public function getLastError()
    {
        return is_string( $this->lastError ) ? $this->lastError : print_r($this->lastError, true);
    }

    /**
     * 获取完整的文件地址
     *
     * @param string $destFile 文件地址
     * @return string
     */
    public function getCdnUrl($destFile)
    {
        if( strpos($destFile, '/') === 0 ){
            $destFile = substr($destFile, 1);
        }
        return $this->host . $destFile;
    }

    /**
     * 返回cdn-client
     *
     * @return mixed|object
     */
    public function getClient()
    {
        return $this->client;
    }
}