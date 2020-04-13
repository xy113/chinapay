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
use ChinaPay\Traits\HasApi;

class Application
{
    use HasApi;

    protected $version = '20150922';
    //前台支付api 生产环境
    protected $payApi = 'https://payment.chinapay.com/CTITS/service/rest/page/nref/000000000017/0/0/0/0/0';
    //前台支付api 测试环境
    protected $testPayApi = 'https://newpayment-test.chinapay.com/CTITS/service/rest/page/nref/000000000017/0/0/0/0/0';
    //后台支付api 生产环境
    protected $bgPayApi = 'https://payment.chinapay.com/CTITS/service/rest/forward/syn/000000000017/0/0/0/0/0';
    //后台支付api 测试环境
    protected $bgTestPayApi = 'https://newpayment-test.chinapay.com/CTITS/service/rest/forward/syn/000000000017/0/0/0/0/0';

    /**
     * Application constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->api = $this->payApi;
        if ($config) $this->config = $config;
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
        return $this->sendRequest($content);
    }

    /**
     * @throws ChinaPayException
     */
    protected function validateContent()
    {
        $builder = new PayContentBuilder($this->content);
        if (!$builder->get('Version')) {
            $builder->set('Version', $this->version);
        }

        if (!$builder->get('MerId')) {
            if ($this->config['mer_id']) {
                $builder->set('MerId', $this->config['mer_id']);
            } else {
                throw new ChinaPayException('missing MerId value', 400);
            }
        }

        if (!$builder->get('TranDate')) {
            $builder->set('TranDate', date('Ymd'));
        }

        if (!$builder->get('TranTime')) {
            $builder->set('TranTime', date('His'));
        }

        if (!$builder->get('BusiType')) {
            $builder->set('TranTime', '0001');
        }

        if (!$builder->get('MerPageUrl')) {
            $builder->set('MerPageUrl', $this->config['mer_page_url']);
        }

        if (!$builder->get('MerBgUrl')) {
            $builder->set('MerBgUrl', $this->config['mer_bg_url']);
        }

        if (!$builder->get('OrderAmt')) {
            throw new ChinaPayException('missing OrderAmt value', 400);
        }

        if (!$builder->get('RemoteAddr')) {
            $builder->set('RemoteAddr', $_SERVER['REMOTE_ADDR']);
        }

        $this->content = $builder->getBizContent();
    }
}
