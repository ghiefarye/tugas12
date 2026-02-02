<?php
session_start();
require_once 'config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!empty($email) && !empty($password)) {
        $db = new Database();
        $conn = $db->connect();
        
        $stmt = $conn->prepare("SELECT * FROM admin WHERE email = :email LIMIT 1");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_nama'] = $admin['nama'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['admin_foto'] = $admin['foto'];
                $_SESSION['admin_role'] = $admin['role'];
                
                header('Location: index.php');
                exit();
            } else {
                $error = 'Password salah!';
            }
        } else {
            $error = 'Email tidak ditemukan!';
        }
    } else {
        $error = 'Harap isi semua field!';
    }
}
?>
<!DOCTYPE html>
<html class="dark" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Login - UniAdmin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#1337ec",
                    },
                    fontFamily: {
                        "display": ["Lexend", "sans-serif"]
                    },
                },
            },
        }
    </script>
    <style>
        body { font-family: 'Lexend', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-primary/20 to-slate-900 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl p-8">
            <div class="flex justify-center mb-8">
                <div class="bg-primary rounded-xl p-4">
                    <span class="material-symbols-outlined text-white text-4xl">school</span>
                </div>
            </div>
            
            <h1 class="text-3xl font-bold text-center text-slate-900 dark:text-white mb-2">UniAdmin</h1>
            <p class="text-center text-slate-600 dark:text-slate-400 mb-8">Sistem Manajemen Akademik Kampus</p>
            
            <?php if ($error): ?>
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Email</label>
                    <input type="email" name="email" class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="admin@university.edu" required/>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Password</label>
                    <input type="password" name="password" class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="••••••••" required/>
                </div>
                
                <button type="submit" class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-3 rounded-lg transition-colors mb-4">
                    Masuk
                </button>
                
                <div class="text-center text-sm text-slate-600 dark:text-slate-400">
                    <p class="mb-2">Akun Demo:</p>
                    <p>Email: <strong>admin@university.edu</strong></p>
                    <p>Password: <strong>password123</strong></p>
                </div>
            </form>
        </div>
        
        <div class="text-center mt-6 text-slate-400 text-sm">
            © 2026 UniAdmin. All rights reserved.
        </div>
    </div>
</body>
</html>
