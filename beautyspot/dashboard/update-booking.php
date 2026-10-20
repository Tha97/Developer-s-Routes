<?php
require_once __DIR__ . '/../includes/bootstrap.php';
$user = require_role('stylist');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/dashboard.php');
}
csrf_check();

$stmt = db()->prepare('SELECT id FROM stylist_profiles WHERE user_id = ?');
$stmt->execute([$user['id']]);
$profile = $stmt->fetch();

$bookingId = (int) ($_POST['booking_id'] ?? 0);
$status = $_POST['status'] ?? '';

if (in_array($status, ['confirmed', 'declined', 'cancelled'], true)) {
    // Ownership check baked into the WHERE clause: a stylist can only update their own bookings.
    $stmt = db()->prepare('UPDATE bookings SET status = ? WHERE id = ? AND stylist_id = ?');
    $stmt->execute([$status, $bookingId, $profile['id']]);

    if ($stmt->rowCount() > 0 && in_array($status, ['confirmed', 'declined'], true)) {
        // Let the client know the answer by email (best effort — they also see it in their dashboard).
        $stmt = db()->prepare(
            'SELECT b.requested_date, b.requested_time, u.name AS client_name, u.email AS client_email, sp.business_name, sp.owner_phone
             FROM bookings b
             JOIN users u ON u.id = b.client_id
             JOIN (SELECT sp2.id, sp2.business_name, uo.phone AS owner_phone FROM stylist_profiles sp2 JOIN users uo ON uo.id = sp2.user_id) sp ON sp.id = b.stylist_id
             WHERE b.id = ?'
        );
        $stmt->execute([$bookingId]);
        if ($b = $stmt->fetch()) {
            $when = date('l, j F Y', strtotime($b['requested_date'])) . ' at ' . date('H:i', strtotime($b['requested_time']));
            $answer = $status === 'confirmed' ? 'confirmed your booking request' : 'is unable to take your booking request';
            $mailBody = "Hi {$b['client_name']},\n\n{$b['business_name']} {$answer} for {$when}.\n\n"
                      . ($status === 'confirmed'
                          ? 'Need to change something? Contact them' . ($b['owner_phone'] ? " on {$b['owner_phone']}" : '') . ".\n\n"
                          : "You can find other professionals at " . SITE_URL . "/discover.php\n\n")
                      . 'See your bookings: ' . SITE_URL . "/dashboard.php\n\n— " . SITE_NAME;
            send_site_mail($b['client_email'], 'Your booking request: ' . ($status === 'confirmed' ? 'confirmed' : 'declined'), $mailBody);
        }
    }
    flash('success', 'Booking updated.');
}

redirect('/dashboard.php');
