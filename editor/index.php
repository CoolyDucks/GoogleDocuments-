<?php
session_start();
if (!isset($_SESSION['visited'])) {
    $_SESSION['visited'] = true;
    include 'welcome.php';
} else {
    header('Location: editor/main.php');
}
exit;

