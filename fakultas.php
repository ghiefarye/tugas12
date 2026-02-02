<?php
$currentPage = 'fakultas';
$pageTitle = 'Manajemen Fakultas - UniAdmin';
$pageHeader = 'Manajemen Fakultas';

require_once 'config/database.php';

$db = new Database();
$conn = $db->connect();

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;
$message = '';
$messageType = 'success';

// DELETE
if ($action == 'delete' && $id) {
    $stmt = $conn->prepare("DELETE FROM fakultas WHERE id = :id");
    $stmt->bindParam(':id', $id);
    if ($stmt->execute()) {
        $message = 'Fakultas berhasil dihapus!';
        $action = 'list';
    }
}

// CREATE/UPDATE
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode_fakultas = $_POST['kode_fakultas'] ?? '';
    $nama_fakultas = $_POST['nama_fakultas'] ?? '';
    $dekan = $_POST['dekan'] ?? '';
    $jumlah_prodi = $_POST['jumlah_prodi'] ?? 0;
    
    if ($action == 'create') {
        $stmt = $conn->prepare("INSERT INTO fakultas (kode_fakultas, nama_fakultas, dekan, jumlah_prodi) VALUES (:kode_fakultas, :nama_fakultas, :dekan, :jumlah_prodi)");
    } else if ($action == 'update' && $id) {
        $stmt = $conn->prepare("UPDATE fakultas SET kode_fakultas = :kode_fakultas, nama_fakultas = :nama_fakultas, dekan = :dekan, jumlah_prodi = :jumlah_prodi WHERE id = :id");
        $stmt->bindParam(':id', $id);
    }
    
    if (isset($stmt)) {
        $stmt->bindParam(':kode_fakultas', $kode_fakultas);
        $stmt->bindParam(':nama_fakultas', $nama_fakultas);
        $stmt->bindParam(':dekan', $dekan);
        $stmt->bindParam(':jumlah_prodi', $jumlah_prodi);
        
        if ($stmt->execute()) {
            $message = $action == 'create' ? 'Fakultas berhasil ditambahkan!' : 'Fakultas berhasil diupdate!';
            $action = 'list';
        }
    }
}

$editData = null;
if (($action == 'edit') && $id) {
    $stmt = $conn->prepare("SELECT * FROM fakultas WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $editData = $stmt->fetch(PDO::FETCH_ASSOC);
}

require_once 'includes/header.php';
?>

<?php if ($message): ?>
<div class="mb-6 p-4 rounded-lg <?php echo $messageType == 'success' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'; ?>">
    <?php echo htmlspecialchars($message); ?>
</div>
<?php endif; ?>

<?php if ($action == 'list'): ?>
<div class="flex justify-between items-center mb-6">
    <h3 class="text-2xl font-bold">Daftar Fakultas</h3>
    <a href="?action=create" class="flex items-center gap-2 bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg text-sm font-bold transition-all">
        <span class="material-symbols-outlined text-sm">add</span>
        Tambah Fakultas
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php
    $stmt = $conn->query("SELECT f.*, COUNT(p.id) as total_prodi FROM fakultas f LEFT JOIN program_studi p ON f.id = p.fakultas_id GROUP BY f.id");
    $fakultasList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($fakultasList as $fak):
    ?>
    <div class="bg-white dark:bg-[#111218] rounded-xl border border-slate-200 dark:border-[#3b3f54] shadow-sm p-6">
        <div class="flex justify-between items-start mb-4">
            <div class="p-3 bg-primary/10 rounded-lg">
                <span class="material-symbols-outlined text-primary text-2xl">school</span>
            </div>
            <div class="flex gap-1">
                <a href="?action=edit&id=<?php echo $fak['id']; ?>" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg">
                    <span class="material-symbols-outlined text-sm">edit</span>
                </a>
                <a href="?action=delete&id=<?php echo $fak['id']; ?>" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg text-red-500" onclick="return confirm('Hapus fakultas ini?')">
                    <span class="material-symbols-outlined text-sm">delete</span>
                </a>
            </div>
        </div>
        
        <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-2"><?php echo htmlspecialchars($fak['nama_fakultas']); ?></h4>
        <p class="text-sm text-slate-500 dark:text-[#9da1b9] mb-4">Kode: <?php echo htmlspecialchars($fak['kode_fakultas']); ?></p>
        
        <div class="border-t border-slate-200 dark:border-[#3b3f54] pt-4 space-y-2">
            <div class="flex justify-between text-sm">
                <span class="text-slate-500 dark:text-[#9da1b9]">Dekan:</span>
                <span class="font-medium text-slate-900 dark:text-white"><?php echo htmlspecialchars($fak['dekan'] ?? 'N/A'); ?></span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-slate-500 dark:text-[#9da1b9]">Jumlah Prodi:</span>
                <span class="font-bold text-primary"><?php echo $fak['total_prodi']; ?></span>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php else: ?>
<div class="bg-white dark:bg-[#111218] rounded-xl border border-slate-200 dark:border-[#282b39] shadow-sm p-8">
    <h3 class="text-xl font-bold mb-6"><?php echo $action == 'create' ? 'Tambah Fakultas Baru' : 'Edit Fakultas'; ?></h3>
    
    <form method="POST" action="?action=<?php echo $action; ?><?php echo $id ? '&id=' . $id : ''; ?>">
        <div class="space-y-6">
            <div>
                <label class="block text-sm font-medium mb-2">Kode Fakultas *</label>
                <input type="text" name="kode_fakultas" value="<?php echo htmlspecialchars($editData['kode_fakultas'] ?? ''); ?>" class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700" required/>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-2">Nama Fakultas *</label>
                <input type="text" name="nama_fakultas" value="<?php echo htmlspecialchars($editData['nama_fakultas'] ?? ''); ?>" class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700" required/>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-2">Dekan</label>
                <input type="text" name="dekan" value="<?php echo htmlspecialchars($editData['dekan'] ?? ''); ?>" class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700"/>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-2">Jumlah Program Studi</label>
                <input type="number" name="jumlah_prodi" value="<?php echo $editData['jumlah_prodi'] ?? 0; ?>" class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700"/>
            </div>
        </div>
        
        <div class="flex gap-4 mt-6">
            <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-6 py-2 rounded-lg font-bold">
                <?php echo $action == 'create' ? 'Tambah' : 'Update'; ?>
            </button>
            <a href="fakultas.php" class="bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 px-6 py-2 rounded-lg font-bold">Batal</a>
        </div>
    </form>
</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
