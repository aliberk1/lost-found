# 📦 Lost & Found — PHP + MySQL Project  

Kayıp eşyaları paylaşmak, aramak ve yönetmek için geliştirilmiş basit bir **PHP & MySQL** projesidir.  
Bu repo, herkesin kendi bilgisayarında kolayca çalıştırabilmesi için gerekli tüm dosyaları içerir.  

## 🖥️ Gereksinimler  
- **PHP 8+** → [php.net/downloads](https://www.php.net/downloads)  
- **MySQL Server + Workbench** → [dev.mysql.com/downloads/installer](https://dev.mysql.com/downloads/installer/)  
- Tarayıcı (Chrome, Edge vb.)

---

## ⚙️ Kurulum Adımları  

### 1️⃣ Kodları indir  
GitHub sayfasından:  
**Code → Download ZIP** veya  
```bash
git clone https://github.com/<kullanici_adin>/lost_found.git



2️⃣ Veritabanını oluştur

MySQL Workbench aç.

Menü: Server → Data Import

“Import from Self-Contained File” seçeneğini işaretle.

sql/seed.sql dosyasını seç.

“Default Target Schema” kısmına lost_found yaz.

Start Import de ✅

Artık veritabanın hazır!

3️⃣ env.php dosyasını düzenle

Kök klasördeki env.php dosyasını aç ve kendi bilgisayarındaki MySQL bilgilerine göre düzenle 👇

<?php
const DB_HOST = '127.0.0.1';  // veya localhost
const DB_NAME = 'lost_found'; // veritabanı adı
const DB_USER = 'root';       // MySQL kullanıcı adı
const DB_PASS = '';           // Şifre (boşsa boş bırak)
?>

4️⃣ Projeyi çalıştır

Proje klasöründe terminal (CMD) aç ve yaz:

php -S localhost:8000


Sonra tarayıcıdan:
👉 http://localhost:8000

Her şey doğruysa proje açılacak 


