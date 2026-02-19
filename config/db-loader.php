<?php
// config/db-loader.php

function isRailway() {
    return getenv('PGHOST') !== false;
}

if (isRailway()) {
    require_once __DIR__ . '/railway-db.php';
} else {
    require_once __DIR__ . '/db.php';
}

