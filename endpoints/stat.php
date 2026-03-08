<?php
// endpoints/stats.php

require_once __DIR__ . '/../api/auth.php';
require_once __DIR__ . '/../api/functions.php';

$user = require_auth();

$stats = getStats();

sendSuccess($stats);
?>