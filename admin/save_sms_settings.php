<?php
require_once '../helpers/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $provider = $_POST['provider'] ?? 'twilio';
    $api_key = $_POST['api_key'] ?? '';
    $api_secret = $_POST['api_secret'] ?? '';
    $sender_id = $_POST['sender_id'] ?? '';
    $is_active = isset($_POST['is_active']) ? true : false;
    
    try {
        $stmt = $con->query("SELECT id FROM sms_settings LIMIT 1");
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existing) {
            if ($api_secret === '********') {
                $stmt = $con->prepare("UPDATE sms_settings SET provider = ?, api_key = ?, sender_id = ?, is_active = ? WHERE id = ?");
                $stmt->execute([$provider, $api_key, $sender_id, $is_active, $existing['id']]);
            } else {
                $stmt = $con->prepare("UPDATE sms_settings SET provider = ?, api_key = ?, api_secret = ?, sender_id = ?, is_active = ? WHERE id = ?");
                $stmt->execute([$provider, $api_key, $api_secret, $sender_id, $is_active, $existing['id']]);
            }
        } else {
            $stmt = $con->prepare("INSERT INTO sms_settings (provider, api_key, api_secret, sender_id, is_active) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$provider, $api_key, $api_secret, $sender_id, $is_active]);
        }
        
        header("Location: sms_settings.php?success=1");
        exit;
    } catch (PDOException $e) {
        header("Location: sms_settings.php?error=" . urlencode($e->getMessage()));
        exit;
    }
} else {
    header("Location: sms_settings.php");
    exit;
}
