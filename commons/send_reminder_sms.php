<?php
header('Content-Type: application/json');
require_once '../helpers/connect.php';
require_once '../helpers/sms.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$loan_id = (int)($input['loan_id'] ?? 0);
$phone = trim($input['phone'] ?? '');

if (!$loan_id || !$phone) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

try {
    $stmt = $con->prepare("SELECT l.loan_no, l.principal_amt, b.fname, b.lname FROM loans l LEFT JOIN borrower b ON l.borrower_id = b.id WHERE l.id = ?");
    $stmt->execute([$loan_id]);
    $loan = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$loan) {
        echo json_encode(['success' => false, 'error' => 'Loan not found']);
        exit;
    }

    $message = "Dear " . ($loan['fname'] ?? '') . " " . ($loan['lname'] ?? '') . ", your loan " . ($loan['loan_no'] ?? 'L-' . $loan_id) . " has a missed repayment. Please make payment as soon as possible to avoid penalties. Contact us for assistance. - Loan Store";

    $result = sendSMS($phone, $message);

    if ($result['success']) {
        // Log the SMS in communication_logs
        try {
            $logStmt = $con->prepare("INSERT INTO communication_logs (loan_id, type, recipient, message, status, created_at) VALUES (?, 'sms', ?, ?, 'sent', NOW())");
            $logStmt->execute([$loan_id, $phone, $message]);
        } catch (PDOException $e) {}
    }

    echo json_encode($result);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
