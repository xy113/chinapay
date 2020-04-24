##使用方法
```$php
        $merId = "531112004160001";
        $orderNo = "20201122593490438098";
        $propFile = __DIR__ . '/security.ini';
        $tranTime = date('His');
        $cardTranData = [
            'CertType' => '01',
            'CertNo' => '211221199608090813',
            'AccType' => '01',
            'AccName' => '张三',
            'CardNo' => '62122624020######',
            'MobileNo'=>'187########'
        ];
        $merBgUrl = url('notify');

        //签约短信
        try {
            $builder = new SmsContentBuilder();
            $builder->setSecurityPropFile($propFile);
            $builder->setMerId($merId);
            $builder->setMerOrderNo($orderNo);
            $builder->setTranDate(date('Ymd'));
            $builder->setTranTime($tranTime);
            $builder->setCardTranData($cardTranData);

            $content = $builder->getBizContent();
            dump($content);

            $res = Factory::signSms()->sendRequest($builder->getBizContent());
            parse_str($res, $arr);
            dump($arr);
        }catch (ChinaPayException $exception){
            dump($exception->getMessage());
        }

        //后台签约
        try {
            $builder = new SignContentBuilder();
            $builder->setSecurityPropFile($propFile);
            $builder->setMerId($merId);
            $builder->setMerOrderNo($orderNo);
            $builder->setMerBgUrl($merBgUrl);
            $builder->setTranDate(date('Ymd'));
            $builder->setTranTime($tranTime);
            $builder->setTranType('9004');
            $builder->setCardTranData($cardTranData);

            $content = $builder->getBizContent();
            dump($content);

            $res = Factory::signing()->testBgSigning()->sendRequest($content);
            parse_str($res, $arr);
            dump($arr);
        } catch (ChinaPayException $exception) {
            return urldecode($exception->getMessage());
        }
```
