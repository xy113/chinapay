<?php
/**
 * ============================================================================
 * Copyright (c) 2015-2020 贵州大师兄信息技术有限公司 All rights reserved.
 * siteַ: https://www.gzdsx.cn
 * ============================================================================
 * @author:     David Song<songdewei@163.com>
 * @version:    v1.0.0
 * ---------------------------------------------
 * Date: 2020/4/13
 * Time: 7:02 下午
 */

namespace ChinaPay\Traits;


use ChinaPay\Exception\ChinaPayException;
use ChinaPay\SecssUtil;
use GuzzleHttp\Client;

trait HasApi
{
    protected $api;
    protected $version = '20150922';
    protected $config = [
        'mer_id' => '',//商户号
        'security_ini' => 'path to security.ini'
    ];
    protected $content = [];

    /**
     * @return mixed
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * @param $api
     * @return $this
     */
    public function setApi($api)
    {
        $this->api = $api;
        return $this;
    }

    /**
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @param array $content
     * @return string
     * @throws ChinaPayException
     */
    protected function sendRequest(array $content)
    {
        $this->content = $content;
        $this->validateContent();
        $this->signContent();
        $clinet = new Client();
        $res = $clinet->post($this->api, [
            'form_params' => $this->content
        ]);
        if ($res->getStatusCode() == 200) {
            return $res->getBody()->getContents();
        } else {
            throw new ChinaPayException($res->getReasonPhrase(), $res->getStatusCode());
        }
    }

    /**
     * @throws ChinaPayException
     */
    protected function signContent()
    {
        foreach ($this->content as $k => $v) {
            if ($v == '' || $v == null) unset($this->content[$k]);
        }
        $util = new SecssUtil();
        $util->init($this->config['security_ini']);
        if ($util->sign($this->content)) {
            $this->content['Signature'] = $util->getSign();
        } else {
            throw new ChinaPayException($util->getErrMsg(), $util->getErrCode());
        }
    }

    /**
     * @throws ChinaPayException
     */
    protected function validateContent()
    {

    }
}
