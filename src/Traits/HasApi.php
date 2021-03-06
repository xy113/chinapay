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

trait HasApi
{
    protected $api;
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
     * @return array
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param array $content
     * @return HasApi
     */
    public function setContent(array $content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * 用于后台支付
     * @param array $content
     * @return string
     * @throws ChinaPayException
     */
    public function sendRequest($content = [])
    {
        if (!empty($content)) {
            $this->content = $content;
        }

        $this->validateCommonFields();
        $this->validateContent();
        if (!isset($this->content['Signature'])) {
            throw new ChinaPayException('missing Signature value', 400);
        }

        return $this->curlPost($this->api, $this->content);

//        $clinet = new Client();
//        $res = $clinet->post($this->api, [
//            'form_params' => $this->content
//        ]);
//        if ($res->getStatusCode() == 200) {
//            return $res->getBody()->getContents();
//        } else {
//            throw new ChinaPayException($res->getReasonPhrase(), $res->getStatusCode());
//        }
    }

    /**
     * 用户前台支付
     * @param array $content
     * @return string
     * @throws ChinaPayException
     */
    public function buildRequestForm($content = [])
    {
        if (!empty($content)) {
            $this->content = $content;
        }

        $this->validateCommonFields();;
        $this->validateContent();
        if (!isset($this->content['Signature'])) {
            throw new ChinaPayException('missing Signature value', 400);
        }

        $fields = '';
        foreach ($this->content as $k => $v) {
            $fields .= '<input type="hidden" name="' . $k . '" value="' . $v . '">';
        }
        return '<html><head><title>ChinaPay Form</title></head><body>' .
            '<form method="post" id="form" action="' . $this->api . '">' . $fields . '</form>' .
            '<script>document.getElementById("form").submit();</script></body></html>';
    }

    protected function validateContent()
    {

    }

    /**
     * @throws ChinaPayException
     */
    protected function validateCommonFields()
    {
        if (!isset($this->content['Version'])) {
            throw new ChinaPayException('missing Version value', 400);
        }

        if (!isset($this->content['MerId'])) {
            throw new ChinaPayException('missing MerId value', 400);
        }

        if (!isset($this->content['BusiType'])) {
            throw new ChinaPayException('missing BusiType value', 400);
        }

        if (!isset($this->content['TranType'])) {
            throw new ChinaPayException('missing TranType value', 400);
        }
    }

    /**
     * @param array $data
     * @param int $ssl
     * @param int $timeout
     * @return mixed
     */
    protected function curlPost($url, $data = [], $ssl = true, $timeout = 500)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $ssl);
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }
}
