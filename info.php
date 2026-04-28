<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

$info = [];
$keys = ['library_name', 'library_description', 'library_address', 'library_phone', 'library_email', 'library_permit', 'library_head'];
foreach ($keys as $key) {
    $info[$key] = function_exists('getSetting') ? getSetting($pdo, $key) : '';
}
?>

<div class="bg-gray-100 min-h-screen py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-mauve-900 text-white p-8 md:p-12 text-center relative overflow-hidden">
                <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
                <h1 class="text-3xl md:text-4xl font-bold mb-4 relative z-10"><?= htmlspecialchars($info['library_name']) ?></h1>
                <p class="text-icy_blue-100 text-lg max-w-2xl mx-auto relative z-10"><?= htmlspecialchars($info['library_description']) ?></p>
            </div>

            <div class="p-8 md:p-12">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    <!-- Left Column: Main Info -->
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">Identitas Perpustakaan</h2>
                        
                        <div class="space-y-6">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-icy_blue-100 rounded-full p-3 mr-4">
                                    <svg class="w-6 h-6 text-frosted_mint-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Alamat</h3>
                                    <p class="text-gray-600 mt-1"><?= nl2br(htmlspecialchars($info['library_address'])) ?></p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-icy_blue-100 rounded-full p-3 mr-4">
                                    <svg class="w-6 h-6 text-frosted_mint-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Nomor Pokok / Ijin</h3>
                                    <p class="text-gray-600 mt-1"><?= htmlspecialchars($info['library_permit']) ?></p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-icy_blue-100 rounded-full p-3 mr-4">
                                    <svg class="w-6 h-6 text-frosted_mint-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Kepala Perpustakaan</h3>
                                    <p class="text-gray-600 mt-1"><?= htmlspecialchars($info['library_head']) ?></p>
                                    <a href="uploads/info.pdf" target="_blank" class="inline-block mt-2 bg-baby_pink-600 hover:bg-baby_pink-700 text-white text-sm font-bold py-2 px-4 rounded shadow transition flex items-center w-fit">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Info Perpusnas
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Contact & Hours -->
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">Kontak Kami</h2>
                        
                        <div class="space-y-6">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-icy_blue-100 rounded-full p-3 mr-4">
                                    <svg class="w-6 h-6 text-frosted_mint-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Telepon</h3>
                                    <p class="text-gray-600 mt-1"><?= htmlspecialchars($info['library_phone']) ?></p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-icy_blue-100 rounded-full p-3 mr-4">
                                    <svg class="w-6 h-6 text-frosted_mint-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Email</h3>
                                    <p class="text-gray-600 mt-1"><?= htmlspecialchars($info['library_email']) ?></p>
                                </div>
                            </div>

                            <div class="mt-8 bg-icy_blue-100 p-6 rounded-xl border border-icy_blue-200">
                                <h3 class="font-bold text-baby_pink-700 mb-2 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Jam Operasional
                                </h3>
                                <ul class="text-mauve-900 space-y-1 text-sm">
                                    <li class="flex justify-between"><span>Senin - Kamis</span> <span class="font-semibold">08.00 - 16.00</span></li>
                                    <li class="flex justify-between"><span>Jumat</span> <span class="font-semibold">08.00 - 11.00 & 13.00 - 16.00</span></li>
                                    <li class="flex justify-between"><span>Sabtu - Minggu</span> <span class="font-semibold">Tutup</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PDF Embed Section -->
                <div class="mt-12 pt-12 border-t border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-frosted_mint-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Dokumen Perpusnas
                    </h2>
                    <div class="w-full bg-gray-100 rounded-xl overflow-hidden shadow-inner border border-gray-200" style="height: 800px;">
                        <object data="uploads/perpusnas.pdf" type="application/pdf" class="w-full h-full">
                            <div class="flex flex-col items-center justify-center h-full text-gray-500">
                                <p class="mb-4">Browser Anda tidak mendukung tampilan PDF.</p>
                                <a href="uploads/perpusnas.pdf" class="bg-baby_pink-600 text-white px-4 py-2 rounded hover:bg-baby_pink-700 transition">
                                    Download PDF
                                </a>
                            </div>
                        </object>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
