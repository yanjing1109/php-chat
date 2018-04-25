<?php
namespace App\Traits;
trait IpTrait
{
    /**
     * 检查 IP 白名单
     *
     * @param string $ips 
     * 
     * @return void
     */
    public function checkIpBlack($ips)
    {
        $ips = explode(',', $ips);
        for ($i = 0, $length = count($ips); $i < $length; $i++)
        {
            $ip = trim($ips[$i]);
            $this->allowIps[$ip] = true;
        }

        $ip = '';
        $ips = getIP();
        if ($ips)
        {
            $ip = explode(',', $ips)[0];
        }
        
        if (!isset($this->allowIps['*']) && !isset($this->allowIps[$ip]))
        {
            exit($ip . ' 403');
        }
    }
}
