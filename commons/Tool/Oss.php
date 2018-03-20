<?php
namespace Commons\Tool;

use OSS\OssClient;
use OSS\Core\OssException;

/**
 * @desc 阿里云oss 方法封装
 */
class Oss
{
    private $config;
	private $client;

    public function __construct($configs)
	{
		foreach ($configs as $config)
		{
			if(!isset($config['accessKeyId']) || empty($config['accessKeyId']))
			{
				throw new \Exception('accessKeyId 不能为空');
			}
			if(!isset($config['accessKeySecret']) || empty($config['accessKeySecret']))
			{
				throw new \Exception('accessKeySecret 不能为空');
			}
			if(!isset($config['endpoint']) || empty($config['endpoint']))
			{
				throw new \Exception('endpoint 不能为空');
			}
			if(!isset($config['bucket']) || empty($config['bucket']))
			{
				throw new \Exception('bucket 不能为空');
			}
			$this->config[$config['bucket']] = $config;
		}
	}

    private function init($bucket)
	{
		if(!isset($this->client[$bucket]))
		{
			try {
				$config = $this->config[$bucket];
				$this->client[$bucket] = new OssClient($config['accessKeyId'], $config['accessKeySecret'], $config['endpoint'], false);
			} catch (OssException $e) {
				printf(__FUNCTION__ . "creating OssClient instance: FAILED\n");
				printf($e->getMessage() . "\n");
				exit;
			}
		}
	}

    /**
	 * 获取临时访问地址
	 *
	 * @param  string $bucket
	 * @param  string $object
	 * @param  int $time
	 * @return string
	 * @author hutong
	 * @date   2017-04-28T17:00:29+080
	 */
	public function signUrl($bucket, $object, $time = 3600)
	{
		if(empty($object) || empty($object))
		{
			return false;
		}
		$this->init($bucket);
		try{
	        return $this->client[$bucket]->signUrl($bucket, trim($object,'/'), $time);
	    } catch(OssException $e) {
	        return;
	    }
		return;
	}

    /**
	 * 上传文件
	 *
	 * @param  string $bucket
	 * @param  string $object
	 * @param  string $filePath
	 * @return booler
	 * @author hutong
	 * @date   2017-04-28T13:57:15+080
	 */
	public function uploadObject($bucket, $object, $filePath)
	{
		if(empty($object) || empty($object) || empty($filePath))
		{
			return false;
		}
		$this->init($bucket);
	    try{
	        $this->client[$bucket]->uploadFile($bucket, $filePath, $object);
	        return true;
	    } catch(OssException $e) {
	        return false;
	    }
	    return false;
	}

	/**
	 * 添加文件
	 *
	 * @param  string $bucket
	 * @param  string $object
	 * @param  string $content
	 * @return booler
	 * @author hutong
	 * @date   2017-04-28T13:56:31+080
	 */
	public function putObject($bucket, $object, $content)
	{
		if(empty($bucket) || empty($object))
		{
			return false;
		}
		$this->init($bucket);
	    try{
	        $this->client[$bucket]->putObject($bucket, $object, $content);
	        return true;
	    } catch(OssException $e) {
	        return false;
	    }
	    return false;
	}

	/**
	 * 获取文件
	 *
	 * @param  string $bucket
	 * @param  string $object
	 * @return string
	 * @author hutong
	 * @date   2017-04-28T13:56:01+080
	 */
	public function getObject($bucket, $object)
	{
		if(empty($bucket) || empty($object))
		{
			return;
		}
		$this->init($bucket);
		try{
	        $exist = $this->client[$bucket]->doesObjectExist($bucket, $object);
	        if($exist)
			{
	        	return $this->client[$bucket]->getObject($bucket, $object);
	        }
	    } catch(OssException $e) {
	        return;
	    }
		return;
	}

	/**
	 * 删除文件
	 *
	 * @param  string $bucket
	 * @param  string $object
	 * @return booler
	 * @author hutong
	 * @date   2017-04-28T13:55:21+080
	 */
	public function delObject($bucket, $object)
	{
		if(empty($bucket))
		{
			return false;
		}
		if(empty($object))
		{
			return true;
		}
		$this->init($bucket);
		try{
			$exist = $this->client[$bucket]->doesObjectExist($bucket, $object);
	        if($exist)
			{
	        	$this->client[$bucket]->deleteObject($bucket, $object);
	        }
	        return true;
	    } catch(OssException $e) {
	        return false;
	    }
	    return false;
	}
}
