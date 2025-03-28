# Laravel Blog Yönetim Sistemi

## Açıklama

Bu, Vue.js ile frontend ve Laravel ile backend geliştirilmiş bir Blog Yönetim Sistemidir. Uygulama, kullanıcı yönetimi, blog yazısı oluşturma ve yönetimi, yorum yönetimi, kategori yönetimi ve bildirimler gibi özellikler içerir. Backend API'leri, başka ortamlarla (mobil, masaüstü vb.) kullanılabilir olması için genişletilebilir şekilde tasarlanmıştır. Proje, dosya yükleme, aktivite kaydı, yayına yayınlama ve API kimlik doğrulaması gibi kütüphanelerle entegrasyon sağlar.

## Gereksinimler

Proje kurulmadan önce gerekli bağımlılıkları yükleyin:

- PHP >= 8.1
- Composer
- Node.js ve NPM
- Laravel 12
- MySQL
- Vue.js (3.x)
- TailwindCSS
- Aşağıda belirtilen frontend kütüphaneleri

### Backend Bağımlılıklarını Yükleme

1. Depoyu yerel makinenize klonlayın:
    ```bash
    git clone https://github.com/yourusername/blog-management-system.git
    cd blog-management-system
    ```

2. Backend bağımlılıklarını yükleyin:
    ```bash
    composer install
    ```

3. `.env.example` dosyasını `.env` olarak kopyalayın ve veritabanı gibi çevresel değişkenleri yapılandırın:
    ```bash
    cp .env.example .env
    ```

4. Uygulama anahtarını oluşturun:
    ```bash
    php artisan key:generate
    ```

5. Veritabanı migrasyonlarını ve seed'leri çalıştırarak gerekli verileri oluşturun:
    ```bash
    php artisan migrate --seed
    ```

6. Laravel için gerekli frontend bağımlılıklarını yükleyin:
    ```bash
    npm install
    ```

7. Backend API'yi başlatın:
    ```bash
    php artisan serve
    ```

### Frontend Bağımlılıklarını Yükleme

1. `frontend` dizinine gidin:
    ```bash
    cd frontend
    ```

2. Vue.js ve diğer frontend bağımlılıklarını yükleyin:
    ```bash
    npm install
    ```

3. Geliştirme sunucusunu başlatın:
    ```bash
    npm run dev
    ```

## Proje Özellikleri

### Kullanıcı Yönetimi
- Kullanıcılar telefon numarası veya e-posta ile kayıt olabilir ve giriş yapabilir.
- Kullanıcı rolleri: admin, yazar ve kullanıcı.
- Admin tüm kaynaklara erişebilirken, yazarlar yalnızca kendi yazılarına erişebilir.

### Blog Yazıları
- Yazarlar blog yazılarını oluşturabilir, düzenleyebilir ve silebilir.
- Her yazının başlık, içerik, kapak görseli, slug ve yayın tarihi gibi bilgileri bulunur.
- Yazıların yayın durumu: taslak veya yayında.
- Yazılar soft delete özelliği ile silinebilir.

### Kategoriler
- Admin kategorileri oluşturabilir, düzenleyebilir ve silebilir.
- Blog yazıları bir veya birden fazla kategoriye ait olabilir.

### Yorumlar
- Tüm kullanıcı rolleri yazılara yorum yapabilir.
- Admin yorumları onaylayabilir veya silebilir. Eğer admin yorum yaparsa, bu yorum otomatik olarak onaylanır.
- Yorumlar soft delete özelliği ile silinebilir.

### Bildirimler
- Bir yazıya yorum yapıldığında yazar bildirilir.
- Bildirimler mail, veritabanı veya broadcasting kullanılarak gönderilebilir.
- Yeni yorumlar, blog yazısının sayfasında canlı olarak gösterilir.

### Otomatik Yazı Silme
- Bir hafta boyunca yorum almayan yazılar otomatik olarak silinir.

## Seed Verisi

Üç örnek kullanıcı seed edilmiş olarak gelir:
- **Admin**: admin@test.com (şifre: password, rol: admin)
- **Yazar**: author@test.com (şifre: password, rol: author)
- **Kullanıcı**: user@test.com (şifre: password, rol: user)
 

## Kuyruk ve Görev Planlama

Görsellerin Spatie MediaLibrary ile dönüştürülmesi ve bildirimlerin gönderilmesi için kuyruğun çalıştırılması gerekmektedir. Ayrıca, 7 gün boyunca yorum almayan blog yazılarının otomatik olarak silinmesi için görev planlamanın yapılandırılması gerekir.

### Kuyruğu Çalıştırma

Kuyruk, görsel dönüşümleri ve bildirimlerin işlenmesi için gereklidir. Kuyruk işçisini başlatmak için şu komutu kullanın:

```bash
php artisan queue:work
```

Bu komutun arka planda çalışmasını sağlamak veya Supervisor gibi bir işlem yöneticisi kullanmak iyi bir fikir olabilir.

### Görev Planlamayı Yapılandırma

7 gün boyunca yorum almayan yazıların otomatik olarak silinmesi için Laravel'in görev planlayıcısını kullanabilirsiniz. Bunu yapmak için aşağıdaki komutu kullanarak görev planlamayı başlatın:

```bash
php artisan schedule:run
```

Bu komutun her dakika çalışması gerektiğinden, görev planlamasını bir cron job olarak ayarlamak gerekir.

## Kütüphaneler ve Araçlar

### Backend
- [Spatie Laravel MediaLibrary](https://spatie.be/docs/laravel-medialibrary/v11/introduction): Dosya yükleme işlemleri için.
- [Spatie Laravel ActivityLog](https://spatie.be/docs/laravel-activitylog/v4/introduction): Model değişikliklerini takip etmek için.
- [Laravel Reverb](https://laravel.com/docs/12.x/reverb): Yayınlama işlemleri için.
- [Laravel Sanctum](https://laravel.com/docs/12.x/sanctum): API kimlik doğrulaması için.

### Frontend
- [Vue 3 Composition API](https://vuejs.org/): Frontend geliştirme için.
- [Tailwind CSS](https://tailwindcss.com/): Tasarım için.
- [Vue Router](https://router.vuejs.org/): Frontend rotalama için.
- [Vue Select](https://vue-select.org/): Arama yapılabilen dropdownlar için.
- [Vue The Mask](https://vuejs-tips.github.io/vue-the-mask/): Girdi maskeleri için (örneğin telefon numarası maskesi).

## Proje Yapısı

- `backend`: Backend mantığı ve API'lerini içerir.
- `frontend`: Vue.js ve TailwindCSS ile oluşturulmuş frontend dosyalarını içerir.
- `database/seeders`: Kullanıcılar, roller, kategoriler ve yazılar gibi başlangıç verilerini oluşturan seed dosyaları.

## Versiyon Kontrolü

Yapılan değişikliklerin düzenli olarak kaydedilmesi ve Git en iyi uygulamalarına uyulması gerekmektedir.


## Sonuç

Bu proje, Laravel backend ve Vue.js frontend kullanılarak basit bir blog yönetim sistemi olarak geliştirilmiştir. Amacımız, Laravel'in en iyi uygulamalarını takip etmek, kod kalitesini sağlamak ve kullanıcı dostu bir deneyim sunmaktır.

## Lisans

Bu proje MIT Lisansı ile lisanslanmıştır.


