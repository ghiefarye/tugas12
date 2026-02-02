<?php
$currentPage = 'laporan';
$pageTitle = 'Laporan - UniAdmin';
$pageHeader = 'Laporan & Analitik';

require_once 'config/database.php';

$db = new Database();
$conn = $db->connect();

// Statistics
$totalMahasiswa = $conn->query("SELECT COUNT(*) as total FROM mahasiswa")->fetch(PDO::FETCH_ASSOC)['total'];
$totalAktif = $conn->query("SELECT COUNT(*) as total FROM mahasiswa WHERE status = 'Aktif'")->fetch(PDO::FETCH_ASSOC)['total'];
$totalCuti = $conn->query("SELECT COUNT(*) as total FROM mahasiswa WHERE status = 'Cuti'")->fetch(PDO::FETCH_ASSOC)['total'];
$totalLulus = $conn->query("SELECT COUNT(*) as total FROM mahasiswa WHERE status = 'Lulus'")->fetch(PDO::FETCH_ASSOC)['total'];

$avgIPK = $conn->query("SELECT AVG(ipk) as rata FROM mahasiswa WHERE ipk > 0")->fetch(PDO::FETCH_ASSOC)['rata'];

// Per Prodi
$stmtProdi = $conn->query("
    SELECT p.nama_prodi, COUNT(m.id) as jumlah, AVG(m.ipk) as avg_ipk
    FROM program_studi p
    LEFT JOIN mahasiswa m ON p.id = m.prodi_id
    GROUP BY p.id, p.nama_prodi
    ORDER BY jumlah DESC
");
$dataProdi = $stmtProdi->fetchAll(PDO::FETCH_ASSOC);

// Per Tahun
$stmtTahun = $conn->query("
    SELECT tahun_masuk, COUNT(*) as jumlah
    FROM mahasiswa
    GROUP BY tahun_masuk
    ORDER BY tahun_masuk DESC
");
$dataTahun = $stmtTahun->fetchAll(PDO::FETCH_ASSOC);

require_once 'includes/header.php';
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white dark:bg-[#111218] p-6 rounded-xl border border-slate-200 dark:border-[#3b3f54] shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <span class="material-symbols-outlined text-primary text-2xl">groups</span>
            </div>
        </div>
        <p class="text-slate-500 dark:text-[#9da1b9] text-sm mb-1">Total Mahasiswa</p>
        <h3 class="text-3xl font-bold text-slate-900 dark:text-white"><?php echo number_format($totalMahasiswa); ?></h3>
    </div>
    
    <div class="bg-white dark:bg-[#111218] p-6 rounded-xl border border-slate-200 dark:border-[#3b3f54] shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg">
                <span class="material-symbols-outlined text-emerald-600 text-2xl">check_circle</span>
            </div>
        </div>
        <p class="text-slate-500 dark:text-[#9da1b9] text-sm mb-1">Mahasiswa Aktif</p>
        <h3 class="text-3xl font-bold text-slate-900 dark:text-white"><?php echo number_format($totalAktif); ?></h3>
    </div>
    
    <div class="bg-white dark:bg-[#111218] p-6 rounded-xl border border-slate-200 dark:border-[#3b3f54] shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                <span class="material-symbols-outlined text-amber-600 text-2xl">pause_circle</span>
            </div>
        </div>
        <p class="text-slate-500 dark:text-[#9da1b9] text-sm mb-1">Mahasiswa Cuti</p>
        <h3 class="text-3xl font-bold text-slate-900 dark:text-white"><?php echo number_format($totalCuti); ?></h3>
    </div>
    
    <div class="bg-white dark:bg-[#111218] p-6 rounded-xl border border-slate-200 dark:border-[#3b3f54] shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                <span class="material-symbols-outlined text-purple-600 text-2xl">school</span>
            </div>
        </div>
        <p class="text-slate-500 dark:text-[#9da1b9] text-sm mb-1">Lulusan</p>
        <h3 class="text-3xl font-bold text-slate-900 dark:text-white"><?php echo number_format($totalLulus); ?></h3>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Statistik Per Program Studi -->
    <div class="bg-white dark:bg-[#111218] rounded-xl border border-slate-200 dark:border-[#3b3f54] shadow-sm p-6">
        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6">Statistik Per Program Studi</h3>
        <div class="space-y-4">
            <?php foreach ($dataProdi as $prodi): ?>
            <div class="border-b border-slate-100 dark:border-[#3b3f54] pb-4 last:border-0">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-medium text-slate-900 dark:text-white"><?php echo htmlspecialchars($prodi['nama_prodi']); ?></span>
                    <span class="text-sm font-bold text-primary"><?php echo number_format($prodi['jumlah']); ?> mahasiswa</span>
                </div>
                <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-[#9da1b9]">
                    <span>Rata-rata IPK:</span>
                    <span class="font-bold text-slate-900 dark:text-white"><?php echo number_format($prodi['avg_ipk'] ?? 0, 2); ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Statistik Per Tahun Masuk -->
    <div class="bg-white dark:bg-[#111218] rounded-xl border border-slate-200 dark:border-[#3b3f54] shadow-sm p-6">
        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6">Statistik Per Tahun Masuk</h3>
        <div class="space-y-3">
            <?php foreach ($dataTahun as $tahun): ?>
            <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-[#1c1d27] rounded-lg">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-primary/10 rounded-lg">
                        <span class="material-symbols-outlined text-primary">calendar_today</span>
                    </div>
                    <span class="font-bold text-slate-900 dark:text-white">Tahun <?php echo $tahun['tahun_masuk']; ?></span>
                </div>
                <span class="text-lg font-bold text-primary"><?php echo number_format($tahun['jumlah']); ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- IPK Distribution -->
<div class="bg-white dark:bg-[#111218] rounded-xl border border-slate-200 dark:border-[#3b3f54] shadow-sm p-6">
    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6">Distribusi IPK Mahasiswa</h3>
    <div class="mb-4">
        <p class="text-sm text-slate-500 dark:text-[#9da1b9]">Rata-rata IPK Keseluruhan</p>
        <p class="text-3xl font-bold text-primary"><?php echo number_format($avgIPK ?? 0, 2); ?></p>
    </div>
    
    <?php
    $ipkRanges = [
        ['label' => '3.51 - 4.00 (Cumlaude)', 'min' => 3.51, 'max' => 4.00, 'color' => 'bg-emerald-500'],
        ['label' => '3.01 - 3.50 (Sangat Memuaskan)', 'min' => 3.01, 'max' => 3.50, 'color' => 'bg-blue-500'],
        ['label' => '2.76 - 3.00 (Memuaskan)', 'min' => 2.76, 'max' => 3.00, 'color' => 'bg-yellow-500'],
        ['label' => '< 2.75 (Cukup)', 'min' => 0, 'max' => 2.75, 'color' => 'bg-orange-500']
    ];
    
    foreach ($ipkRanges as $range):
        $stmt = $conn->prepare("SELECT COUNT(*) as jumlah FROM mahasiswa WHERE ipk >= :min AND ipk <= :max");
        $stmt->bindParam(':min', $range['min']);
        $stmt->bindParam(':max', $range['max']);
        $stmt->execute();
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['jumlah'];
        $percentage = $totalMahasiswa > 0 ? ($count / $totalMahasiswa) * 100 : 0;
    ?>
    <div class="mb-4">
        <div class="flex justify-between text-sm mb-2">
            <span class="text-slate-600 dark:text-[#9da1b9]"><?php echo $range['label']; ?></span>
            <span class="font-bold text-slate-900 dark:text-white"><?php echo $count; ?> (<?php echo number_format($percentage, 1); ?>%)</span>
        </div>
        <div class="w-full bg-slate-100 dark:bg-[#282b39] h-3 rounded-full overflow-hidden">
            <div class="<?php echo $range['color']; ?> h-full rounded-full transition-all" style="width: <?php echo $percentage; ?>%"></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Export Section -->
<div class="mt-8 bg-white dark:bg-[#111218] rounded-xl border border-slate-200 dark:border-[#3b3f54] shadow-sm p-6">
    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4">Export Laporan</h3>
    <div class="flex flex-wrap gap-4">
        <button onclick="alert('Export Excel akan segera tersedia')" class="flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-lg font-bold transition-colors">
            <span class="material-symbols-outlined">table_chart</span>
            Export ke Excel
        </button>
        <button onclick="alert('Export PDF akan segera tersedia')" class="flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-bold transition-colors">
            <span class="material-symbols-outlined">picture_as_pdf</span>
            Export ke PDF
        </button>
        <button onclick="window.print()" class="flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-bold transition-colors">
            <span class="material-symbols-outlined">print</span>
            Cetak Laporan
        </button>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
