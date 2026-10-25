import 'package:flutter/material.dart';

class AppColors {
  // Warna utama sesuai tema website
  static const Color teal = Color(0xFF088395);
  static const Color tealDark = Color(0xFF09637E);
  static const Color tealLight = Color(0xFF7AB2B2);
  static const Color navy = Color(0xFF111827);

  // Background
  static const Color bg = Color(0xFFEBF4F6);
  static const Color surface = Color(0xFFF2F9FB);
  static const Color surface2 = Color(0xFFD4EAF0);
  static const Color white = Color(0xFFFFFFFF);

  // Border
  static const Color border = Color(0xFFBCD9E2);

  // Text
  static const Color txt = Color(0xFF111827);
  static const Color txtMid = Color(0xFF374151);
  static const Color txtGray = Color(0xFF6B7280);
  static const Color txtLight = Color(0xFF9CA3AF);

  // Status
  static const Color green = Color(0xFF059669);
  static const Color greenBg = Color(0xFFD1FAE5);
  static const Color yellow = Color(0xFFD97706);
  static const Color yellowBg = Color(0xFFFEF3C7);
  static const Color red = Color(0xFFDC2626);
  static const Color redBg = Color(0xFFFEE2E2);
  static const Color blue = Color(0xFF2563EB);
  static const Color blueBg = Color(0xFFDBEAFE);
}

class AppTheme {
  static ThemeData get theme => ThemeData(
    useMaterial3: true,
    fontFamily: 'Roboto',
    colorScheme: ColorScheme.fromSeed(
      seedColor: AppColors.teal,
      primary: AppColors.teal,
      surface: AppColors.bg,
    ),
    scaffoldBackgroundColor: AppColors.bg,
    appBarTheme: const AppBarTheme(
      backgroundColor: AppColors.teal,
      foregroundColor: AppColors.white,
      elevation: 0,
      centerTitle: true,
      titleTextStyle: TextStyle(
        color: AppColors.white,
        fontSize: 17,
        fontWeight: FontWeight.w700,
      ),
    ),
    elevatedButtonTheme: ElevatedButtonThemeData(
      style: ElevatedButton.styleFrom(
        backgroundColor: AppColors.teal,
        foregroundColor: AppColors.white,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        padding: const EdgeInsets.symmetric(vertical: 14),
        textStyle: const TextStyle(fontSize: 15, fontWeight: FontWeight.w700),
      ),
    ),
    inputDecorationTheme: InputDecorationTheme(
      filled: true,
      fillColor: AppColors.surface,
      border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: AppColors.border),
      ),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: AppColors.border),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: AppColors.teal, width: 2),
      ),
      labelStyle: const TextStyle(color: AppColors.txtGray),
    ),
    bottomNavigationBarTheme: const BottomNavigationBarThemeData(
      backgroundColor: AppColors.white,
      selectedItemColor: AppColors.teal,
      unselectedItemColor: AppColors.txtLight,
      elevation: 8,
      type: BottomNavigationBarType.fixed,
    ),
  );
}
