<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// Test DB connection
try {
    $pdo = new PDO(
        'mysql:host=sql112.infinityfree.com;dbname=if0_41450308_medistock',
        'if0_41450308',
        'Nemankudi26'
    );
    echo "DB Connected OK<br>";
    
    // Check users table
    $stmt = $pdo->query("SELECT id, name, email, role FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Users in DB:<br>";
    foreach($users as $u) {
        echo "- {$u['name']} | {$u['email']} | {$u['role']}<br>";
    }
    
    // Test password
    $stmt = $pdo->query("SELECT password FROM users WHERE email='admin@hospital.com'");
    $row = $stmt->fetch();
    if($row) {
        $test = password_verify('Admin@12345', $row['password']);
        echo "<br>Password 'Admin@12345' matches: " . ($test ? 'YES' : 'NO') . "<br>";
        $test2 = password_verify('password', $row['password']);
        echo "Password 'password' matches: " . ($test2 ? 'YES' : 'NO') . "<br>";
    }
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

---

## Step 2 — Upload & Run

Upload to `/htdocs/public/` via FileZilla, then visit:
```
http://medistock.page.gd/debug.php