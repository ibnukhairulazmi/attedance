// lib/config/api_config.dart

/// ⚠️ PERHATIAN SEBELUM RILIS:
/// baseUrl di bawah sebelumnya di-hardcode ke URL tunnel ngrok gratis
/// (https://makeover-parchment-helpful.ngrok-free.dev). URL ngrok gratis
/// bersifat sementara — akan BERUBAH atau MATI setiap kali tunnel di
/// laptop developer di-restart. Jika APK/IPA yang memakai URL ini
/// didistribusikan ke mahasiswa, aplikasi akan berhenti berfungsi total
/// begitu tunnel tersebut ditutup.
///
/// Gunakan domain production yang stabil (mis. https://presensi.kampus.ac.id/api/v1)
/// sebelum build untuk distribusi/rilis. Selama development, override lewat
/// --dart-define agar tidak perlu edit kode setiap ganti tunnel:
///
///   flutter run --dart-define=API_BASE_URL=https://xxxx.ngrok-free.dev/api/v1
///
class ApiConfig {
  static const String baseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: 'https://makeover-parchment-helpful.ngrok-free.dev/api/v1',
  );
  static const Duration connectTimeout = Duration(seconds: 15);
  static const Duration receiveTimeout = Duration(seconds: 15);
}
