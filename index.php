<?php
$currentPage = 'dashboard';
$pageTitle = 'Dashboard - UniAdmin';
$pageHeader = 'Ringkasan Dashboard';

require_once 'config/database.php';
require_once 'includes/header.php';

$db = new Database();
$conn = $db->connect();

// Get Statistics
$stmt = $conn->query("SELECT COUNT(*) as total FROM mahasiswa");
$totalMahasiswa = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->query("SELECT COUNT(*) as total FROM mata_kuliah WHERE status = 'Aktif'");
$totalMataKuliah = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->query("SELECT COUNT(*) as total FROM dosen WHERE status = 'Aktif'");
$totalDosen = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->query("SELECT COUNT(*) as total FROM mahasiswa WHERE status = 'Tertunda'");
$totalPending = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Get Recent Students
$stmt = $conn->query("
    SELECT m.*, p.nama_prodi 
    FROM mahasiswa m 
    LEFT JOIN program_studi p ON m.prodi_id = p.id 
    ORDER BY m.created_at DESC 
    LIMIT 5
");
$recentStudents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get Department Distribution
$stmt = $conn->query("
    SELECT p.nama_prodi, COUNT(m.id) as jumlah,
    ROUND((COUNT(m.id) * 100.0 / (SELECT COUNT(*) FROM mahasiswa)), 0) as persentase
    FROM mahasiswa m
    JOIN program_studi p ON m.prodi_id = p.id
    GROUP BY p.id, p.nama_prodi
    ORDER BY jumlah DESC
    LIMIT 4
");
$departmentDist = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Card 1: Total Mahasiswa -->
    <div class="bg-white dark:bg-[#111218] p-6 rounded-xl border border-slate-200 dark:border-[#3b3f54] shadow-sm">
        <div class="flex justify-between items-start mb-4">
            <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <span class="material-symbols-outlined text-primary">groups</span>
            </div>
            <span class="text-emerald-500 dark:text-[#0bda65] text-xs font-bold flex items-center">
                <span class="material-symbols-outlined text-xs mr-1">trending_up</span>+2%
            </span>
        </div>
        <p class="text-slate-500 dark:text-[#9da1b9] text-sm font-medium mb-1">Total Mahasiswa</p>
        <h3 class="text-slate-900 dark:text-white text-2xl font-bold"><?php echo number_format($totalMahasiswa); ?></h3>
    </div>

    <!-- Card 2: Mata Kuliah Aktif -->
    <div class="bg-white dark:bg-[#111218] p-6 rounded-xl border border-slate-200 dark:border-[#3b3f54] shadow-sm">
        <div class="flex justify-between items-start mb-4">
            <div class="p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                <span class="material-symbols-outlined text-purple-600">auto_stories</span>
            </div>
            <span class="text-slate-400 dark:text-[#9da1b9] text-xs font-bold">0% perubahan</span>
        </div>
        <p class="text-slate-500 dark:text-[#9da1b9] text-sm font-medium mb-1">Mata Kuliah Aktif</p>
        <h3 class="text-slate-900 dark:text-white text-2xl font-bold"><?php echo number_format($totalMataKuliah); ?></h3>
    </div>

    <!-- Card 3: Anggota Fakultas -->
    <div class="bg-white dark:bg-[#111218] p-6 rounded-xl border border-slate-200 dark:border-[#3b3f54] shadow-sm">
        <div class="flex justify-between items-start mb-4">
            <div class="p-2 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                <span class="material-symbols-outlined text-orange-600">account_balance_wallet</span>
            </div>
            <span class="text-emerald-500 dark:text-[#0bda65] text-xs font-bold flex items-center">
                <span class="material-symbols-outlined text-xs mr-1">trending_up</span>+1%
            </span>
        </div>
        <p class="text-slate-500 dark:text-[#9da1b9] text-sm font-medium mb-1">Anggota Fakultas</p>
        <h3 class="text-slate-900 dark:text-white text-2xl font-bold"><?php echo number_format($totalDosen); ?></h3>
    </div>

    <!-- Card 4: Pendaftaran Tertunda -->
    <div class="bg-white dark:bg-[#111218] p-6 rounded-xl border border-slate-200 dark:border-[#3b3f54] shadow-sm">
        <div class="flex justify-between items-start mb-4">
            <div class="p-2 bg-red-50 dark:bg-red-900/20 rounded-lg">
                <span class="material-symbols-outlined text-red-600">pending_actions</span>
            </div>
            <span class="text-red-500 dark:text-[#fa6538] text-xs font-bold flex items-center">
                <span class="material-symbols-outlined text-xs mr-1">trending_down</span>-5%
            </span>
        </div>
        <p class="text-slate-500 dark:text-[#9da1b9] text-sm font-medium mb-1">Pendaftaran Tertunda</p>
        <h3 class="text-slate-900 dark:text-white text-2xl font-bold"><?php echo number_format($totalPending); ?></h3>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
    <!-- Tren Pendaftaran -->
    <div class="xl:col-span-2 bg-white dark:bg-[#111218] rounded-xl border border-slate-200 dark:border-[#3b3f54] p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-slate-900 dark:text-white text-lg font-bold">Tren Pendaftaran</h3>
                <p class="text-slate-500 dark:text-[#9da1b9] text-sm">Pendaftaran mahasiswa bulanan untuk tahun akademik saat ini</p>
            </div>
            <div class="flex gap-2">
                <button class="px-3 py-1.5 text-xs font-medium rounded-lg border border-slate-200 dark:border-[#3b3f54] text-slate-600 dark:text-white hover:bg-slate-50 dark:hover:bg-[#282b39]">Mingguan</button>
                <button class="px-3 py-1.5 text-xs font-medium rounded-lg bg-primary text-white">Bulanan</button>
            </div>
        </div>
        <div class="h-64 w-full mt-4">
            <svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 800 240">
                <defs>
                    <linearGradient id="chartGradient" x1="0" x2="0" y1="0" y2="1">
                        <stop offset="0%" stop-color="#1337ec" stop-opacity="0.2"></stop>
                        <stop offset="100%" stop-color="#1337ec" stop-opacity="0"></stop>
                    </linearGradient>
                </defs>
                <path d="M0 200 C 50 180, 100 190, 150 140 S 250 80, 300 100 S 400 160, 450 130 S 550 40, 600 60 S 700 30, 800 20 V 240 H 0 Z" fill="url(#chartGradient)"></path>
                <path d="M0 200 C 50 180, 100 190, 150 140 S 250 80, 300 100 S 400 160, 450 130 S 550 40, 600 60 S 700 30, 800 20" fill="none" stroke="#1337ec" stroke-linecap="round" stroke-width="3"></path>
                <circle cx="150" cy="140" fill="#1337ec" r="4"></circle>
                <circle cx="300" cy="100" fill="#1337ec" r="4"></circle>
                <circle cx="450" cy="130" fill="#1337ec" r="4"></circle>
                <circle cx="600" cy="60" fill="#1337ec" r="4"></circle>
                <circle cx="800" cy="20" fill="#1337ec" r="4"></circle>
            </svg>
        </div>
        <div class="flex justify-between mt-4 text-[11px] font-bold text-slate-400 dark:text-[#9da1b9] uppercase tracking-wider">
            <span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>Mei</span><span>Jun</span><span>Jul</span><span>Agu</span><span>Sep</span><span>Okt</span><span>Nov</span><span>Des</span>
        </div>
    </div>

    <!-- Distribusi Departemen -->
    <div class="bg-white dark:bg-[#111218] rounded-xl border border-slate-200 dark:border-[#3b3f54] p-6 shadow-sm">
        <h3 class="text-slate-900 dark:text-white text-lg font-bold mb-4">Distribusi Departemen</h3>
        <div class="space-y-5">
            <?php 
            $colors = ['bg-primary', 'bg-blue-400', 'bg-purple-500', 'bg-orange-500'];
            foreach ($departmentDist as $index => $dept): 
            ?>
            <div>
                <div class="flex justify-between text-sm mb-1.5">
                    <span class="text-slate-600 dark:text-[#9da1b9]"><?php echo htmlspecialchars($dept['nama_prodi']); ?></span>
                    <span class="text-slate-900 dark:text-white font-bold"><?php echo $dept['persentase']; ?>%</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-[#282b39] h-2 rounded-full overflow-hidden">
                    <div class="<?php echo $colors[$index]; ?> h-full rounded-full" style="width: <?php echo $dept['persentase']; ?>%"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="mt-8 pt-6 border-t border-slate-100 dark:border-[#282b39]">
            <a href="laporan.php" class="w-full block text-center py-2.5 text-primary bg-primary/5 dark:bg-primary/10 rounded-lg text-sm font-bold hover:bg-primary/10 dark:hover:bg-primary/20 transition-colors">
                Lihat Analitik Lengkap
            </a>
        </div>
    </div>
</div>

<!-- Aktivitas Mahasiswa Terbaru -->
<div class="mt-8 bg-white dark:bg-[#111218] rounded-xl border border-slate-200 dark:border-[#3b3f54] shadow-sm overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-100 dark:border-[#282b39] flex items-center justify-between">
        <h3 class="text-slate-900 dark:text-white text-lg font-bold">Aktivitas Mahasiswa Terbaru</h3>
        <a href="mahasiswa.php" class="text-sm font-bold text-primary hover:underline">Lihat Semua Mahasiswa</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50 dark:bg-[#1a1c26] text-slate-500 dark:text-[#9da1b9] text-[11px] uppercase tracking-wider font-bold">
                <tr>
                    <th class="px-6 py-4">Nama Mahasiswa</th>
                    <th class="px-6 py-4">NIM</th>
                    <th class="px-6 py-4">Departemen</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-[#282b39]">
                <?php foreach ($recentStudents as $student): 
                    $statusClass = [
                        'Aktif' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400',
                        'Cuti' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
                        'Lulus' => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300',
                        'Tertunda' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400'
                    ];
                    $initials = strtoupper(substr($student['nama'], 0, 1) . substr(strstr($student['nama'], ' '), 1, 1));
                ?>
                <tr class="hover:bg-slate-50/50 dark:hover:bg-[#1a1c26]/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-primary font-bold text-xs">
                                <?php echo $initials; ?>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-slate-900 dark:text-white"><?php echo htmlspecialchars($student['nama']); ?></span>
                                <span class="text-[11px] text-slate-500 dark:text-[#9da1b9]"><?php echo htmlspecialchars($student['email']); ?></span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-slate-600 dark:text-[#9da1b9]">#<?php echo htmlspecialchars($student['nim']); ?></td>
                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-white"><?php echo htmlspecialchars($student['nama_prodi']); ?></td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold <?php echo $statusClass[$student['status']]; ?>">
                            <?php echo htmlspecialchars($student['status']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="mahasiswa.php?action=view&id=<?php echo $student['id']; ?>" class="text-slate-400 hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-xl">more_vert</span>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
