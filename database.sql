CREATE DATABASE IF NOT EXISTS db_perpustakaan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_perpustakaan;

CREATE TABLE IF NOT EXISTS books (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(30) NOT NULL UNIQUE,
  title VARCHAR(150) NOT NULL,
  author VARCHAR(100) NOT NULL,
  publisher VARCHAR(100) DEFAULT '',
  year_published YEAR NULL,
  stock_total INT NOT NULL DEFAULT 1,
  stock_available INT NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS members (
  id INT AUTO_INCREMENT PRIMARY KEY,
  member_code VARCHAR(30) NOT NULL UNIQUE,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(120) DEFAULT '',
  phone VARCHAR(30) DEFAULT '',
  address TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS loans (
  id INT AUTO_INCREMENT PRIMARY KEY,
  transaction_code VARCHAR(40) NOT NULL UNIQUE,
  book_id INT NOT NULL,
  member_id INT NOT NULL,
  loan_date DATE NOT NULL,
  due_date DATE NOT NULL,
  return_date DATE NULL,
  status ENUM('Dipinjam','Dikembalikan') NOT NULL DEFAULT 'Dipinjam',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_loan_book FOREIGN KEY (book_id) REFERENCES books(id),
  CONSTRAINT fk_loan_member FOREIGN KEY (member_id) REFERENCES members(id)
);

INSERT IGNORE INTO books (code,title,author,publisher,year_published,stock_total,stock_available) VALUES
('BK-001','Laskar Pelangi','Andrea Hirata','Bentang Pustaka',2005,5,5),
('BK-002','Bumi Manusia','Pramoedya Ananta Toer','Lentera Dipantara',1980,3,3),
('BK-003','Filosofi Teras','Henry Manampiring','Kompas',2018,4,4),
('BK-004','Atomic Habits','James Clear','Gramedia',2018,2,2);

INSERT IGNORE INTO members (member_code,name,email,phone,address) VALUES
('AG-001','Nadia Putri','nadia@example.com','081234567890','Bondowoso'),
('AG-002','Rizky Ramadhan','rizky@example.com','081298765432','Jember'),
('AG-003','Siti Aisyah','siti@example.com','081377788899','Bondowoso');
