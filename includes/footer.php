    </main>
    <?php
    $footer_name = isset($site_name) ? $site_name : (function_exists('getSetting') && isset($pdo) ? getSetting($pdo, 'library_name', 'Perpustakaan Hybrid') : 'Perpustakaan Hybrid');
    $footer_logo = isset($site_logo) ? $site_logo : (function_exists('getSetting') && isset($pdo) ? getSetting($pdo, 'library_logo', '') : '');
    $footer_addr = function_exists('getSetting') && isset($pdo) ? getSetting($pdo, 'library_address', 'Kalurahan Trimulyo, Sleman, Daerah Istimewa Yogyakarta') : 'Kalurahan Trimulyo, Sleman, Daerah Istimewa Yogyakarta';
    $footer_email = function_exists('getSetting') && isset($pdo) ? getSetting($pdo, 'library_email', '') : '';
    $footer_phone = function_exists('getSetting') && isset($pdo) ? getSetting($pdo, 'library_phone', '') : '';
    ?>
    <footer class="bg-mauve-900 text-white py-8">
        <div class="container mx-auto px-4 grid md:grid-cols-3 gap-8">
            <div>
                <div class="flex items-start gap-4">
                    <?php if (!empty($footer_logo)): ?>
                        <img src="<?= BASE_URL ?>uploads/logo/<?= htmlspecialchars($footer_logo) ?>" class="h-12 w-12 object-contain flex-shrink-0" alt="Logo">
                    <?php endif; ?>
                    <div>
                        <h3 class="text-xl font-bold text-white mb-2"><?= htmlspecialchars($footer_name) ?></h3>
                        <p class="text-sm">
                            Membuka jendela dunia melalui koleksi fisik dan digital.
                            Belajar tanpa batas, di mana saja dan kapan saja.
                        </p>
                    </div>
                </div>
            </div>
            <div>
                <h3 class="text-xl font-bold text-white mb-4">Kontak Kami</h3>
                <ul class="space-y-2 text-sm">
                    <li class="flex items-center">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <?= htmlspecialchars($footer_addr) ?>
                    </li>
                    <li class="flex items-center">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        <?= htmlspecialchars($footer_email) ?>
                    </li>
                    <li class="flex items-center">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                        <?= htmlspecialchars($footer_phone) ?>
                    </li>
                </ul>
            </div>
            <div>
                <h3 class="text-xl font-bold text-white mb-4">Jam Operasional</h3>
                <ul class="space-y-2 text-sm">
                    <li>Senin - Jumat: 08:00 - 16:00</li>
                    <li>Sabtu: 08:00 - 12:00</li>
                    <li>Minggu: Tutup</li>
                </ul>
            </div>
        </div>
        <div class="text-center text-sm mt-8 pt-8 border-t border-mauve-800">
            &copy; <?php echo date('Y'); ?> <?= htmlspecialchars($footer_name) ?>. All rights reserved<a href="<?= BASE_URL ?>admin/index.php" class="text-lemon_chiffon-400 hover:text-lemon_chiffon-300">.</a>
        </div>
    </footer>

    <!-- Mobile Bottom Navigation -->
    <div class="fixed bottom-0 left-0 z-50 w-full h-16 bg-white border-t border-gray-200 md:hidden shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">
        <div class="grid h-full grid-cols-4 mx-auto font-medium">
            <a href="<?= BASE_URL ?>index.php" class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 group text-gray-500 hover:text-mauve-900 transition-colors duration-200">
                <svg class="w-6 h-6 mb-1 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span class="text-[10px] font-bold">Beranda</span>
            </a>
            <a href="<?= BASE_URL ?>catalog.php" class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 group text-gray-500 hover:text-mauve-900 transition-colors duration-200">
                <svg class="w-6 h-6 mb-1 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <span class="text-[10px] font-bold">Katalog</span>
            </a>
            <a href="<?= BASE_URL ?>news.php" class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 group text-gray-500 hover:text-mauve-900 transition-colors duration-200">
                <svg class="w-6 h-6 mb-1 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                <span class="text-[10px] font-bold">Berita</span>
            </a>
            <a href="<?= isset($_SESSION['member_id']) ? BASE_URL.'member/index.php' : BASE_URL.'login.php' ?>" class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 group text-gray-500 hover:text-mauve-900 transition-colors duration-200">
                <svg class="w-6 h-6 mb-1 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <span class="text-[10px] font-bold">Anggota</span>
            </a>
        </div>
    </div>
</body>
</html>
