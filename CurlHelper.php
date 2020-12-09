function fastGet($url)
{
    $curl = curl_init();                
    curl_setopt($curl, CURLOPT_URL, $url);

    curl_setopt($curl, CURLOPT_TIMEOUT, 1); 
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
    curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
    curl_setopt($curl, CURLOPT_DNS_CACHE_TIMEOUT, 10); 

    curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);

    curl_exec($curl);   

    curl_close($curl);  
}
