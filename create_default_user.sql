-- Create default admin user
-- Password: admin123 (hashed with PASSWORD_DEFAULT)
INSERT INTO `pengguna` (`username`, `password_hash`, `nama`, `role`) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin-prov');

-- Create additional test users
INSERT INTO `pengguna` (`username`, `password_hash`, `nama`, `role`) VALUES 
('admin_kab', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Kabupaten', 'admin-kab'),
('penyuluh1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Penyuluh Pertama', 'penyuluh'),
('viewer1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Viewer Pertama', 'viewer');

-- Note: All users have password: admin123
-- You can change these passwords after first login
