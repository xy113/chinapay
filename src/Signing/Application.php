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
 * Time: 5:51 下午
 */

namespace ChinaPay\Signing;


use ChinaPay\Exception\ChinaPayException;
use ChinaPay\Traits\HasApi;

class Application
{
    use HasApi;

    protected $version = '20150922';
    //生产环境api 前台
    protected $prodApi = 'https://payment.chinapay.com/CTITS/service/rest/page/nref/000000000017/0/0/0/0/0';
    //测试环境api 前台
    protected $testApi = 'https://newpayment-test.chinapay.com/CTITS/service/rest/page/nref/000000000017/0/0/0/0/0';
    //生产环境api 后台
    protected $bgApi = 'https://payment.chinapay.com/CTITS/service/rest/forward/syn/000000000017/0/0/0/0/0';
    //测试环境api 后台
    protected $bgTestApi = 'https://newpayment-test.chinapay.com/CTITS/service/rest/forward/syn/000000000017/0/0/0/0/0';

    /**
     * Application constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->api = $this->prodApi;
        if ($config) $this->config = $config;
    }

    /**
     * @return $this
     */
    public function prod()
    {
        $this->api = $this->prodApi;
        return $this;
    }

    /**
     * @return $this
     */
    public function test()
    {
        $this->api = $this->testApi;
        return $this;
    }

    /**
     * @return $this
     */
    public function bgSigning()
    {
        $this->api = $this->bgApi;
        return $this;
    }

    /**
     * @return $this
     */
    public function bgTestSigning()
    {
        $this->api = $this->bgTestApi;
        return $this;
    }

    /**
     * @param array $content
     * @return bool|string
     * @throws ChinaPayException
     */
    public function requestSign(array $content)
    {
        return $this->sendRequest($content);
    }

    /**
     * @throws ChinaPayException
     */
    protected function validateContent()
    {
        $builder = new SignContentBuilder($this->content);
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

        if (!$builder->get('BusiType')) {
            throw new ChinaPayException('missing BusiType value', 400);
        }

        if (!$builder->get('TranType')) {
            throw new ChinaPayException('missing TranType value', 400);
        }

        if (!$builder->get('MerOrderNo')) {
            throw new ChinaPayException('missing MerOrderNo value', 400);
        }

        if (!$builder->get('TranDate')) {
            $builder->set('TranDate', date('Ymd'));
        }

        if (!$builder->get('TranTime')) {
            $builder->set('TranTime', date('His'));
        }

        if (!$builder->get('MerBgUrl')) {
            $builder->set('MerBgUrl', $this->config['mer_bg_url']);
        }

        if (!$builder->get('CardTranData')) {
            throw new ChinaPayException('missing CardTranData value', 400);
        }

        $this->content = $builder->getBizContent();
    }
}
