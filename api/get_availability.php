<?php
include '../config/db.php';

header('Content-Type: application/json');

if (!isset($_GET['vehicle_id'])) {
    echo json_encode(['error' => 'Missing vehicle_id']);
    exit;
}

$vehicle_id = intval($_GET['vehicle_id']);

try {
    // Fetch bookings that are not cancelled
    $stmt = $conn->prepare("SELECT start_date, end_date FROM bookings WHERE vehicle_id = ? AND status != 'cancelled'");
    $stmt->execute([$vehicle_id]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $blocked_dates = [];
    foreach ($bookings as $booking) {
        $blocked_dates[] = [
            'from' => $booking['start_date'],
            'to' => $booking['end_date']
        ];
    }

    echo json_encode(['blocked' => $blocked_dates]);
} catch (PDOException $e) {
    // Log error internally (already handled in db.php but good to be safe)
    echo json_encode(['error' => 'Database error']);
}
?>
