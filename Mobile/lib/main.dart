// lib/main.dart

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'config/theme.dart';
import 'screens/splash_screen.dart';
import 'screens/login_screen.dart';
import 'screens/home_screen.dart';
import 'screens/scanner_screen.dart';
import 'screens/history_screen.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  SystemChrome.setPreferredOrientations([
    DeviceOrientation.portraitUp,
    DeviceOrientation.portraitDown,
  ]);
  SystemChrome.setSystemUIOverlayStyle(const SystemUiOverlayStyle(
    statusBarColor: Colors.transparent,
    statusBarIconBrightness: Brightness.light,
  ));
  runApp(const PresensiApp());
}

class PresensiApp extends StatelessWidget {
  const PresensiApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'PresensiApp',
      debugShowCheckedModeBanner: false,
      theme: AppTheme.theme,
      initialRoute: '/splash',
      routes: {
        '/splash': (_) => const SplashScreen(),
        '/login': (_) => const LoginScreen(),
        '/home': (_) => const HomeScreen(),
        '/scanner': (_) => const ScannerScreen(),
        '/history': (_) => const HistoryScreen(),
      },
    );
  }
}
