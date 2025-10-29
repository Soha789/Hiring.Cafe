<?php
session_start();
unset($_SESSION['auth']);
echo "<script>alert('You have been logged out.'); window.location.href='index.php';</script>";
