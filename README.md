# MATC - Sistem Pengurusan Mesyuarat Agung Tahunan Cabang

Sistem pengesahan kehadiran digital untuk Mesyuarat Agung Tahunan Cabang Parti Keadilan Rakyat (PKR) Kepala Batas, Pulau Pinang.

## Ciri-ciri Utama

- **Pengesahan Kehadiran Digital** - Borang kehadiran awam untuk 4 kategori: Anggota, AJK Cabang, Wanita, AMK
- **Penjanaan Kod QR** - Admin boleh menjana kod QR unik untuk setiap kategori kehadiran
- **Panel Pentadbir** - Dashboard dengan statistik kehadiran, pengurusan ahli, mesyuarat dan rekod kehadiran
- **Keselamatan** - Perlindungan brute force, honeypot anti-bot, penghadan kadar (rate limiting), penyulitan IC/telefon
- **Reka Bentuk Responsif** - Antara muka mesra mudah alih dengan tema gelap PKR

## Teknologi

- **Backend:** Laravel 12, PHP 8.4
- **Frontend:** Vue 3 (Composition API), Inertia.js, Tailwind CSS
- **Autentikasi:** Laravel Breeze
- **Pangkalan Data:** MySQL
- **Seni Bina:** Repository Pattern, Service Layer, Form Requests, Policies

## Keperluan Sistem

- PHP >= 8.4 (dengan extension `curl`, `intl`)
- Composer
- Node.js >= 18
- MySQL >= 8.0

## Pemasangan

1. **Klon repositori**
   ```bash
   git clone https://github.com/chillocreative/matc.git
   cd matc
   ```

2. **Pasang dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Kemaskini `.env` dengan maklumat pangkalan data:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=matc
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Sediakan pangkalan data**
   ```bash
   php artisan migrate
   php artisan db:seed --class=AdminSeeder
   ```

5. **Bina aset frontend**
   ```bash
   npm run build
   ```

6. **Jalankan pelayan**
   ```bash
   php artisan serve
   ```

## Akaun Pentadbir

Selepas menjalankan `AdminSeeder`:

- **Emel:** admin@matc.local
- **Kata Laluan:** password

## Struktur Projek

```
app/
├── Enums/           # CategoryType, MeetingStatus, AttendanceStatus, UserRole
├── Http/
│   ├── Controllers/ # DashboardController, MemberController, MeetingController,
│   │                # AttendanceController, QrAttendanceController
│   ├── Middleware/   # HandleInertiaRequests
│   └── Requests/    # Form validation (StoreMember, UpdateMember, dll.)
├── Models/          # User, Member, Meeting, Attendance
├── Policies/        # Authorization policies
├── Repositories/    # Repository pattern (Contracts + Eloquent)
├── Rules/           # MalaysianIc custom validation
├── Services/        # AttendanceService, MemberService, MeetingService,
│                    # BruteForceProtection
└── Providers/       # AppServiceProvider, RepositoryServiceProvider

resources/js/
├── Components/      # KehadiranForm, NavLink, Dropdown, dll.
├── Layouts/         # AuthenticatedLayout, GuestLayout
└── Pages/
    ├── Auth/        # Login
    ├── Dashboard.vue
    ├── Member/      # Index, Create, Edit, Show
    ├── Meeting/     # Index, Create, Edit, Show
    ├── Attendance/  # Index, Mark, Verify
    ├── QrAttendance/ # Show (admin QR), PublicForm, Hadir
    └── Profile/     # Edit
```

## Laluan Utama

| Laluan | Keterangan |
|--------|------------|
| `/` | Halaman utama mesyuarat |
| `/anggota` | Borang kehadiran Anggota |
| `/ajk-cabang` | Borang kehadiran AJK Cabang |
| `/wanita` | Borang kehadiran Wanita |
| `/amk` | Borang kehadiran AMK |
| `/login` | Log masuk pentadbir |
| `/dashboard` | Panel pentadbir |
| `/qr/{kategori}` | Penjanaan QR Code (admin) |

## Lesen

Hak cipta terpelihara.
