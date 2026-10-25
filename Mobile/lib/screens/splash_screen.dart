// lib/screens/splash_screen.dart

import 'package:flutter/material.dart';
import '../config/theme.dart';
import '../services/auth_service.dart';
import '../services/api_service.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen>
    with SingleTickerProviderStateMixin {
  late AnimationController _ctrl;
  late Animation<double> _fade;
  late Animation<double> _slide;

  @override
  void initState() {
    super.initState();
    _ctrl = AnimationController(
        vsync: this, duration: const Duration(milliseconds: 800));
    _fade = CurvedAnimation(parent: _ctrl, curve: Curves.easeOut);
    _slide = Tween<double>(begin: 30, end: 0)
        .animate(CurvedAnimation(parent: _ctrl, curve: Curves.easeOut));
    _ctrl.forward();
    _checkAuth();
  }

  @override
  void dispose() {
    _ctrl.dispose();
    super.dispose();
  }

  Future<void> _checkAuth() async {
    // Tampilkan splash minimal 1.5 detik
    await Future.delayed(const Duration(milliseconds: 1500));

    final authService = AuthService();
    final token = await authService.getToken();

    if (!mounted) return;

    // Tidak ada token → langsung ke login
    if (token == null) {
      Navigator.pushReplacementNamed(context, '/login');
      return;
    }

    // Ada token → verifikasi ke server apakah masih valid
    ApiService.setToken(token);
    final profile = await authService.fetchProfile();

    if (!mounted) return;

    if (profile != null) {
      // Token valid → ke dashboard
      Navigator.pushReplacementNamed(context, '/home');
    } else {
      // Token tidak valid / expired → hapus dan ke login
      await authService.logout();
      Navigator.pushReplacementNamed(context, '/login');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [AppColors.tealDark, AppColors.teal],
          ),
        ),
        child: Center(
          child: FadeTransition(
            opacity: _fade,
            child: AnimatedBuilder(
              animation: _slide,
              builder: (_, child) => Transform.translate(
                offset: Offset(0, _slide.value),
                child: child,
              ),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Container(
                    width: 100,
                    height: 100,
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.15),
                      borderRadius: BorderRadius.circular(28),
                      border: Border.all(
                          color: Colors.white.withOpacity(0.2), width: 1.5),
                    ),
                    child: const Icon(Icons.qr_code_scanner,
                        size: 56, color: Colors.white),
                  ),
                  const SizedBox(height: 28),
                  const Text(
                    'PresensiApp',
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 30,
                      fontWeight: FontWeight.w800,
                      letterSpacing: -0.5,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    'Sistem Presensi Digital',
                    style: TextStyle(
                        color: Colors.white.withOpacity(0.7), fontSize: 14),
                  ),
                  const SizedBox(height: 60),
                  SizedBox(
                    width: 28,
                    height: 28,
                    child: CircularProgressIndicator(
                      color: Colors.white.withOpacity(0.8),
                      strokeWidth: 2.5,
                    ),
                  ),
                  const SizedBox(height: 16),
                  Text(
                    'Memeriksa sesi...',
                    style: TextStyle(
                        color: Colors.white.withOpacity(0.5), fontSize: 12),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
