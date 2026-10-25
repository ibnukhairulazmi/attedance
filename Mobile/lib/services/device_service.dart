// lib/services/device_service.dart

import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:uuid/uuid.dart';

/// PERBAIKAN PENTING:
/// Implementasi sebelumnya memakai `androidInfo.id` sebagai device_id.
/// Field itu sebenarnya adalah `Build.ID` (fingerprint versi OS/ROM),
/// BUKAN identifier unik per unit perangkat. Akibatnya banyak HP dengan
/// model & versi firmware yang sama akan mengirim device_id yang SAMA
/// PERSIS ke server, sehingga mekanisme "satu akun hanya boleh satu
/// device" di backend tidak benar-benar mencegah akun dipakai di HP lain
/// yang kebetulan sama modelnya.
///
/// Solusi: generate UUID v4 sekali saat aplikasi pertama kali dijalankan,
/// simpan permanen di secure storage (Keychain di iOS, EncryptedSharedPreferences
/// di Android). UUID ini unik per instalasi aplikasi dan tidak bergantung pada
/// identifier hardware yang semakin dibatasi Android/iOS untuk alasan privasi.
///
/// Catatan: UUID ini akan berubah jika aplikasi di-uninstall lalu diinstall
/// ulang. Ini perilaku yang wajar/diharapkan untuk kasus presensi — device
/// baru (secara efektif) tetap harus melalui proses reset device oleh
/// administrator, sama seperti sebelumnya saat ganti HP fisik.
class DeviceService {
  static const _deviceIdKey = 'device_install_id';
  static const _storage = FlutterSecureStorage();
  static const _uuid = Uuid();

  static Future<String> getDeviceId() async {
    final existing = await _storage.read(key: _deviceIdKey);
    if (existing != null && existing.isNotEmpty) {
      return existing;
    }

    final newId = _uuid.v4();
    await _storage.write(key: _deviceIdKey, value: newId);
    return newId;
  }
}
