<?php
function validateHotelForm($name, $location, $rating, $rooms, $status) {
    $errors = [];
    if (empty($name)) {
        $errors[] = 'Hotel name is required.';
    }
    if (empty($location)) {
        $errors[] = 'Location is required.';
    }
    if (!is_numeric($rating) || $rating < 1 || $rating > 5) {
        $errors[] = 'Rating must be between 1 and 5.';
    }
    if (!is_numeric($rooms) || $rooms < 1) {
        $errors[] = 'Rooms must be a positive number.';
    }
    if ($status !== 'Active' && $status !== 'Inactive') {
        $errors[] = 'Status must be Active or Inactive.';
    }
    return $errors;
}
