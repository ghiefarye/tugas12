<?php
$currentPage = 'mahasiswa';
$pageTitle = 'Manajemen Mahasiswa - UniAdmin';
$pageHeader = 'Direktori Mahasiswa';

require_once 'config/database.php';

$db = new Database();
$conn = $db->connect();

// Handle Actions
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;
$message = '';
$messageType = 'success';

// DELETE
if ($action == 'delete' && $id) {
    $stmt = $conn->prepare("DELETE FROM mahasiswa WHERE id = :id");
    $stmt->bindParam(':id', $id);
    if ($stmt->execute()) {
        $message = 'Data mahasiswa berhasil dihapus!';
        $action = 'list';
    } else {
        $message = 'Gagal menghapus data!';
        $messageType = 'error';
    }
}

// CREATE/UPDATE
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nim = $_POST['nim'] ?? '';
    $nama = $_POST['nama'] ?? '';
    $email = $_POST['email'] ?? '';
    $prodi_id = $_POST['prodi_id'] ?? null;
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? null;
    $alamat = $_POST['alamat'] ?? '';
    $no_telepon = $_POST['no_telepon'] ?? '';
    $tahun_masuk = $_POST['tahun_masuk'] ?? date('Y');
    $ipk = $_POST['ipk'] ?? 0.00;
    $status = $_POST['status'] ?? 'Aktif';
    
    if ($action == 'create') {
        $password = password_hash('password123', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO mahasiswa (nim, nama, email, password, prodi_id, jenis_kelamin, tanggal_lahir, alamat, no_telepon, tahun_masuk, ipk, status) VALUES (:nim, :nama, :email, :password, :prodi_id, :jenis_kelamin, :tanggal_lahir, :alamat, :no_telepon, :tahun_masuk, :ipk, :status)");
        $stmt->bindParam(':password', $password);
    } else if ($action == 'update' && $id) {
        $stmt = $conn->prepare("UPDATE mahasiswa SET nim = :nim, nama = :nama, email = :email, prodi_id = :prodi_id, jenis_kelamin = :jenis_kelamin, tanggal_lahir = :tanggal_lahir, alamat = :alamat, no_telepon = :no_telepon, tahun_masuk = :tahun_masuk, ipk = :ipk, status = :status WHERE id = :id");
        $stmt->bindParam(':id', $id);
    }
    
    if (isset($stmt)) {
        $stmt->bindParam(':nim', $nim);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':prodi_id', $prodi_id);
        $stmt->bindParam(':jenis_kelamin', $jenis_kelamin);
        $stmt->bindParam(':tanggal_lahir', $tanggal_lahir);
        $stmt->bindParam(':alamat', $alamat);
        $stmt->bindParam(':no_telepon', $no_telepon);
        $stmt->bindParam(':tahun_masuk', $tahun_masuk);
        $stmt->bindParam(':ipk', $ipk);
        $stmt->bindParam(':status', $status);
        
        if ($stmt->execute()) {
            $message = $action == 'create' ? 'Data mahasiswa berhasil ditambahkan!' : 'Data mahasiswa berhasil diupdate!';
            $action = 'list';
        } else {
            $message = 'Gagal menyimpan data!';
            $messageType = 'error';
        }
    }
}

// Get single record for edit/view
$editData = null;
if (($action == 'edit' || $action == 'view') && $id) {
    $stmt = $conn->prepare("SELECT * FROM mahasiswa WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $editData = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get all program studi for dropdown
$stmtProdi = $conn->query("SELECT p.*, f.nama_fakultas FROM program_studi p LEFT JOIN fakultas f ON p.fakultas_id = f.id ORDER BY f.nama_fakultas, p.nama_prodi");
$prodiList = $stmtProdi->fetchAll(PDO::FETCH_ASSOC);

require_once 'includes/header.php';
?>

<?php if ($message): ?>
<div class="mb-6 p-4 rounded-lg <?php echo $messageType == 'success' ? 'bg-emerald-100 border border-emerald-400 text-emerald-700' : 'bg-red-100 border border-red-400 text-red-700'; ?>">
    <?php echo htmlspecialchars($message); ?>
</div>
<?php endif; ?>

<?php if ($action == 'list'): ?>
<!-- List View -->
<div class="flex items-center gap-2 mb-6 text-sm">
    <a class="text-slate-500 dark:text-[#9da1b9] hover:text-primary transition-colors" href="index.php">Dashboard</a>
    <span class="text-slate-300 dark:text-[#3b3f54]">/</span>
    <span class="font-semibold">Direktori Mahasiswa</span>
</div>

<div class="flex justify-between items-center mb-6">
    <div class="flex flex-wrap items-center gap-3">
        <select class="px-4 py-2 bg-white dark:bg-[#282b39] border border-slate-200 dark:border-transparent rounded-lg shadow-sm text-sm" id="filterProdi">
            <option value="">Semua Program Studi</option>
            <?php foreach ($prodiList as $prodi): ?>
            <option value="<?php echo $prodi['id']; ?>"><?php echo htmlspecialchars($prodi['nama_prodi']); ?></option>
            <?php endforeach; ?>
        </select>
        
        <select class="px-4 py-2 bg-white dark:bg-[#282b39] border border-slate-200 dark:border-transparent rounded-lg shadow-sm text-sm" id="filterStatus">
            <option value="">Semua Status</option>
            <option value="Aktif">Aktif</option>
            <option value="Cuti">Cuti</option>
            <option value="Lulus">Lulus</option>
            <option value="Tertunda">Tertunda</option>
        </select>
        
        <button class="flex items-center gap-2 px-4 py-2 text-slate-500 dark:text-[#9da1b9] hover:text-primary transition-colors text-sm font-medium" onclick="resetFilters()">
            <span class="material-symbols-outlined text-lg">filter_list_off</span>
            Hapus Filter
        </button>
    </div>
    
    <a href="?action=create" class="flex items-center gap-2 bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg text-sm font-bold transition-all shadow-sm">
        <span class="material-symbols-outlined text-sm">add</span>
        Tambah Mahasiswa Baru
    </a>
</div>

<div class="bg-white dark:bg-[#111218] rounded-xl border border-slate-200 dark:border-[#282b39] shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse" id="mahasiswaTable">
            <thead>
                <tr class="bg-slate-50 dark:bg-[#1c1d27] border-b border-slate-200 dark:border-[#3b3f54]">
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-[#9da1b9]">Nama Mahasiswa</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-[#9da1b9]">NIM</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-[#9da1b9]">Program Studi</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-[#9da1b9]">IPK</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-[#9da1b9]">Status</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-[#9da1b9] text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-[#3b3f54]">
                <?php
                $stmt = $conn->query("
                    SELECT m.*, p.nama_prodi 
                    FROM mahasiswa m 
                    LEFT JOIN program_studi p ON m.prodi_id = p.id 
                    ORDER BY m.created_at DESC
                ");
                $mahasiswaList = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($mahasiswaList as $mhs):
                    $statusClass = [
                        'Aktif' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20',
                        'Cuti' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 border-amber-200 dark:border-amber-500/20',
                        'Lulus' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400 border-blue-200 dark:border-blue-500/20',
                        'Tertunda' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700'
                    ];
                    $ipkPercent = min(100, ($mhs['ipk'] / 4.0) * 100);
                ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-[#282b39]/30 transition-colors" data-prodi="<?php echo $mhs['prodi_id']; ?>" data-status="<?php echo $mhs['status']; ?>">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-full overflow-hidden bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-primary font-bold">
                                <?php 
                                $initials = strtoupper(substr($mhs['nama'], 0, 1) . substr(strstr($mhs['nama'], ' '), 1, 1));
                                echo $initials;
                                ?>
                            </div>
                            <div>
                                <div class="text-sm font-bold"><?php echo htmlspecialchars($mhs['nama']); ?></div>
                                <div class="text-xs text-slate-500 dark:text-[#9da1b9]"><?php echo htmlspecialchars($mhs['email']); ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-slate-600 dark:text-[#9da1b9]"><?php echo htmlspecialchars($mhs['nim']); ?></td>
                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-[#9da1b9]"><?php echo htmlspecialchars($mhs['nama_prodi'] ?? 'N/A'); ?></td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold"><?php echo number_format($mhs['ipk'], 2); ?></span>
                            <div class="w-16 h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-500 rounded-full" style="width: <?php echo $ipkPercent; ?>%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border <?php echo $statusClass[$mhs['status']]; ?>">
                            <?php echo htmlspecialchars($mhs['status']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="?action=view&id=<?php echo $mhs['id']; ?>" class="p-2 hover:bg-slate-200 dark:hover:bg-[#3b3f54] rounded-lg transition-colors text-primary" title="Lihat Profil">
                                <span class="material-symbols-outlined text-xl">visibility</span>
                            </a>
                            <a href="?action=edit&id=<?php echo $mhs['id']; ?>" class="p-2 hover:bg-slate-200 dark:hover:bg-[#3b3f54] rounded-lg transition-colors text-slate-500 dark:text-[#9da1b9]" title="Edit Data">
                                <span class="material-symbols-outlined text-xl">edit</span>
                            </a>
                            <a href="?action=delete&id=<?php echo $mhs['id']; ?>" class="p-2 hover:bg-slate-200 dark:hover:bg-[#3b3f54] rounded-lg transition-colors text-red-500" title="Hapus Data" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                <span class="material-symbols-outlined text-xl">delete</span>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function resetFilters() {
    document.getElementById('filterProdi').value = '';
    document.getElementById('filterStatus').value = '';
    filterTable();
}

function filterTable() {
    const prodiFilter = document.getElementById('filterProdi').value;
    const statusFilter = document.getElementById('filterStatus').value;
    const rows = document.querySelectorAll('#mahasiswaTable tbody tr');
    
    rows.forEach(row => {
        const prodi = row.getAttribute('data-prodi');
        const status = row.getAttribute('data-status');
        
        const prodiMatch = !prodiFilter || prodi === prodiFilter;
        const statusMatch = !statusFilter || status === statusFilter;
        
        row.style.display = (prodiMatch && statusMatch) ? '' : 'none';
    });
}

document.getElementById('filterProdi').addEventListener('change', filterTable);
document.getElementById('filterStatus').addEventListener('change', filterTable);
</script>

<?php elseif ($action == 'create' || $action == 'edit'): ?>
<!-- Form View -->
<div class="flex items-center gap-2 mb-6 text-sm">
    <a class="text-slate-500 dark:text-[#9da1b9] hover:text-primary transition-colors" href="index.php">Dashboard</a>
    <span class="text-slate-300 dark:text-[#3b3f54]">/</span>
    <a class="text-slate-500 dark:text-[#9da1b9] hover:text-primary transition-colors" href="mahasiswa.php">Mahasiswa</a>
    <span class="text-slate-300 dark:text-[#3b3f54]">/</span>
    <span class="font-semibold"><?php echo $action == 'create' ? 'Tambah Baru' : 'Edit Data'; ?></span>
</div>

<div class="bg-white dark:bg-[#111218] rounded-xl border border-slate-200 dark:border-[#282b39] shadow-sm p-8">
    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6"><?php echo $action == 'create' ? 'Tambah Mahasiswa Baru' : 'Edit Data Mahasiswa'; ?></h3>
    
    <form method="POST" action="?action=<?php echo $action; ?><?php echo $id ? '&id=' . $id : ''; ?>" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">NIM *</label>
                <input type="text" name="nim" value="<?php echo htmlspecialchars($editData['nim'] ?? ''); ?>" class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent" required/>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nama Lengkap *</label>
                <input type="text" name="nama" value="<?php echo htmlspecialchars($editData['nama'] ?? ''); ?>" class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent" required/>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Email *</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($editData['email'] ?? ''); ?>" class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent" required/>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Program Studi *</label>
                <select name="prodi_id" class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent" required>
                    <option value="">Pilih Program Studi</option>
                    <?php foreach ($prodiList as $prodi): ?>
                    <option value="<?php echo $prodi['id']; ?>" <?php echo ($editData['prodi_id'] ?? '') == $prodi['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($prodi['nama_prodi']) . ' - ' . htmlspecialchars($prodi['nama_fakultas']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Jenis Kelamin *</label>
                <select name="jenis_kelamin" class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent" required>
                    <option value="">Pilih Jenis Kelamin</option>
                    <option value="Laki-laki" <?php echo ($editData['jenis_kelamin'] ?? '') == 'Laki-laki' ? 'selected' : ''; ?>>Laki-laki</option>
                    <option value="Perempuan" <?php echo ($editData['jenis_kelamin'] ?? '') == 'Perempuan' ? 'selected' : ''; ?>>Perempuan</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" value="<?php echo $editData['tanggal_lahir'] ?? ''; ?>" class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"/>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">No. Telepon</label>
                <input type="text" name="no_telepon" value="<?php echo htmlspecialchars($editData['no_telepon'] ?? ''); ?>" class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"/>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Tahun Masuk *</label>
                <input type="number" name="tahun_masuk" value="<?php echo $editData['tahun_masuk'] ?? date('Y'); ?>" min="2000" max="2099" class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent" required/>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">IPK</label>
                <input type="number" name="ipk" value="<?php echo $editData['ipk'] ?? '0.00'; ?>" step="0.01" min="0" max="4" class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"/>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Status *</label>
                <select name="status" class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent" required>
                    <option value="Aktif" <?php echo ($editData['status'] ?? 'Aktif') == 'Aktif' ? 'selected' : ''; ?>>Aktif</option>
                    <option value="Cuti" <?php echo ($editData['status'] ?? '') == 'Cuti' ? 'selected' : ''; ?>>Cuti</option>
                    <option value="Lulus" <?php echo ($editData['status'] ?? '') == 'Lulus' ? 'selected' : ''; ?>>Lulus</option>
                    <option value="Tertunda" <?php echo ($editData['status'] ?? '') == 'Tertunda' ? 'selected' : ''; ?>>Tertunda</option>
                </select>
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Alamat</label>
            <textarea name="alamat" rows="3" class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo htmlspecialchars($editData['alamat'] ?? ''); ?></textarea>
        </div>
        
        <div class="flex gap-4">
            <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-6 py-2 rounded-lg font-bold transition-colors">
                <?php echo $action == 'create' ? 'Tambah Mahasiswa' : 'Update Data'; ?>
            </button>
            <a href="mahasiswa.php" class="bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-900 dark:text-white px-6 py-2 rounded-lg font-bold transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>

<?php elseif ($action == 'view' && $editData): ?>
<!-- View Detail -->
<div class="flex items-center gap-2 mb-6 text-sm">
    <a class="text-slate-500 dark:text-[#9da1b9] hover:text-primary transition-colors" href="index.php">Dashboard</a>
    <span class="text-slate-300 dark:text-[#3b3f54]">/</span>
    <a class="text-slate-500 dark:text-[#9da1b9] hover:text-primary transition-colors" href="mahasiswa.php">Mahasiswa</a>
    <span class="text-slate-300 dark:text-[#3b3f54]">/</span>
    <span class="font-semibold">Detail Profil</span>
</div>

<div class="bg-white dark:bg-[#111218] rounded-xl border border-slate-200 dark:border-[#282b39] shadow-sm p-8">
    <div class="flex items-start justify-between mb-6">
        <div class="flex items-center gap-4">
            <div class="size-20 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-2xl">
                <?php 
                $initials = strtoupper(substr($editData['nama'], 0, 1) . substr(strstr($editData['nama'], ' '), 1, 1));
                echo $initials;
                ?>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white"><?php echo htmlspecialchars($editData['nama']); ?></h3>
                <p class="text-slate-500 dark:text-[#9da1b9]"><?php echo htmlspecialchars($editData['nim']); ?></p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="?action=edit&id=<?php echo $editData['id']; ?>" class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg font-bold transition-colors">
                Edit Data
            </a>
            <a href="mahasiswa.php" class="bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-900 dark:text-white px-4 py-2 rounded-lg font-bold transition-colors">
                Kembali
            </a>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-slate-500 dark:text-[#9da1b9] mb-1">Email</label>
            <p class="text-slate-900 dark:text-white font-medium"><?php echo htmlspecialchars($editData['email']); ?></p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-slate-500 dark:text-[#9da1b9] mb-1">Program Studi</label>
            <p class="text-slate-900 dark:text-white font-medium">
                <?php 
                $stmt = $conn->prepare("SELECT nama_prodi FROM program_studi WHERE id = :id");
                $stmt->bindParam(':id', $editData['prodi_id']);
                $stmt->execute();
                $prodi = $stmt->fetch(PDO::FETCH_ASSOC);
                echo htmlspecialchars($prodi['nama_prodi'] ?? 'N/A');
                ?>
            </p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-slate-500 dark:text-[#9da1b9] mb-1">Jenis Kelamin</label>
            <p class="text-slate-900 dark:text-white font-medium"><?php echo htmlspecialchars($editData['jenis_kelamin']); ?></p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-slate-500 dark:text-[#9da1b9] mb-1">Tanggal Lahir</label>
            <p class="text-slate-900 dark:text-white font-medium"><?php echo $editData['tanggal_lahir'] ? date('d F Y', strtotime($editData['tanggal_lahir'])) : 'N/A'; ?></p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-slate-500 dark:text-[#9da1b9] mb-1">No. Telepon</label>
            <p class="text-slate-900 dark:text-white font-medium"><?php echo htmlspecialchars($editData['no_telepon'] ?? 'N/A'); ?></p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-slate-500 dark:text-[#9da1b9] mb-1">Tahun Masuk</label>
            <p class="text-slate-900 dark:text-white font-medium"><?php echo htmlspecialchars($editData['tahun_masuk']); ?></p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-slate-500 dark:text-[#9da1b9] mb-1">IPK</label>
            <p class="text-slate-900 dark:text-white font-medium"><?php echo number_format($editData['ipk'], 2); ?></p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-slate-500 dark:text-[#9da1b9] mb-1">Status</label>
            <p class="text-slate-900 dark:text-white font-medium"><?php echo htmlspecialchars($editData['status']); ?></p>
        </div>
        
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-500 dark:text-[#9da1b9] mb-1">Alamat</label>
            <p class="text-slate-900 dark:text-white font-medium"><?php echo htmlspecialchars($editData['alamat'] ?? 'N/A'); ?></p>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
