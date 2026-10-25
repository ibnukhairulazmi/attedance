// lib/models/student_model.dart

import 'package:flutter/material.dart';

class StudentModel {
  final int id;
  final String nama;
  final String nim;
  final String email;
  final String kelas;
  final String angkatan;

  const StudentModel({
    required this.id,
    required this.nama,
    required this.nim,
    required this.email,
    required this.kelas,
    required this.angkatan,
  });

  factory StudentModel.fromJson(Map<String, dynamic> json) => StudentModel(
        id: json['id'] as int,
        nama: json['nama'] as String? ?? '',
        nim: json['nim'] as String? ?? '',
        email: json['email'] as String? ?? '',
        kelas: json['kelas'] as String? ?? '',
        angkatan: json['angkatan'] as String? ?? '',
      );
}

class AttendanceRecord {
  final int id;
  final String subject;
  final String date;
  final String status;
  final String? time;
  final double? distance; // 1. Pastikan tipenya double?

  const AttendanceRecord({
    required this.id,
    required this.subject,
    required this.date,
    required this.status,
    this.time,
    this.distance,
  });

  factory AttendanceRecord.fromJson(Map<String, dynamic> json) =>
      AttendanceRecord(
        id: json['id'] as int,
        subject: json['subject'] as String? ?? '-',
        date: json['date'] as String? ?? '-',
        status: json['status'] as String? ?? 'alpha',
        time: json['time'] as String?,
        distance: json['distance'] != null
            ? double.tryParse(json['distance'].toString())
            : null,
      );

  Color get statusColor {
    switch (status) {
      case 'hadir':
        return const Color(0xFF059669);
      case 'izin':
        return const Color(0xFF2563EB);
      case 'sakit':
        return const Color(0xFFD97706);
      default:
        return const Color(0xFFDC2626);
    }
  }

  String get statusLabel {
    switch (status) {
      case 'hadir':
        return 'Hadir';
      case 'izin':
        return 'Izin';
      case 'sakit':
        return 'Sakit';
      default:
        return 'Alpha';
    }
  }
}
