# 📚 API Documentation - Perpustakaan Kalurahan Trimulyo

Dokumentasi ini ditujukan bagi pengembang yang ingin mengintegrasikan data dari Perpustakaan Kalurahan Trimulyo ke aplikasi lain (Mobile, Web, atau Portal Desa).

## Informasi Umum
- **Base URL**: `https://trimulyo.orbitdev.id/api/`
- **Format Respon**: `JSON`
- **CORS**: Diizinkan (`Access-Control-Allow-Origin: *`)

---

## 1. Info Perpustakaan
Mengambil identitas dan kontak resmi perpustakaan.

- **Endpoint**: `GET /info.php`
- **Parameter**: Tidak ada.

### Contoh Respon:
```json
{
  "status": "success",
  "data": {
    "library_name": "Perpustakaan Kalurahan Trimulyo",
    "description": "...",
    "address": "Jalan Salak Km.3, Trimulyo...",
    "phone": "(0274) 869248",
    "email": "desatrimulyo.slemankab@gmail.com",
    "head_of_library": "Cholik Harmoko, S.TP, NL.P",
    "last_updated": "2026-04-29 07:20:00"
  }
}
```

---

## 2. Katalog Buku
Mengambil daftar koleksi buku yang tersedia di perpustakaan.

- **Endpoint**: `GET /books.php`
- **Parameter**:
  - `q` (string): Kata kunci pencarian (judul, penulis, atau ISBN).
  - `limit` (int): Jumlah data per halaman (default: 10).
  - `page` (int): Nomor halaman (default: 1).

### Contoh Respon:
```json
{
  "status": "success",
  "query": "sejarah",
  "page": 1,
  "limit": 5,
  "data": [
    {
      "id": 1,
      "title": "Sejarah Sleman",
      "author": "Bambang Sudibyo",
      "publisher": "Gramedia",
      "year": 2021,
      "isbn": "978602...",
      "cover_image": "6941a...jpg",
      "category": "Sejarah",
      "cover_url": "https://trimulyo.orbitdev.id/uploads/covers/6941a...jpg"
    }
  ]
}
```

---

## Contoh Implementasi (JavaScript/Fetch)
```javascript
fetch('https://trimulyo.orbitdev.id/api/books.php?q=hujan&limit=5')
  .then(response => response.json())
  .then(data => {
    if(data.status === 'success') {
      console.log(data.data);
    }
  });
```

## Status Code
- `200 OK`: Request berhasil.
- `500 Internal Server Error`: Terjadi kesalahan pada database atau server.
