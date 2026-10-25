import 'dart:async';
import 'package:geolocator/geolocator.dart';

class LocationResult {
  final bool success;
  final String? message;
  final double? latitude;
  final double? longitude;
  final double? accuracy;

  const LocationResult({
    required this.success,
    this.message,
    this.latitude,
    this.longitude,
    this.accuracy,
  });
}

class LocationService {
  static const double _attendanceRequiredAccuracy = 10.0;
  static const int _attendanceMaxAttempts = 5;
  static const int _warmUpSeconds = 3;

  static Future<LocationResult> getLocationForAttendance() async {
    final permissionResult = await _checkPermission();
    if (permissionResult != null) return permissionResult;

    // PERBAIKAN: sebelumnya stream ini dibuat tapi tidak pernah di-listen,
    // sehingga GPS provider tidak benar-benar "dipanaskan" — delay 3 detik
    // di bawah cuma buang waktu tanpa manfaat. Sekarang benar-benar
    // subscribe (lalu cancel) supaya request lokasi berikutnya lebih cepat
    // mendapat fix akurat.
    final warmUpSub = Geolocator.getPositionStream(
      locationSettings: const LocationSettings(
        accuracy: LocationAccuracy.best,
        distanceFilter: 0,
      ),
    ).listen((_) {});

    await Future.delayed(const Duration(seconds: _warmUpSeconds));
    await warmUpSub.cancel();

    Position? bestPosition;

    for (int attempt = 1; attempt <= _attendanceMaxAttempts; attempt++) {
      try {
        final position = await Geolocator.getCurrentPosition(
          desiredAccuracy: LocationAccuracy.best,
          timeLimit: const Duration(seconds: 20),
        );

        if (bestPosition == null || position.accuracy < bestPosition.accuracy) {
          bestPosition = position;
        }

        if (bestPosition.accuracy <= _attendanceRequiredAccuracy) {
          return LocationResult(
            success: true,
            latitude: position.latitude,
            longitude: position.longitude,
            accuracy: position.accuracy,
          );
        }

        if (attempt < _attendanceMaxAttempts) {
          final delay = attempt <= 2 ? 4 : 2;
          await Future.delayed(Duration(seconds: delay));
          continue;
        }

        return LocationResult(
          success: true,
          latitude: bestPosition.latitude,
          longitude: bestPosition.longitude,
          accuracy: bestPosition.accuracy,
          message:
              'Sinyal GPS lemah (${bestPosition.accuracy.toStringAsFixed(0)}m). '
              'Hasil presensi mungkin kurang akurat.',
        );
      } on TimeoutException {
        if (attempt < _attendanceMaxAttempts) {
          await Future.delayed(const Duration(seconds: 2));
          continue;
        }

        if (bestPosition != null) {
          return LocationResult(
            success: true,
            latitude: bestPosition.latitude,
            longitude: bestPosition.longitude,
            accuracy: bestPosition.accuracy,
            message: 'GPS timeout, menggunakan posisi terakhir yang tersedia.',
          );
        }

        return const LocationResult(
          success: false,
          message: 'GPS timeout. Pastikan GPS aktif dan coba lagi.',
        );
      } catch (e) {
        return LocationResult(
          success: false,
          message: 'Gagal mendapatkan lokasi: $e',
        );
      }
    }

    return const LocationResult(
      success: false,
      message: 'Gagal mendapatkan lokasi yang akurat.',
    );
  }

  static Future<LocationResult> getCurrentLocation() async {
    final permissionResult = await _checkPermission();
    if (permissionResult != null) return permissionResult;

    try {
      final lastKnown = await Geolocator.getLastKnownPosition();
      if (lastKnown != null) {
        final age = DateTime.now().difference(lastKnown.timestamp);
        if (age.inSeconds < 60 && lastKnown.accuracy <= 50) {
          return LocationResult(
            success: true,
            latitude: lastKnown.latitude,
            longitude: lastKnown.longitude,
            accuracy: lastKnown.accuracy,
          );
        }
      }

      final position = await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.high,
        timeLimit: const Duration(seconds: 15),
      );

      return LocationResult(
        success: true,
        latitude: position.latitude,
        longitude: position.longitude,
        accuracy: position.accuracy,
      );
    } on TimeoutException {
      return const LocationResult(
        success: false,
        message: 'Timeout mendapatkan lokasi. Pastikan GPS aktif.',
      );
    } catch (e) {
      return LocationResult(
        success: false,
        message: 'Gagal mendapatkan lokasi: $e',
      );
    }
  }

  static Future<LocationResult?> _checkPermission() async {
    final serviceEnabled = await Geolocator.isLocationServiceEnabled();
    if (!serviceEnabled) {
      return const LocationResult(
        success: false,
        message: 'GPS tidak aktif. Aktifkan GPS di pengaturan perangkat.',
      );
    }

    LocationPermission permission = await Geolocator.checkPermission();
    if (permission == LocationPermission.denied) {
      permission = await Geolocator.requestPermission();
      if (permission == LocationPermission.denied) {
        return const LocationResult(
          success: false,
          message: 'Izin akses lokasi ditolak.',
        );
      }
    }

    if (permission == LocationPermission.deniedForever) {
      return const LocationResult(
        success: false,
        message:
            'Izin lokasi ditolak permanen. Buka Pengaturan untuk mengaktifkan.',
      );
    }

    return null;
  }
}
