// lib/services/auth_service.dart

import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../models/student_model.dart';
import 'api_service.dart';
import 'device_service.dart';

class AuthService {
  static const _tokenKey = 'student_api_token';

  final _storage = const FlutterSecureStorage();
  final _apiService = ApiService();

  Future<void> saveToken(String token) async {
    await _storage.write(key: _tokenKey, value: token);
    ApiService.setToken(token);
  }

  Future<String?> getToken() async {
    final token = await _storage.read(key: _tokenKey);
    if (token != null) ApiService.setToken(token);
    return token;
  }

  Future<bool> isLoggedIn() async {
    final token = await getToken();
    return token != null;
  }

  Future<({bool success, String message, StudentModel? student})> login({
    required String nimOrEmail,
    required String password,
  }) async {
    final deviceId = await DeviceService.getDeviceId();

    try {
      final result = await _apiService.post(
        '/auth/login',
        body: {
          'nim_or_email': nimOrEmail,
          'password': password,
          'device_id': deviceId,
        },
      );

      if (result['success'] == true) {
        final token = result['data']['token'] as String;

        final student = StudentModel.fromJson(
          result['data']['student'] as Map<String, dynamic>,
        );

        await saveToken(token);

        return (
          success: true,
          message: 'Login berhasil.',
          student: student,
        );
      }

      final errorMessage = result['message'] is String
          ? result['message'] as String
          : 'Login gagal.';

      return (
        success: false,
        message: errorMessage,
        student: null,
      );
    } on DioException catch (e) {
      final responseData = e.response?.data;
      final apiMessage = responseData is Map<String, dynamic> &&
              responseData['message'] is String
          ? responseData['message'] as String
          : 'Terjadi kesalahan';

      return (
        success: false,
        message: apiMessage,
        student: null,
      );
    }
  }

  Future<StudentModel?> fetchProfile() async {
    try {
      final result = await _apiService.get('/auth/me');
      print(result);
      if (result['success'] == true) {
        return StudentModel.fromJson(result['data'] as Map<String, dynamic>);
      }
      return null;
    } catch (_) {
      return null;
    }
  }

  Future<void> logout() async {
    await _storage.delete(key: _tokenKey);
    ApiService.clearToken();
  }
}
