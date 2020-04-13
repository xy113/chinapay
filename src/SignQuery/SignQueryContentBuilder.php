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
 * Time: 6:11 下午
 */

namespace ChinaPay\SignQuery;


use ChinaPay\Traits\HasContent;

class SignQueryContentBuilder
{
    use HasContent;

    protected $content = [
        'Version' => '20150922',
        'AccessType' => 0,
        'InstuId' => '',
        'AcqCode' => '',
        'MerId' => '',
        'BusiType' => '0001',
        'TranType' => '0504',
        'OriTranType' => '9904',
        'CardTranData' => '',
        'TranReserved' => '',
        'TimeStamp' => '',
        'RemoteAddr' => ''
    ];

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
    public function setOriTranType($value)
    {
        $this->content['OriTranType'] = $value;
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setCardTranData($value)
    {
        $this->content['CardTranData'] = $value;
        return $this;
    }
}
