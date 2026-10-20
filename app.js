const $ = s => document.querySelector(s);
const $$ = s => document.querySelectorAll(s);
let page = 'dashboard', editing = null, toastTimer;
const titles = { dashboard: 'Dashboard', books: 'Data Buku', members: 'Data Anggota', loans: 'Peminjaman', history: 'Riwayat Transaksi' };
const today = new Date();
$('#today').textContent = today.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
function esc(v = '') { return String(v ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c])); }
async function api(action, opts = {}) { const res = await fetch(`api.php?action=${action}${opts.query || ''}`, { method: opts.method || 'GET', headers: { 'Content-Type': 'application/json' }, body: opts.data ? JSON.stringify(opts.data) : undefined }); let data; try {
    data = await res.json();
}
catch {
    throw Error('Respons server tidak valid. Periksa PHP/Laragon.');
} if (!res.ok || data.error)
    throw Error(data.error || 'Permintaan gagal.'); return data; }
function notify(msg, error = false) { const t = $('#toast'); t.textContent = msg; t.className = 'toast show' + (error ? ' error' : ''); clearTimeout(toastTimer); toastTimer = setTimeout(() => t.className = 'toast', 3200); }
function go(p) { page = p; $$('.nav').forEach(b => b.classList.toggle('active', b.dataset.page === p)); $('#pageTitle').textContent = titles[p]; $('#crumb').textContent = p === 'dashboard' ? 'HALAMAN UTAMA' : 'DATA MASTER / ' + titles[p].toUpperCase(); $('#sidebar').classList.remove('open'); render(); }
$$('.nav').forEach(b => b.addEventListener('click', () => go(b.dataset.page)));
$('#menuBtn').onclick = () => $('#sidebar').classList.toggle('open');
function welcome(title, desc, btn = '') { return `<div class="welcome"><div><div class="eyebrow">SISTEM INFORMASI PERPUSTAKAAN</div><h2>${title}</h2><p>${desc}</p></div>${btn}</div>`; }
function panelHead(title, desc, tools = '') { return `<div class="panel-head"><div><h3>${title}</h3><p>${desc}</p></div>${tools}</div>`; }
function stat(label, value, icon, note) { return `<div class="stat"><div class="stat-top"><span>${label}</span><span class="stat-icon">${icon}</span></div><strong>${value}</strong><small>${note}</small></div>`; }
async function render() { try {
    if (page === 'dashboard')
        await dashboard();
    else if (page === 'books')
        await books();
    else if (page === 'members')
        await members();
    else
        await loans();
}
catch (e) {
    $('#content').innerHTML = `<div class="panel empty">Gagal memuat data: ${esc(e.message)}<br><br><button class="btn primary" onclick="render()">Coba lagi</button></div>`;
} }
async function dashboard() { const d = await api('dashboard'), s = d.stats; $('#content').innerHTML = welcome('Selamat datang, Administrator 👋', 'Berikut ringkasan aktivitas perpustakaan hari ini.') + `<div class="stats">${stat('Total Eksemplar Buku', s.books, '▤', 'Seluruh stok buku')}${stat('Buku Tersedia', s.available, '✓', 'Siap dipinjam')}${stat('Total Anggota', s.members, '♙', 'Anggota terdaftar')}${stat('Sedang Dipinjam', s.active, '⇄', 'Belum dikembalikan')}</div><div class="split"><div class="panel">${panelHead('Transaksi Terbaru', 'Aktivitas peminjaman dan pengembalian terakhir')}<div class="table-wrap"><table><thead><tr><th>TRANSAKSI</th><th>PEMINJAM / BUKU</th><th>TANGGAL</th><th>STATUS</th></tr></thead><tbody>${d.recent.length ? d.recent.map(r => `<tr><td><b>${esc(r.transaction_code)}</b></td><td><div class="cell-title">${esc(r.name)}</div><div class="subtext">${esc(r.title)}</div></td><td>${esc(r.loan_date)}</td><td>${status(r.status)}</td></tr>`).join('') : `<tr><td colspan="4" class="empty">Belum ada transaksi.</td></tr>`}</tbody></table></div></div><div class="panel">${panelHead('Akses Cepat', 'Kelola data perpustakaan')}<div class="quick-list"><div class="quick-item"><div class="book-icon">▤</div><div><b>Data Buku</b><small>Tambah atau perbarui koleksi buku</small></div><button class="action-btn" onclick="go('books')">Buka →</button></div><div class="quick-item"><div class="book-icon">♙</div><div><b>Data Anggota</b><small>Kelola informasi anggota</small></div><button class="action-btn" onclick="go('members')">Buka →</button></div><div class="quick-item"><div class="book-icon">⇄</div><div><b>Peminjaman</b><small>Catat pinjam dan pengembalian</small><button class="action-btn" onclick="go('loans')">Buka →</button></div></div></div></div></div>`; }
function status(s) { return `<span class="pill ${s === 'Dipinjam' ? 'orange' : 'green'}">${esc(s)}</span>`; }
function tools(placeholder, addLabel, addFn) { return `<div class="table-tools"><input class="search" id="search" placeholder="${placeholder}"><button class="btn primary" onclick="${addFn}">＋ ${addLabel}</button></div>`; }
async function books(q = '') { const rows = await api('list', { query: `&type=books&q=${encodeURIComponent(q)}` }); $('#content').innerHTML = welcome('Koleksi Buku', 'Kelola seluruh koleksi dan pantau stok yang tersedia.', `<button class="btn primary" onclick="openForm('book')">＋ Tambah Buku</button>`) + `<div class="panel">${panelHead('Daftar Buku', `${rows.length} data buku ditemukan`, `<input class="search" id="search" placeholder="Cari judul, kode, penulis..." value="${esc(q)}">`)}<div class="table-wrap"><table><thead><tr><th>KODE BUKU</th><th>JUDUL & PENULIS</th><th>PENERBIT / TAHUN</th><th>STOK</th><th>STATUS</th><th>AKSI</th></tr></thead><tbody>${rows.length ? rows.map(b => `<tr><td><b>${esc(b.code)}</b></td><td><div class="cell-title">${esc(b.title)}</div><div class="subtext">${esc(b.author)}</div></td><td>${esc(b.publisher || '-')}<div class="subtext">${esc(b.year_published || '-')}</div></td><td>${b.stock_available} / ${b.stock_total}</td><td><span class="pill ${+b.stock_available ? 'green' : 'red'}">${+b.stock_available ? 'Tersedia' : 'Habis'}</span></td><td><div class="actions"><button class="action-btn" onclick='openForm("book",${JSON.stringify(b).replace(/'/g, "&#39;")})'>Edit</button><button class="action-btn" onclick="removeData('book',${b.id})">Hapus</button></div></td></tr>`).join('') : `<tr><td colspan="6" class="empty">Data buku tidak ditemukan.</td></tr>`}</tbody></table></div></div>`; bindSearch(books); }
async function members(q = '') { const rows = await api('list', { query: `&type=members&q=${encodeURIComponent(q)}` }); $('#content').innerHTML = welcome('Data Anggota', 'Simpan dan kelola informasi anggota perpustakaan.', `<button class="btn primary" onclick="openForm('member')">＋ Tambah Anggota</button>`) + `<div class="panel">${panelHead('Daftar Anggota', `${rows.length} anggota ditemukan`, `<input class="search" id="search" placeholder="Cari nama, kode, email..." value="${esc(q)}">`)}<div class="table-wrap"><table><thead><tr><th>KODE ANGGOTA</th><th>NAMA</th><th>EMAIL</th><th>TELEPON</th><th>ALAMAT</th><th>AKSI</th></tr></thead><tbody>${rows.length ? rows.map(m => `<tr><td><b>${esc(m.member_code)}</b></td><td class="cell-title">${esc(m.name)}</td><td>${esc(m.email || '-')}</td><td>${esc(m.phone || '-')}</td><td>${esc(m.address || '-')}</td><td><div class="actions"><button class="action-btn" onclick='openForm("member",${JSON.stringify(m).replace(/'/g, "&#39;")})'>Edit</button><button class="action-btn" onclick="removeData('member',${m.id})">Hapus</button></div></td></tr>`).join('') : `<tr><td colspan="6" class="empty">Data anggota tidak ditemukan.</td></tr>`}</tbody></table></div></div>`; bindSearch(members); }
async function loans(q = '') { const rows = await api('list', { query: `&type=loans&q=${encodeURIComponent(q)}` }); const isHistory = page === 'history'; $('#content').innerHTML = welcome(isHistory ? 'Riwayat Transaksi' : 'Peminjaman Buku', isHistory ? 'Lihat seluruh catatan peminjaman dan pengembalian.' : 'Catat peminjaman baru dan proses pengembalian buku.', isHistory ? '' : `<button class="btn primary" onclick="openForm('loan')">＋ Transaksi Pinjam</button>`) + `<div class="panel">${panelHead(isHistory ? 'Semua Transaksi' : 'Daftar Peminjaman', `${rows.length} transaksi ditemukan`, `<input class="search" id="search" placeholder="Cari kode, buku, anggota..." value="${esc(q)}">`)}<div class="table-wrap"><table><thead><tr><th>KODE TRANSAKSI</th><th>ANGGOTA</th><th>BUKU</th><th>TANGGAL PINJAM</th><th>JATUH TEMPO</th><th>TGL KEMBALI</th><th>STATUS</th>${isHistory ? '' : '<th>AKSI</th>'}</tr></thead><tbody>${rows.length ? rows.map(l => `<tr><td><b>${esc(l.transaction_code)}</b></td><td><div class="cell-title">${esc(l.name)}</div><div class="subtext">${esc(l.member_code)}</div></td><td><div class="cell-title">${esc(l.title)}</div><div class="subtext">${esc(l.code)}</div></td><td>${esc(l.loan_date)}</td><td>${esc(l.due_date)}</td><td>${esc(l.return_date || '-')}</td><td>${status(l.status)}</td>${isHistory ? '' : `<td>${l.status === 'Dipinjam' ? `<button class="action-btn" onclick="returnBook(${l.id})">Kembalikan</button>` : '—'}</td>`}</tr>`).join('') : `<tr><td colspan="${isHistory ? 7 : 8}" class="empty">Belum ada transaksi.</td></tr>`}</tbody></table></div></div>`; bindSearch(loans); }
function bindSearch(fn) { const el = $('#search'); if (!el)
    return; let t; el.addEventListener('input', () => { clearTimeout(t); t = setTimeout(() => fn(el.value), 220); }); }
function field(name, label, type = 'text', value = '', required = false, full = false, extra = '') { return `<div class="field ${full ? 'full' : ''}"><label for="${name}">${label}${required ? ' *' : ''}</label>${type === 'textarea' ? `<textarea id="${name}" name="${name}" ${required ? 'required' : ''}>${esc(value)}</textarea>` : `<input id="${name}" name="${name}" type="${type}" value="${esc(value)}" ${required ? 'required' : ''} ${extra}>`}</div>`; }
function selectField(name, label, options, required = true) { return `<div class="field full"><label for="${name}">${label} *</label><select id="${name}" name="${name}" required>${options}</select></div>`; }
async function openForm(type, data = null) {
    editing = data;
    const form = $('#formFields');
    $('#modalTitle').textContent = (data ? 'Edit ' : 'Tambah ') + ({ book: 'Buku', member: 'Anggota', loan: 'Peminjaman' }[type]);
    let html = '';
    if (type === 'book')
        html = field('code', 'Kode Buku', 'text', data?.code || '', true) + field('title', 'Judul Buku', 'text', data?.title || '', true) + field('author', 'Penulis', 'text', data?.author || '', true) + field('publisher', 'Penerbit', 'text', data?.publisher || '') + field('year_published', 'Tahun Terbit', 'number', data?.year_published || '', false, false, 'min="1000" max="2155"') + field('stock_total', 'Total Stok', 'number', data?.stock_total ?? 1, true, false, 'min="0"');
    if (type === 'member')
        html = field('member_code', 'Kode Anggota', 'text', data?.member_code || '', true) + field('name', 'Nama Lengkap', 'text', data?.name || '', true) + field('email', 'Email', 'email', data?.email || '') + field('phone', 'Nomor Telepon', 'text', data?.phone || '') + field('address', 'Alamat', 'textarea', data?.address || '', false, true);
    if (type === 'loan') {
        const o = await api('options');
        html = selectField('book_id', 'Pilih Buku', `<option value="">Pilih buku tersedia</option>` + o.books.map(b => `<option value="${b.id}">${esc(b.code)} — ${esc(b.title)} (stok ${b.stock_available})</option>`).join('')) + selectField('member_id', 'Pilih Anggota', `<option value="">Pilih anggota</option>` + o.members.map(m => `<option value="${m.id}">${esc(m.member_code)} — ${esc(m.name)}</option>`).join('')) + field('loan_date', 'Tanggal Pinjam', 'date', today.toISOString().slice(0, 10), true) + field('due_date', 'Tanggal Jatuh Tempo', 'date', new Date(Date.now() + 7 * 86400000).toISOString().slice(0, 10), true);
    }
    form.innerHTML = html;
    $('#modalBackdrop').classList.add('show');
    form.dataset.type = type;
}
function closeForm() { $('#modalBackdrop').classList.remove('show'); editing = null; }
$('#closeModal').onclick = closeForm;
$('#cancelModal').onclick = closeForm;
$('#modalBackdrop').addEventListener('click', e => { if (e.target.id === 'modalBackdrop')
    closeForm(); });
$('#dataForm').addEventListener('submit', async (e) => { e.preventDefault(); const type = e.currentTarget.dataset.type; const data = Object.fromEntries(new FormData(e.currentTarget).entries()); if (editing)
    data.id = editing.id; try {
    await api(type === 'book' ? 'save_book' : type === 'member' ? 'save_member' : 'loan', { method: 'POST', data });
    closeForm();
    notify(type === 'loan' ? 'Peminjaman berhasil dicatat. Stok buku otomatis berkurang.' : 'Data berhasil disimpan.');
    render();
}
catch (err) {
    notify(err.message, true);
} });
async function removeData(type, id) { if (!confirm('Yakin ingin menghapus data ini?'))
    return; try {
    await api(type === 'book' ? 'delete_book' : 'delete_member', { method: 'POST', data: { id } });
    notify('Data berhasil dihapus.');
    render();
}
catch (e) {
    notify(e.message, true);
} }
async function returnBook(id) { if (!confirm('Proses pengembalian buku ini?'))
    return; try {
    await api('return', { method: 'POST', data: { id } });
    notify('Buku berhasil dikembalikan. Stok otomatis bertambah.');
    render();
}
catch (e) {
    notify(e.message, true);
} }
render();
