// lib/services/api_service.dart

import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart' show kDebugMode, debugPrint;
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../config/api_config.dart';

class ApiService {
  static const _tokenKey = 'student_api_token';
  static const _storage = FlutterSecureStorage();

  static final Dio _dio = Dio(
    BaseOptions(
      baseUrl: ApiConfig.baseUrl,
      connectTimeout: ApiConfig.connectTimeout,
      receiveTimeout: ApiConfig.receiveTimeout,
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      validateStatus: (status) => status != null && status < 500,
    ),
  )..interceptors.add(
      InterceptorsWrapper(onRequest: (options, handler) async {
        final token = await _storage.read(key: _tokenKey);
        if (token != null) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        // PERBAIKAN: sebelumnya pakai print() tanpa syarat — ini mengirim
        // method, URL, status token, dan (di method post/_handleError) isi
        // lengkap request/response ke log perangkat pada build production
        // sekalipun. Log seperti ini bisa terbaca lewat `adb logcat` di HP
        // yang di-debug/root, atau ikut tertangkap tool crash-reporting.
        // Sekarang dibatasi hanya saat kDebugMode true (otomatis false di
        // build release/profile).
        if (kDebugMode) {
          debugPrint('🚀 ${options.method} ${options.uri}');
          debugPrint('🔑 Token: ${token != null ? "ada" : "TIDAK ADA"}');
        }
        handler.next(options);
      }, onResponse: (response, handler) {
        if (kDebugMode) {
          debugPrint('✅ ${response.statusCode} ${response.requestOptions.uri}');
        }
        handler.next(response);
      }, onError: (error, handler) {
        if (kDebugMode) {
          debugPrint('========================');
          debugPrint('STATUS : ${error.response?.statusCode}');
          debugPrint('BODY   : ${error.response?.data}');
          debugPrint('MESSAGE: ${error.message}');
          debugPrint('========================');
        }
        handler.next(error);
      }),
    );

  static void setToken(String token) {
    _dio.options.headers['Authorization'] = 'Bearer $token';
  }

  static void clearToken() {
    _dio.options.headers.remove('Authorization');
  }

  Future<Map<String, dynamic>> get(
    String path, {
    Map<String, dynamic>? query,
  }) async {
    try {
      final response = await _dio.get(path, queryParameters: query);
      return response.data as Map<String, dynamic>;
    } on DioException catch (e) {
      return _handleError(e);
    } catch (e) {
      return {'success': false, 'message': 'Terjadi kesalahan: $e'};
    }
  }

  Future<Map<String, dynamic>> post(
    String path, {
    required Map<String, dynamic> body,
  }) async {
    try {
      final response = await _dio.post(path, data: body);
      if (kDebugMode) {
        debugPrint('📥 Raw response: ${response.data}');
        debugPrint('📥 Status: ${response.statusCode}');
      }
      return response.data as Map<String, dynamic>;
    } on DioException catch (e) {
      return _handleError(e);
    }
  }

  Future<Map<String, dynamic>> put(
    String path, {
    required Map<String, dynamic> body,
  }) async {
    try {
      final response = await _dio.put(path, data: body);
      return response.data as Map<String, dynamic>;
    } on DioException catch (e) {
      return _handleError(e);
    } catch (e) {
      return {'success': false, 'message': 'Terjadi kesalahan: $e'};
    }
  }

  Map<String, dynamic> _handleError(DioException e) {
    if (kDebugMode) {
      debugPrint('========================');
      debugPrint('STATUS : ${e.response?.statusCode}');
      debugPrint('BODY   : ${e.response?.data}');
      debugPrint('========================');
    }

    if (e.response?.data is Map) {
      return Map<String, dynamic>.from(e.response!.data);
    }

    // Fallback jika message dari Dio null (mis. error jaringan tanpa response).
    return {
      'success': false,
      'message': e.message ?? 'Terjadi kesalahan jaringan.',
    };
  }
}
