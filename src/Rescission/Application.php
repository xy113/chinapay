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
 * Time: 6:01 下午
 */

namespace ChinaPay\Rescission;


use ChinaPay\Exception\ChinaPayException;
use ChinaPay\SecssUtil;
use ChinaPay\Traits\HasApi;
use GuzzleHttp\Client;

class Application
{
    use HasApi;

    protected $version = '20140728';
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
    public function requestRescission(array $content)
    {
        return $this->sendRequest($content);
    }

    /**
     * @throws ChinaPayException
     */
    protected function validateContent()
    {
        $builder = new RescissionContentBuilder($this->content);
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

        if (!$builder->get('MerOrderNo')) {
            throw new ChinaPayException('missing MerOrderNo value', 400);
        }

        if (!$builder->get('TranDate')) {
            throw new ChinaPayException('missing TranDate value', 400);
        }

        if (!$builder->get('BusiType')) {
            throw new ChinaPayException('missing BusiType value', 400);
        }

        if (!$builder->get('TranType')) {
            throw new ChinaPayException('missing TranType value', 400);
        }

        $this->content = $builder->getBizContent();
    }
}
