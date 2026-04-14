<?php
function validateHotelForm($name, $location, $rating, $rooms, $status) {
    $errors = [];
    if (empty($name) || strlen($name) < 2) {
        $errors[] = 'Hotel name is required and must be at least 2 characters.';
    }
    if (empty($location) || strlen($location) < 2) {
        $errors[] = 'Location is required and must be at least 2 characters.';
    }
    if (!is_numeric($rating) || $rating < 1 || $rating > 5) {
        $errors[] = 'Rating must be between 1 and 5.';
    }
    if (!is_numeric($rooms) || $rooms < 1) {
        $errors[] = 'Rooms must be a positive number.';
    }
    if (!in_array($status, ['Active', 'Inactive'])) {
        $errors[] = 'Status must be Active or Inactive.';
    }
    return $errors;
}
