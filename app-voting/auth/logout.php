<?php
require_once __DIR__ . '/../config/app.php';

unset($_SESSION['user']);
session_regenerate_id(true);
set_flash('success', 'Anda sudah keluar.');
redirect('auth/login.php');