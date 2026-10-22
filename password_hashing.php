<?php
$filename = "users.txt";

echo "=== USER REGISTRATION & AUTHENTICATION SYSTEM ===\n\n";

function registerUser($username, $password, $filename) {
    $salt = bin2hex(random_bytes(16));
    
    $hashedPassword = hash('sha256', $password . $salt);
    
    $record = $username . "|" . $salt . "|" . $hashedPassword . "\n";
    
    file_put_contents($filename, $record, FILE_APPEND);
    
    echo "[REGISTRATION SUCCESS] User '{$username}' registered successfully!\n";
    echo "Generated Salt: {$salt}\n";
    echo "Stored Hash: {$hashedPassword}\n\n";
}

function loginUser($username, $password, $filename) {
    if (!file_exists($filename)) {
        echo "[LOGIN FAILED] User database file not found.\n";
        return;
    }
    
    $lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $userFound = false;
    
    foreach ($lines as $line) {
        list($storedUser, $storedSalt, $storedHash) = explode("|", $line);
        
        if ($storedUser === $username) {
            $userFound = true;
            $inputHash = hash('sha256', $password . $storedSalt);
            
            if ($inputHash === $storedHash) {
                echo "[LOGIN SUCCESS] Welcome back, {$username}! Authentication successful.\n\n";
            } else {
                echo "[LOGIN FAILED] Incorrect password for user '{$username}'.\n\n";
            }
            break;
        }
    }
    
    if (!$userFound) {
        echo "[LOGIN FAILED] Username '{$username}' not found.\n\n";
    }
}


if (file_exists($filename)) unlink($filename);

echo "--- STEP 1: Registering New Users ---\n";
registerUser("marwa", "SecurePass2026!", $filename);
registerUser("ahmed", "MySecret123", $filename);

echo "--- STEP 2: Testing Valid Login ---\n";
loginUser("marwa", "SecurePass2026!", $filename);

echo "--- STEP 3: Testing Invalid Password ---\n";
loginUser("marwa", "WrongPassword", $filename);

echo "--- STEP 4: Testing Non-Existing User ---\n";
loginUser("unknown_user", "somepass", $filename);
?>