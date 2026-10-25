// lib/services/attendance_service.dart

import '../models/student_model.dart';
import 'api_service.dart';
import 'device_service.dart';
import 'location_service.dart';

class ScanResult {
  final bool success;
  final String message;
  final Map<String, dynamic>? data;

  const ScanResult({
    required this.success,
    required this.message,
    this.data,
  });
}

class AttendanceService {
  final _apiService = ApiService();

  Future<ScanResult> processQrScan(String qrToken) async {
    // PERBAIKAN: sebelumnya memakai LocationService.getCurrentLocation(),
    // metode generik yang bisa menerima posisi cache hingga umur 60 detik
    // dan akurasi sampai 50m. LocationService.getLocationForAttendance()
    // sudah dibuat khusus untuk presensi — retry sampai 5x demi akurasi
    // ≤10m, sesuai batas radius presensi (10m) dan validasi
    // min_gps_accuracy yang sekarang ditegakkan di server.
    final location = await LocationService.getLocationForAttendance();
    if (!location.success) {
      return ScanResult(success: false, message: location.message!);
    }

    final deviceId = await DeviceService.getDeviceId();

    final result = await _apiService.post('/attendance/scan', body: {
      'qr_token': qrToken,
      'latitude': location.latitude,
      'longitude': location.longitude,
      'accuracy': location.accuracy,
      'device_id': deviceId,
    });

    return ScanResult(
      success: result['success'] == true,
      message: result['message'] as String? ?? 'Terjadi kesalahan.',
      data: result['data'] as Map<String, dynamic>?,
    );
  }

  Future<List<AttendanceRecord>> getHistory({int page = 1}) async {
    final result = await _apiService.get(
      '/attendance/history',
      query: {'page': page},
    );
    if (result['success'] == true) {
      final list = result['data'] as List<dynamic>;
      return list
          .map((e) => AttendanceRecord.fromJson(e as Map<String, dynamic>))
          .toList();
    }
    return [];
  }

  Future<List<AttendanceRecord>> getTodayAttendance() async {
    final result = await _apiService.get('/attendance/today');
    if (result['success'] == true) {
      final list = result['data'] as List<dynamic>;
      return list
          .map((e) => AttendanceRecord.fromJson(e as Map<String, dynamic>))
          .toList();
    }
    return [];
  }
}
