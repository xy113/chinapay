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
 * Time: 2:59 下午
 */

namespace ChinaPay;

/**
 * Class Factory
 * @package ChinaPay
 *
 * @method static \ChinaPay\Payment\Application payment(array $config) 支付
 * @method static \ChinaPay\Query\Application query(array $config) 查询
 * @method static \ChinaPay\ElementQuery\Application elementQuery(array $config) 要素查询
 * @method static \ChinaPay\Signing\Application signing(array $config) 签约
 * @method static \ChinaPay\SignQuery\Application signQuery(array $config) 签约查询
 * @method static \ChinaPay\Rescission\Application rescission(array $config) 解约
 */
class Factory
{
    private static $appMap = [
        'payment' => \ChinaPay\Payment\Application::class,
        'query' => \ChinaPay\Query\Application::class,
        'elementQuery' => \ChinaPay\ElementQuery\Application::class,
        'signing'=>\ChinaPay\Signing\Application::class,
        'signQuery'=>\ChinaPay\SignQuery\Application::class,
        'rescission'=>\ChinaPay\Rescission\Application::class,
    ];

    /**
     * @param $name
     * @param array $config
     * @return mixed
     */
    public static function make($name, array $config = [])
    {
        $application = self::$appMap[$name];

        return new $application($config);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return self::make($name, ...$arguments);
    }
}
