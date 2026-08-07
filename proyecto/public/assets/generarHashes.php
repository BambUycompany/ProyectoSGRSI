<?php
    echo "Usuario 2 roles: " . password_hash("password123", PASSWORD_DEFAULT) . "\n";
    echo "Usuario 3 roles: " . password_hash("password456", PASSWORD_DEFAULT) . "\n";
    echo "Usuario 0 rol: " . password_hash("sinrol", PASSWORD_DEFAULT) . "\n";
?>