<?php

echo "ERROR: " . ($title ?? 'An error occurred') . "\n";
if (isset($message)) {
    echo "Message: " . $message . "\n";
}
if (isset($file) && isset($line)) {
    echo "File: " . $file . "\n";
    echo "Line: " . $line . "\n";
}


