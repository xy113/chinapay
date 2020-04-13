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
 * Time: 11:36 下午
 */

namespace ChinaPay\ElementQuery;


use ChinaPay\Exception\ChinaPayException;
use ChinaPay\Traits\HasApi;

class Application
{
    use HasApi;

    protected $version = '20150922';
    //生产环境api
    protected $prodApi = 'https://payment.chinapay.com/CTITS/service/rest/forward/syn/000000000017/0/0/0/0/0';
    //测试环境api
    protected $testApi = 'https://newpayment-test.chinapay.com/CTITS/service/rest/forward/syn/000000000017/0/0/0/0/0';

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
     * @param array $content
     * @return bool|string
     * @throws ChinaPayException
     */
    public function requestQuery(array $content)
    {
        return $this->sendRequest($content);
    }

    protected function validateContent()
    {
        $builder = new ElementQueryContentBuilder($this->content);
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

        if (!$builder->get('OriTranType')) {
            throw new ChinaPayException('missing OriTranType value', 400);
        }

        if (!$builder->get('CardTranData')) {
            throw new ChinaPayException('missing CardTranData value', 400);
        }

        $this->content = $builder->getBizContent();
    }
}
