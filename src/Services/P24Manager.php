<?php
namespace NetborgTeam\P24\Services;



/**
 * Description of P24Manager
 *
 * @author netborg
 */
class P24Manager {
    
    const ENDPOINT_LIVE = "https://secure.przelewy24.pl/";
    const ENDPOINT_SANDBOX = "https://sandbox.przelewy24.pl/";
    const API_VERSION = "3.2";
    
    
    
    private $merchantId;
    private $posId;
    private $crc;
    private $endpoint;
    
    
    
    public function __construct() {
        $this->merchantId = config('p24.merchant_id');
        $this->posId = config('p24.pos_id');
        $this->crc = config('p24.crc');
        
        if (config('p24.mode') === 'live') {
            $this->endpoint = self::ENDPOINT_LIVE;
        } else {
            $this->endpoint = self::ENDPOINT_SANDBOX;
        }
    }
    
    
}
