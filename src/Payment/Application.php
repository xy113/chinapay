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
 * Time: 3:30 下午
 */

namespace ChinaPay\Payment;


use ChinaPay\Exception\ChinaPayException;
use ChinaPay\SecssUtil;
use GuzzleHttp\Client;

class Application
{
    protected $version = '20150922';
    //前台支付api 生产环境
    protected $payApi = 'https://payment.chinapay.com/CTITS/service/rest/page/nref/000000000017/0/0/0/0/0';
    //前台支付api 测试环境
    protected $testPayApi = 'https://newpayment-test.chinapay.com/CTITS/service/rest/page/nref/000000000017/0/0/0/0/0';
    //后台支付api 生产环境
    protected $bgPayApi = 'https://payment.chinapay.com/CTITS/service/rest/forward/syn/000000000017/0/0/0/0/0';
    //后台支付api 测试环境
    protected $bgTestPayApi = 'https://newpayment-test.chinapay.com/CTITS/service/rest/forward/syn/000000000017/0/0/0/0/0';

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
    public function pay()
    {
        $this->api = $this->payApi;
        return $this;
    }

    /**
     * @return $this
     */
    public function testPay()
    {
        $this->api = $this->testPayApi;
        return $this;
    }

    /**
     * @return $this
     */
    public function bgPay()
    {
        $this->api = $this->bgPayApi;
        return $this;
    }

    /**
     * @return $this
     */
    public function bgTestPay()
    {
        $this->api = $this->bgTestPayApi;
        return $this;
    }

    /**
     * @param array $content
     * @return bool|string
     * @throws ChinaPayException
     */
    public function requestPay(array $content)
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

        if (!isset($content['MerOrderNo']) || !$content['MerOrderNo']) {
            throw new ChinaPayException('missing MerOrderNo value', 400);
        }

        if (!isset($content['TranDate']) || !$content['TranDate']) {
            $content['TranDate'] = date('Ymd');
        }

        if (!isset($content['TranTime']) || !$content['TranTime']) {
            $content['TranTime'] = date('His');
        }

        if (!isset($content['OrderAmt'])) {
            throw new ChinaPayException('missing OrderAmt value', 400);
        }

        if (!$content['OrderAmt']) {
            throw new ChinaPayException('missing OrderAmt value', 400);
        }

        if (!isset($content['BusiType']) || !$content['BusiType']) {
            $content['BusiType'] = '0001';
        }

        if (!isset($content['MerPageUrl']) || !$content['MerPageUrl']) {
            if ($this->config['mer_page_url']) {
                $content['MerPageUrl'] = $this->config['mer_page_url'];
            }
        }

        if (!isset($content['MerBgUrl']) || !$content['MerBgUrl']) {
            if ($this->config['mer_bg_url']) {
                $content['MerBgUrl'] = $this->config['mer_bg_url'];
            } else {
                throw new ChinaPayException('missing MerBgUrl value', 400);
            }
        }

        if (!isset($content['RemoteAddr']) || !$content['RemoteAddr']) {
            $content['RemoteAddr'] = $_SERVER['REMOTE_ADDR'];
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
