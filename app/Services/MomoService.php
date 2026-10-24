<?php
namespace App\Services;

class MomoService {
    private $endpoint;
    private $partnerCode;
    private $accessKey;
    private $secretKey;
    private $redirectUrl;
    private $ipnUrl;
    
    public function __construct() {
        $this->endpoint = 'https://test-payment.momo.vn/v2/gateway/api/create';
        $this->partnerCode = 'MOMO';
        $this->accessKey = 'F8BBA842ECF85';
        $this->secretKey = 'K951B6PE1waDMi640xX08PD3vg6EkVlz';
        
        // Dùng localhost cho callback - MoMo sẽ redirect về đây sau khi thanh toán
        $this->redirectUrl = 'http://localhost/dormflow/payments/momo/callback';
        $this->ipnUrl = 'http://localhost/dormflow/payments/momo/ipn';
    }
    
    public function createPayment($orderInfo, $amount, $orderId, $extraData = '') {
        $requestId = time() . rand(1000, 9999);
        
        $rawHash = "accessKey=" . $this->accessKey . 
                   "&amount=" . $amount . 
                   "&extraData=" . $extraData . 
                   "&ipnUrl=" . $this->ipnUrl . 
                   "&orderId=" . $orderId . 
                   "&orderInfo=" . $orderInfo . 
                   "&partnerCode=" . $this->partnerCode . 
                   "&redirectUrl=" . $this->redirectUrl . 
                   "&requestId=" . $requestId . 
                   "&requestType=captureWallet";
        
        $signature = hash_hmac("sha256", $rawHash, $this->secretKey);
        
        $data = [
            'partnerCode' => $this->partnerCode,
            'partnerName' => 'DormFlow KTX',
            'storeId' => 'DormFlowStore',
            'requestId' => $requestId,
            'amount' => (string)$amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $this->redirectUrl,
            'ipnUrl' => $this->ipnUrl,
            'lang' => 'vi',
            'extraData' => (string)$extraData,
            'requestType' => 'captureWallet',
            'signature' => $signature
        ];
        
        $result = $this->execPostRequest($this->endpoint, json_encode($data));
        
        if (!$result) {
            return ['error' => 'Không thể kết nối đến MoMo'];
        }
        
        return json_decode($result, true);
    }
    
    private function execPostRequest($url, $data) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $result = curl_exec($ch);
        
        if (curl_errno($ch)) {
            error_log('Curl error: ' . curl_error($ch));
        }
        
        curl_close($ch);
        return $result;
    }
}