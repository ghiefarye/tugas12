<?php
$currentPage = 'mata-kuliah';
$pageTitle = 'Manajemen Mata Kuliah - UniAdmin';
$pageHeader = 'Manajemen Mata Kuliah';

require_once 'config/database.php';

$db = new Database();
$conn = $db->connect();

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;
$message = '';

// DELETE
if ($action == 'delete' && $id) {
    $stmt = $conn->prepare("DELETE FROM mata_kuliah WHERE id = :id");
    $stmt->bindParam(':id', $id);
    if ($stmt->execute()) {
        $message = 'Mata kuliah berhasil dihapus!';
        $action = 'list';
    }
}

// CREATE/UPDATE
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode_mk = $_POST['kode_mk'] ?? '';
    $nama_mk = $_POST['nama_mk'] ?? '';
    $sks = $_POST['sks'] ?? 0;
    $semester = $_POST['semester'] ?? null;
    $prodi_id = $_POST['prodi_id'] ?? null;
    $deskripsi = $_POST['deskripsi'] ?? '';
    $status = $_POST['status'] ?? 'Aktif';
    
    if ($action == 'create') {
        $stmt = $conn->prepare("INSERT INTO mata_kuliah (kode_mk, nama_mk, sks, semester, prodi_id, deskripsi, status) VALUES (:kode_mk, :nama_mk, :sks, :semester, :prodi_id, :deskripsi, :status)");
    } else if ($action == 'update' && $id) {
        $stmt = $conn->prepare("UPDATE mata_kuliah SET kode_mk = :kode_mk, nama_mk = :nama_mk, sks = :sks, semester = :semester, prodi_id = :prodi_id, deskripsi = :deskripsi, status = :status WHERE id = :id");
        $stmt->bindParam(':id', $id);
    }
    
    if (isset($stmt)) {
        $stmt->bindParam(':kode_mk', $kode_mk);
        $stmt->bindParam(':nama_mk', $nama_mk);
        $stmt->bindParam(':sks', $sks);
        $stmt->bindParam(':semester', $semester);
        $stmt->bindParam(':prodi_id', $prodi_id);
        $stmt->bindParam(':deskripsi', $deskripsi);
        $stmt->bindParam(':status', $status);
        
        if ($stmt->execute()) {
            $message = $action == 'create' ? 'Mata kuliah berhasil ditambahkan!' : 'Mata kuliah berhasil diupdate!';
            $action = 'list';
        }
    }
}

$editData = null;
if (($action == 'edit') && $id) {
    $stmt = $conn->prepare("SELECT * FROM mata_kuliah WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $editData = $stmt->fetch(PDO::FETCH_ASSOC);
}

$stmtProdi = $conn->query("SELECT * FROM program_studi ORDER BY nama_prodi");
$prodiList = $stmtProdi->fetchAll(PDO::FETCH_ASSOC);

require_once 'includes/header.php';
?>

<?php if ($message): ?>
<div class="mb-6 p-4 rounded-lg bg-emerald-100 text-emerald-700">
    <?php echo htmlspecialchars($message); ?>
</div>
<?php endif; ?>

<?php if ($action == 'list'): ?>
<div class="flex justify-between items-center mb-6">
    <h3 class="text-2xl font-bold">Daftar Mata Kuliah</h3>
    <a href="?action=create" class="flex items-center gap-2 bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg text-sm font-bold">
        <span class="material-symbols-outlined text-sm">add</span>
        Tambah Mata Kuliah
    </a>
</div>

<div class="bg-white dark:bg-[#111218] rounded-xl border border-slate-200 dark:border-[#282b39] shadow-sm overflow-hidden">
    <table class="w-full">
        <thead class="bg-slate-50 dark:bg-[#1c1d27]">
            <tr>
                <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500 dark:text-[#9da1b9]">Kode MK</th>
                <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500 dark:text-[#9da1b9]">Nama Mata Kuliah</th>
                <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500 dark:text-[#9da1b9]">SKS</th>
                <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500 dark:text-[#9da1b9]">Semester</th>
                <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500 dark:text-[#9da1b9]">Program Studi</th>
                <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500 dark:text-[#9da1b9]">Status</th>
                <th class="px-6 py-4 text-right text-xs font-bold uppercase text-slate-500 dark:text-[#9da1b9]">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-[#3b3f54]">
            <?php
            $stmt = $conn->query("SELECT mk.*, p.nama_prodi FROM mata_kuliah mk LEFT JOIN program_studi p ON mk.prodi_id = p.id ORDER BY mk.kode_mk");
            $mkList = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($mkList as $mk):
            ?>
            <tr class="hover:bg-slate-50 dark:hover:bg-[#282b39]/30">
                <td class="px-6 py-4 font-mono text-sm font-bold"><?php echo htmlspecialchars($mk['kode_mk']); ?></td>
                <td class="px-6 py-4 font-medium"><?php echo htmlspecialchars($mk['nama_mk']); ?></td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                        <?php echo $mk['sks']; ?> SKS
                    </span>
                </td>
                <td class="px-6 py-4 text-sm"><?php echo $mk['semester'] ?? 'N/A'; ?></td>
                <td class="px-6 py-4 text-sm"><?php echo htmlspecialchars($mk['nama_prodi'] ?? 'N/A'); ?></td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold <?php echo $mk['status'] == 'Aktif' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700'; ?>">
                        <?php echo htmlspecialchars($mk['status']); ?>
                    </span>
                </td>
                <td class="px-6 py-4 text-right">
                    <a href="?action=edit&id=<?php echo $mk['id']; ?>" class="text-primary hover:underline mr-3">Edit</a>
                    <a href="?action=delete&id=<?php echo $mk['id']; ?>" class="text-red-500 hover:underline" onclick="return confirm('Hapus mata kuliah ini?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php else: ?>
<div class="bg-white dark:bg-[#111218] rounded-xl border border-slate-200 dark:border-[#282b39] shadow-sm p-8">
    <h3 class="text-xl font-bold mb-6"><?php echo $action == 'create' ? 'Tambah Mata Kuliah Baru' : 'Edit Mata Kuliah'; ?></h3>
    
    <form method="POST" action="?action=<?php echo $action; ?><?php echo $id ? '&id=' . $id : ''; ?>">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium mb-2">Kode MK *</label>
                <input type="text" name="kode_mk" value="<?php echo htmlspecialchars($editData['kode_mk'] ?? ''); ?>" class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700" required/>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-2">Nama Mata Kuliah *</label>
                <input type="text" name="nama_mk" value="<?php echo htmlspecialchars($editData['nama_mk'] ?? ''); ?>" class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700" required/>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-2">SKS *</label>
                <input type="number" name="sks" value="<?php echo $editData['sks'] ?? 3; ?>" min="1" max="6" class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700" required/>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-2">Semester</label>
                <input type="number" name="semester" value="<?php echo $editData['semester'] ?? ''; ?>" min="1" max="14" class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700"/>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-2">Program Studi</label>
                <select name="prodi_id" class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700">
                    <option value="">Pilih Program Studi</option>
                    <?php foreach ($prodiList as $prodi): ?>
                    <option value="<?php echo $prodi['id']; ?>" <?php echo ($editData['prodi_id'] ?? '') == $prodi['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($prodi['nama_prodi']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700">
                    <option value="Aktif" <?php echo ($editData['status'] ?? 'Aktif') == 'Aktif' ? 'selected' : ''; ?>>Aktif</option>
                    <option value="Non-Aktif" <?php echo ($editData['status'] ?? '') == 'Non-Aktif' ? 'selected' : ''; ?>>Non-Aktif</option>
                </select>
            </div>
        </div>
        
        <div class="mt-6">
            <label class="block text-sm font-medium mb-2">Deskripsi</label>
            <textarea name="deskripsi" rows="4" class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700"><?php echo htmlspecialchars($editData['deskripsi'] ?? ''); ?></textarea>
        </div>
        
        <div class="flex gap-4 mt-6">
            <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-6 py-2 rounded-lg font-bold">
                <?php echo $action == 'create' ? 'Tambah' : 'Update'; ?>
            </button>
            <a href="mata-kuliah.php" class="bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 px-6 py-2 rounded-lg font-bold">Batal</a>
        </div>
    </form>
</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
