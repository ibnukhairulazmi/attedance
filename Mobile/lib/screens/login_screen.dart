// lib/screens/login_screen.dart

import 'package:flutter/material.dart';
import '../config/theme.dart';
import '../services/auth_service.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  // BUG: sebelumnya _nisController → sistem pakai NIM bukan NIS
  final _nimController = TextEditingController();
  final _pwdController = TextEditingController();
  final _authService = AuthService();

  bool _loading = false;
  bool _obscurePwd = true;
  String? _errorMsg;

  @override
  void dispose() {
    _nimController.dispose();
    _pwdController.dispose();
    super.dispose();
  }

  Future<void> _login() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() {
      _loading = true;
      _errorMsg = null;
    });

    final result = await _authService.login(
      // BUG: sebelumnya nisOrEmail → nimOrEmail
      nimOrEmail: _nimController.text.trim(),
      password: _pwdController.text,
    );

    if (!mounted) return;

    if (result.success) {
      Navigator.pushReplacementNamed(context, '/home');
    } else {
      setState(() {
        _loading = false;
        _errorMsg = result.message;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: [AppColors.tealDark, AppColors.teal, AppColors.bg],
            stops: [0.0, 0.4, 1.0],
          ),
        ),
        child: SafeArea(
          child: Column(
            children: [
              // Header
              Padding(
                padding: const EdgeInsets.fromLTRB(24, 40, 24, 32),
                child: Column(
                  children: [
                    Container(
                      width: 68,
                      height: 68,
                      decoration: BoxDecoration(
                        color: Colors.white.withOpacity(0.15),
                        borderRadius: BorderRadius.circular(20),
                        border:
                            Border.all(color: Colors.white.withOpacity(0.2)),
                      ),
                      child: const Icon(Icons.qr_code_scanner,
                          size: 38, color: Colors.white),
                    ),
                    const SizedBox(height: 14),
                    const Text('PresensiApp',
                        style: TextStyle(
                            color: Colors.white,
                            fontSize: 24,
                            fontWeight: FontWeight.w800,
                            letterSpacing: -0.5)),
                    const SizedBox(height: 4),
                    Text('Sistem Presensi Digital',
                        style: TextStyle(
                            color: Colors.white.withOpacity(0.7),
                            fontSize: 13)),
                  ],
                ),
              ),

              // Card Form
              Expanded(
                child: Container(
                  width: double.infinity,
                  decoration: const BoxDecoration(
                    color: AppColors.white,
                    borderRadius:
                        BorderRadius.vertical(top: Radius.circular(28)),
                  ),
                  child: SingleChildScrollView(
                    padding: const EdgeInsets.all(28),
                    child: Form(
                      key: _formKey,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text('Masuk ke Akun',
                              style: TextStyle(
                                  fontSize: 20,
                                  fontWeight: FontWeight.w800,
                                  color: AppColors.txt)),
                          const SizedBox(height: 4),
                          // BUG: sebelumnya 'NIS atau email' → 'NIM atau email'
                          const Text('Gunakan NIM atau email kampus',
                              style: TextStyle(
                                  fontSize: 13, color: AppColors.txtGray)),
                          const SizedBox(height: 28),

                          // Error Box
                          if (_errorMsg != null) ...[
                            Container(
                              padding: const EdgeInsets.all(12),
                              decoration: BoxDecoration(
                                color: AppColors.redBg,
                                borderRadius: BorderRadius.circular(10),
                                border: Border.all(
                                    color: AppColors.red.withOpacity(0.3)),
                              ),
                              child: Row(
                                children: [
                                  const Icon(Icons.error_outline,
                                      color: AppColors.red, size: 18),
                                  const SizedBox(width: 8),
                                  Expanded(
                                      child: Text(_errorMsg!,
                                          style: const TextStyle(
                                              color: AppColors.red,
                                              fontSize: 13))),
                                ],
                              ),
                            ),
                            const SizedBox(height: 20),
                          ],

                          // NIM / Email
                          // BUG: sebelumnya label 'NIS atau Email'
                          _buildLabel('NIM atau Email'),
                          const SizedBox(height: 6),
                          TextFormField(
                            controller: _nimController,
                            keyboardType: TextInputType.emailAddress,
                            decoration: _inputDecoration(
                                // BUG: placeholder 'Masukkan NIS atau email'
                                'Masukkan NIM atau email',
                                Icons.person_outline_rounded),
                            validator: (v) =>
                                v == null || v.isEmpty ? 'Wajib diisi' : null,
                          ),
                          const SizedBox(height: 18),

                          // Password
                          _buildLabel('Password'),
                          const SizedBox(height: 6),
                          TextFormField(
                            controller: _pwdController,
                            obscureText: _obscurePwd,
                            decoration: _inputDecoration(
                              'Masukkan password',
                              Icons.lock_outline_rounded,
                            ).copyWith(
                              suffixIcon: IconButton(
                                icon: Icon(
                                  _obscurePwd
                                      ? Icons.visibility_off_outlined
                                      : Icons.visibility_outlined,
                                  color: AppColors.txtLight,
                                  size: 20,
                                ),
                                onPressed: () =>
                                    setState(() => _obscurePwd = !_obscurePwd),
                              ),
                            ),
                            validator: (v) =>
                                v == null || v.isEmpty ? 'Wajib diisi' : null,
                          ),
                          const SizedBox(height: 32),

                          // Tombol Login
                          SizedBox(
                            width: double.infinity,
                            height: 52,
                            child: ElevatedButton(
                              onPressed: _loading ? null : _login,
                              style: ElevatedButton.styleFrom(
                                backgroundColor: AppColors.teal,
                                foregroundColor: Colors.white,
                                disabledBackgroundColor: AppColors.tealLight,
                                shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(14)),
                                elevation: 0,
                              ),
                              child: _loading
                                  ? const SizedBox(
                                      width: 22,
                                      height: 22,
                                      child: CircularProgressIndicator(
                                          color: Colors.white,
                                          strokeWidth: 2.5))
                                  : const Text('Masuk',
                                      style: TextStyle(
                                          fontSize: 16,
                                          fontWeight: FontWeight.w700)),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildLabel(String text) {
    return Text(text,
        style: const TextStyle(
            fontSize: 12,
            fontWeight: FontWeight.w700,
            color: AppColors.txtGray,
            letterSpacing: 0.2));
  }

  InputDecoration _inputDecoration(String hint, IconData icon) {
    return InputDecoration(
      hintText: hint,
      hintStyle: const TextStyle(color: AppColors.txtLight, fontSize: 14),
      prefixIcon: Icon(icon, color: AppColors.txtLight, size: 20),
      filled: true,
      fillColor: AppColors.surface,
      border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: AppColors.border)),
      enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: AppColors.border)),
      focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: AppColors.teal, width: 2)),
      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
    );
  }
}
