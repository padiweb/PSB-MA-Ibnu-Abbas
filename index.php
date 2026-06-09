<?php
// Redirect langsung ke public/
header('Location: public/index.php' . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''));
exit;