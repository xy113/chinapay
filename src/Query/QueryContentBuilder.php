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
 * Time: 11:49 下午
 */

namespace ChinaPay\Query;


class QueryContentBuilder
{
    private $content = [
        'Version' => '20140728',
        'AccessType' => 0,
        'InstuId' => '',
        'AcqCode' => '',
        'MerId' => '',
        'MerOrderNo' => '',
        'TranDate' => '',
        'TranType' => '0502',
        'BusiType' => '0001',
        'TranReserved' => ''
    ];

    /**
     * PayContentBuilder constructor.
     * @param array $content
     */
    public function __construct(array $content = [])
    {
        foreach ($content as $k => $v) {
            if (key_exists($k, $this->content)) {
                if ($v == '' || $v == null) continue;
                $this->content[$k] = $v;
            }
        }
    }

    /**
     * @param $value
     * @return $this
     */
    public function setAccessType($value)
    {
        $this->content['AccessType'] = $value;
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setInstuId($value)
    {
        $this->content['InstuId'] = $value;
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setAcqCode($value)
    {
        $this->content['AcqCode'] = $value;
        return $this;
    }

    /**
     * 设置商户ID
     * @param $value
     * @return $this
     */
    public function setMerId($value)
    {
        $this->content['MerId'] = $value;
        return $this;
    }

    /**
     * 设置商户订单号
     * @param $value
     * @return $this
     */
    public function setMerOrderNo($value)
    {
        $this->content['MerOrderNo'] = $value;
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setTranDate($value)
    {
        $this->content['TranDate'] = $value;
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setTranType($value){
        $this->content['TranType'] = $value;
        return $this;
    }

    /**
     * @param $value
     */
    public function setBusiType($value){
        $this->content['BusiType'] = $value;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function set($name, $value)
    {
        if (key_exists($name, $this->content)) {
            $this->content[$name] = $value;
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getBizContent()
    {
        foreach ($this->content as $k => $v) {
            if ($v == '' || $v == null) unset($this->content[$k]);
        }
        return $this->content;
    }
}
