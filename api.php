<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/config.php';
session_start();
function out($data, $code=200) { http_response_code($code); echo json_encode($data, JSON_UNESCAPED_UNICODE); exit; }
function body() { return json_decode(file_get_contents('php://input'), true) ?? $_POST; }
$action = $_GET['action'] ?? '';
try {
  if ($action === 'dashboard') {
    $stats = [
      'books' => (int)$pdo->query("SELECT COALESCE(SUM(stock_total),0) FROM books")->fetchColumn(),
      'available' => (int)$pdo->query("SELECT COALESCE(SUM(stock_available),0) FROM books")->fetchColumn(),
      'members' => (int)$pdo->query("SELECT COUNT(*) FROM members")->fetchColumn(),
      'active' => (int)$pdo->query("SELECT COUNT(*) FROM loans WHERE status='Dipinjam'")->fetchColumn(),
      'returned' => (int)$pdo->query("SELECT COUNT(*) FROM loans WHERE status='Dikembalikan'")->fetchColumn()
    ];
    out(['stats'=>$stats,'recent'=>$pdo->query("SELECT l.transaction_code,b.title,m.name,l.loan_date,l.due_date,l.return_date,l.status FROM loans l JOIN books b ON b.id=l.book_id JOIN members m ON m.id=l.member_id ORDER BY l.id DESC LIMIT 6")->fetchAll()]);
  }
  if ($action === 'list') {
    $type = $_GET['type'] ?? '';
    $q = trim($_GET['q'] ?? '');
    $like = "%$q%";
    if ($type === 'books') {
      $s=$pdo->prepare("SELECT * FROM books WHERE code LIKE ? OR title LIKE ? OR author LIKE ? ORDER BY id DESC");
      $s->execute([$like,$like,$like]); out($s->fetchAll());
    }
    if ($type === 'members') {
      $s=$pdo->prepare("SELECT * FROM members WHERE member_code LIKE ? OR name LIKE ? OR email LIKE ? ORDER BY id DESC");
      $s->execute([$like,$like,$like]); out($s->fetchAll());
    }
    if ($type === 'loans') {
      $s=$pdo->prepare("SELECT l.*,b.title,b.code,m.name,m.member_code FROM loans l JOIN books b ON b.id=l.book_id JOIN members m ON m.id=l.member_id WHERE l.transaction_code LIKE ? OR b.title LIKE ? OR m.name LIKE ? ORDER BY l.id DESC");
      $s->execute([$like,$like,$like]); out($s->fetchAll());
    }
    out([],400);
  }
  if ($action === 'save_book') {
    $d=body(); $id=(int)($d['id']??0); $code=trim($d['code']??''); $title=trim($d['title']??''); $author=trim($d['author']??'');
    if (!$code || !$title || !$author) out(['error'=>'Kode, judul, dan penulis wajib diisi.'],422);
    $total=max(0,(int)($d['stock_total']??1));
    if ($id) {
      $s=$pdo->prepare("SELECT stock_total,stock_available FROM books WHERE id=?");$s->execute([$id]);$old=$s->fetch();
      if (!$old) out(['error'=>'Buku tidak ditemukan.'],404);
      $borrowed=(int)$old['stock_total']-(int)$old['stock_available'];
      if ($total<$borrowed) out(['error'=>'Stok total tidak boleh lebih kecil dari jumlah buku yang sedang dipinjam.'],422);
      $avail=$total-$borrowed;
      $s=$pdo->prepare("UPDATE books SET code=?,title=?,author=?,publisher=?,year_published=?,stock_total=?,stock_available=? WHERE id=?");
      $s->execute([$code,$title,$author,$d['publisher']??'',($d['year_published']??'')?:null,$total,$avail,$id]);
    } else {
      $s=$pdo->prepare("INSERT INTO books(code,title,author,publisher,year_published,stock_total,stock_available) VALUES(?,?,?,?,?,?,?)");
      $s->execute([$code,$title,$author,$d['publisher']??'',($d['year_published']??'')?:null,$total,$total]);
    }
    out(['success'=>true]);
  }
  if ($action === 'delete_book') {
    $id=(int)(body()['id']??0);
    $s=$pdo->prepare("SELECT COUNT(*) FROM loans WHERE book_id=?");$s->execute([$id]);
    if ($s->fetchColumn()>0) out(['error'=>'Buku memiliki riwayat transaksi, jadi tidak dapat dihapus.'],422);
    $s=$pdo->prepare("DELETE FROM books WHERE id=?");$s->execute([$id]);out(['success'=>true]);
  }
  if ($action === 'save_member') {
    $d=body();$id=(int)($d['id']??0);$code=trim($d['member_code']??'');$name=trim($d['name']??'');
    if (!$code||!$name) out(['error'=>'Kode anggota dan nama wajib diisi.'],422);
    if($id){$s=$pdo->prepare("UPDATE members SET member_code=?,name=?,email=?,phone=?,address=? WHERE id=?");$s->execute([$code,$name,$d['email']??'',$d['phone']??'',$d['address']??'',$id]);}
    else{$s=$pdo->prepare("INSERT INTO members(member_code,name,email,phone,address) VALUES(?,?,?,?,?)");$s->execute([$code,$name,$d['email']??'',$d['phone']??'',$d['address']??'']);}
    out(['success'=>true]);
  }
  if ($action === 'delete_member') {
    $id=(int)(body()['id']??0);$s=$pdo->prepare("SELECT COUNT(*) FROM loans WHERE member_id=?");$s->execute([$id]);
    if($s->fetchColumn()>0)out(['error'=>'Anggota memiliki riwayat transaksi, jadi tidak dapat dihapus.'],422);
    $s=$pdo->prepare("DELETE FROM members WHERE id=?");$s->execute([$id]);out(['success'=>true]);
  }
  if ($action === 'options') {
    out(['books'=>$pdo->query("SELECT id,code,title,stock_available FROM books WHERE stock_available>0 ORDER BY title")->fetchAll(),
         'members'=>$pdo->query("SELECT id,member_code,name FROM members ORDER BY name")->fetchAll()]);
  }
  if ($action === 'loan') {
    $d=body();$book=(int)($d['book_id']??0);$member=(int)($d['member_id']??0);$loan=$d['loan_date']??date('Y-m-d');$due=$d['due_date']??date('Y-m-d',strtotime('+7 days'));
    if(!$book||!$member||$due<$loan)out(['error'=>'Pilih buku dan anggota. Tanggal jatuh tempo harus sama atau setelah tanggal pinjam.'],422);
    $pdo->beginTransaction();
    $s=$pdo->prepare("SELECT stock_available FROM books WHERE id=? FOR UPDATE");$s->execute([$book]);$available=$s->fetchColumn();
    if($available===false||(int)$available<1){$pdo->rollBack();out(['error'=>'Stok buku tidak tersedia.'],422);}
    $code='TRX-'.date('YmdHis').'-'.random_int(100,999);
    $s=$pdo->prepare("INSERT INTO loans(transaction_code,book_id,member_id,loan_date,due_date) VALUES(?,?,?,?,?)");$s->execute([$code,$book,$member,$loan,$due]);
    $s=$pdo->prepare("UPDATE books SET stock_available=stock_available-1 WHERE id=?");$s->execute([$book]);$pdo->commit();out(['success'=>true]);
  }
  if ($action === 'return') {
    $id=(int)(body()['id']??0);$pdo->beginTransaction();
    $s=$pdo->prepare("SELECT book_id,status FROM loans WHERE id=? FOR UPDATE");$s->execute([$id]);$loan=$s->fetch();
    if(!$loan||$loan['status']!=='Dipinjam'){$pdo->rollBack();out(['error'=>'Transaksi tidak ditemukan atau sudah dikembalikan.'],422);}
    $s=$pdo->prepare("UPDATE loans SET status='Dikembalikan',return_date=CURDATE() WHERE id=?");$s->execute([$id]);
    $s=$pdo->prepare("UPDATE books SET stock_available=LEAST(stock_total,stock_available+1) WHERE id=?");$s->execute([$loan['book_id']]);$pdo->commit();out(['success'=>true]);
  }
  out(['error'=>'Aksi tidak dikenal.'],404);
} catch (PDOException $e) {
  if($pdo->inTransaction())$pdo->rollBack();
  out(['error'=>str_contains($e->getMessage(),'Duplicate')?'Kode sudah digunakan. Periksa kembali kode unik.':'Terjadi kesalahan database: '.$e->getMessage()],500);
}
