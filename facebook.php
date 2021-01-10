<?php
	function curl($params = array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $params['url']);
        if (isset($params['cookie'])) {
            curl_setopt($ch, CURLOPT_COOKIE, $params['cookie']);
        }
        curl_setopt($ch, CURLOPT_ENCODING, '');
        if (!isset($params['browser'])) {
            $params['browser'] = "Mozilla/5.0 (Windows NT 6.1; rv:11.0) Gecko/20100101 Firefox/11.0";
        }
        curl_setopt($ch, CURLOPT_USERAGENT, $params['browser']);
        if (isset($params['headers'])) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $params['headers']);
        }
        if (isset($params['method'])) {
            if ($params['method'] == 'POST') {
                curl_setopt($ch, CURLOPT_POST, 1);
            } else {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $params['method']);
            }
        }
        if (isset($params['data'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params['data']);
        }
        if (isset($params['getheader'])) {
            curl_setopt($ch, CURLOPT_HEADER, 1);
        } else {
            curl_setopt($ch, CURLOPT_HEADER, 0);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $body = curl_exec($ch);
        if (isset($params['getinfor'])) {
            $infor = curl_getinfo($ch);
        }
        curl_close($ch);
        if (isset($params['getinfor'])) {
            return array(
                'body' => $body,
                'infor' => $infor,
            );
        }
        return $body;
    }

	$params = [
		'url' => "https://m.facebook.com/composer/ocelot/async_loader/?publisher=feed",
		'cookie' => 'xs=27:X05WHpp0OmuCLg:2:1606845717:-1:-1; c_user=100058509108298; datr=BoXGX5AdzMMAdBnO_CGGpwGi; sb=etbJX0lKNCJqXAu1l10_-78G; fr=16FLTw7IWjoaiZEB4.AWUvRWE0tlSgqmox5_prD0Wvchs.BfxoUV.tk.AAA.0.0.BfxoUV.AWWCwOj2pRw; spin=r.1003078713_b.trunk_t.1607590449_s.1_v.2_;',
	];
	echo curl($params);