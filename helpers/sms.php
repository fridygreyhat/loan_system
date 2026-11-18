<?php
class SMSService {
    private $provider;
    private $apiKey;
    private $apiSecret;
    private $senderId;
    
    public function __construct($provider = 'messaging_service_tz', $apiKey = null, $apiSecret = null, $senderId = 'LoanStore') {
        $this->provider = $provider;
        $this->apiKey = $apiKey ?? getenv('MESSAGING_SERVICE_API_KEY');
        $this->apiSecret = $apiSecret;
        $this->senderId = $senderId;
    }
    
    public function send($to, $message) {
        $to = $this->formatPhoneNumber($to);
        
        switch ($this->provider) {
            case 'messaging_service_tz':
                return $this->sendViaMessagingServiceTZ($to, $message);
            case 'twilio':
                return $this->sendViaTwilio($to, $message);
            case 'africas_talking':
                return $this->sendViaAfricasTalking($to, $message);
            default:
                return ['success' => false, 'error' => 'Unknown provider'];
        }
    }
    
    private function formatPhoneNumber($phone) {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) == 9) {
            $phone = '255' . $phone;
        } elseif (strlen($phone) == 10 && $phone[0] == '0') {
            $phone = '255' . substr($phone, 1);
        } elseif (strpos($phone, '+') === 0) {
            $phone = substr($phone, 1);
        }
        return $phone;
    }
    
    private function sendViaMessagingServiceTZ($to, $message) {
        $url = 'https://messaging-service.co.tz/api/sms/v1/text/single';
        
        $data = [
            'from' => $this->senderId,
            'to' => $to,
            'text' => $message
        ];
        
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Basic ' . $this->apiKey
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        if ($error) {
            return ['success' => false, 'error' => $error];
        }
        
        $result = json_decode($response, true);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            return [
                'success' => true,
                'message_id' => $result['messages'][0]['messageId'] ?? null,
                'response' => $result
            ];
        }
        
        return [
            'success' => false,
            'error' => $result['requestError']['serviceException']['text'] ?? 'Failed to send SMS',
            'response' => $result
        ];
    }
    
    private function sendViaTwilio($to, $message) {
        return ['success' => false, 'error' => 'Twilio not configured'];
    }
    
    private function sendViaAfricasTalking($to, $message) {
        return ['success' => false, 'error' => 'Africa\'s Talking not configured'];
    }
    
    public function sendBulk($recipients, $message) {
        $results = [];
        foreach ($recipients as $to) {
            $results[] = [
                'to' => $to,
                'result' => $this->send($to, $message)
            ];
        }
        return $results;
    }
    
    public function getBalance() {
        if ($this->provider === 'messaging_service_tz') {
            $url = 'https://messaging-service.co.tz/api/account/1/balance';
            
            $headers = [
                'Accept: application/json',
                'Authorization: Basic ' . $this->apiKey
            ];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $response = curl_exec($ch);
                
            return json_decode($response, true);
        }
        
        return null;
    }
}

/**
 * Send one SMS using the provider configured in Admin > SMS Settings.
 * Nothing is sent unless SMS is switched on there and an API key is set
 * (the key may also come from the MESSAGING_SERVICE_API_KEY env variable).
 */
function sendSMS($to, $message, $senderId = null) {
    global $con;
    $settings = [];
    if (isset($con) && $con instanceof PDO) {
        try {
            $settings = $con->query("SELECT * FROM sms_settings ORDER BY is_active DESC, id LIMIT 1")->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            $settings = [];
        }
    }
    if (empty($settings['is_active'])) {
        return ['success' => false, 'error' => 'SMS sending is turned off in SMS Settings'];
    }
    $apiKey = ($settings['api_key'] ?? '') !== '' ? $settings['api_key'] : getenv('MESSAGING_SERVICE_API_KEY');
    if (!$apiKey) {
        return ['success' => false, 'error' => 'No SMS API key configured'];
    }
    $sms = new SMSService(
        $settings['provider'] ?: 'messaging_service_tz',
        $apiKey,
        $settings['api_secret'] ?? null,
        $senderId ?? (($settings['sender_id'] ?? '') !== '' ? $settings['sender_id'] : 'LoanStore')
    );
    return $sms->send($to, $message);
}

function sendBulkSMS($recipients, $message, $senderId = 'LoanStore') {
    $sms = new SMSService('messaging_service_tz', null, null, $senderId);
    return $sms->sendBulk($recipients, $message);
}
