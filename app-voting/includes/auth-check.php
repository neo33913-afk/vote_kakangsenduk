<?php
require_once __DIR__ . '/../config/app.php';

if (!is_logged_in()) {
    set_flash('danger', 'Silakan login terlebih dahulu.');
    redirect('auth/login.php');
}