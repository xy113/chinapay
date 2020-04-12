<?php
/**
 * ============================================================================
 * Copyright (c) 2015-2020 贵州大师兄信息技术有限公司 All rights reserved.
 * siteַ: https://www.gzdsx.cn
 * ============================================================================
 * @author:     David Song<songdewei@163.com>
 * @version:    v1.0.0
 * ---------------------------------------------
 * Date: 2020/4/12
 * Time: 11:54 下午
 */

namespace ChinaPay\Query;


use ChinaPay\Exception\ChinaPayException;
use ChinaPay\SecssUtil;
use GuzzleHttp\Client;

class Application
{
    protected $version = '20140728';
    //生产环境api
    protected $prodApi = 'https://payment.chinapay.com/CTITS/service/rest/forward/syn/000000000060/0/0/0/0/0';
    //测试环境api
    protected $testApi = 'https://newpayment-test.chinapay.com/CTITS/service/rest/forward/syn/000000000060/0/0/0/0/0';

    private $api;
    private $config;

    /**
     * Application constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if ($config) {
            $this->config = $config;
        } else {
            $configs = require(__DIR__ . '/../config.php');
            $this->config = $configs['default'];
        }
        $this->api = $this->prodApi;
    }

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
     * @param $config
     * @return $this
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return $this
     */
    public function prod(){
        $this->api = $this->prodApi;
        return $this;
    }

    /**
     * @return $this
     */
    public function test(){
        $this->api = $this->testApi;
        return $this;
    }

    /**
     * @param array $content
     * @return bool|string
     * @throws ChinaPayException
     */
    public function requestQuery(array $content)
    {

        $bizContent = $this->getBizContent($content);
        $clinet = new Client();
        $res = $clinet->post($this->api, [
            'form_params' => $bizContent
        ]);
        if ($res->getStatusCode() == 200) {
            return $res->getBody()->getContents();
        } else {
            throw new ChinaPayException($res->getReasonPhrase(), $res->getStatusCode());
        }
    }

    /**
     * @param array $content
     * @return array
     * @throws ChinaPayException
     */
    private function getBizContent(array $content)
    {
        if (!isset($content['Version']) || !$content['Version']) {
            $content['Version'] = $this->version;
        }

        if (!isset($content['MerId']) || !$content['MerId']) {
            if ($this->config['mer_id']) {
                $content['MerId'] = $this->config['mer_id'];
            } else {
                throw new ChinaPayException('missing MerId value', 400);
            }
        }

        if (!isset($content['MerOrderNo']) || !$content['MerOrderNo']){
            throw new ChinaPayException('missing MerOrderNo value', 400);
        }

        if (!isset($content['TranDate']) || !$content['TranDate']){
            throw new ChinaPayException('missing TranDate value', 400);
        }

        if (!isset($content['BusiType']) || !$content['BusiType']) {
            $content['BusiType'] = '0001';
        }

        if (!isset($content['TranType']) || !$content['TranType']) {
            $content['TranType'] = '0502';
        }

        foreach ($content as $k => $v) {
            if ($v == '' || $v == null) unset($content[$k]);
        }

        $util = new SecssUtil();
        $util->init(__DIR__ . '/../security.ini');
        if ($util->sign($content)) {
            $content['Signature'] = $util->getSign();
            return $content;
        } else {
            throw new ChinaPayException($util->getErrMsg(), $util->getErrCode());
        }
    }
}
