<?php

require_once __DIR__ . '/../src/Utility/auth.php';

logoutUser();

header('Location: index.php');
exit;