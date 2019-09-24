<?php

namespace haotong\cdn;

use Qiniu\Auth;
use Qiniu\Config;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
use Exception;

/**
 * 七牛云CDN
 *
 * Class QiniuTarget
 * @package haotong\cdn
 */
class Qiniu extends CdnAbstract implements CdnInterface
{
    public $accessKey;

    public $secretKey;

    public $bucket;

    /** @var  BucketManager */
    protected $client;

    protected $lastError = null;

    /**
     * 初始化
     *
     * @throws Exception
     */
    public function init()
    {
        parent::init();
        if( empty($this->accessKey) ) throw new Exception("Qiniu accessKey cannot be blank");

        if( empty($this->secretKey) ) throw new Exception("Qiniu secretKey cannot be blank");

        $this->client = $this->getBucketManager();
    }

    /**
     * 上传文件
     *
     * @param string $localFile
     * @param string $destFile
     * @return bool
     */
    public function upload($localFile, $destFile)
    {
        $token = $this->getAuth()->uploadToken($this->bucket);
        $uploadMgr = new UploadManager();
        list($ret, $err) = $uploadMgr->putFile($token, $destFile, $localFile);
        if ($err !== null) {
            $this->lastError = $err;
            return false;
        } else {
            return true;
        }
    }

    /**
     * 分片上传
     *
     * @param string $localFile
     * @param string $destFile
     * @return bool|void
     */
    public function multiUpload($localFile, $destFile)
    {
        $this->upload($localFile, $destFile);
    }

    /**
     * 删除
     *
     * @param string $destFile
     * @return bool
     */
    public function delete($destFile)
    {
        $err = $this->client->delete($this->bucket, $destFile);
        if ($err) {
            $this->lastError = $err;
            return false;
        }
        return true;
    }

    /**
     * 检查是否存在
     *
     * @param string $destFile
     * @return bool
     */
    public function exists($destFile)
    {
        list($fileInfo, $err) = $this->client->stat($this->bucket, $destFile);
        if ($err) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 获取认证
     *
     * @return Auth
     */
    public function getAuth()
    {
        return new Auth($this->accessKey, $this->secretKey);
    }

    /**
     * 空间资源管理及批量操作类
     *
     * @return BucketManager
     */
    private function getBucketManager()
    {
        return new BucketManager($this->getAuth(), new Config());
    }

}