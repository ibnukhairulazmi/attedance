// lib/screens/home_screen.dart

import 'package:flutter/material.dart';
import '../config/theme.dart';
import '../models/student_model.dart';
import '../services/auth_service.dart';
import '../services/attendance_service.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  final _authService = AuthService();
  final _attendanceService = AttendanceService();

  StudentModel? _student;
  List<AttendanceRecord> _todayList = [];
  bool _loading = true;
  String? _errorMsg;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    setState(() {
      _loading = true;
      _errorMsg = null;
    });
    try {
      final student = await _authService.fetchProfile();

      if (student == null) {
        await _authService.logout();
        if (mounted) {
          Navigator.pushNamedAndRemoveUntil(
              context, '/login', (route) => false);
        }
        return;
      }

      final today = await _attendanceService.getTodayAttendance();
      if (mounted) {
        setState(() {
          _student = student;
          _todayList = today;
          _loading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _loading = false;
          _errorMsg = 'Gagal memuat data. Tarik ke bawah untuk coba lagi.';
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      body: _loading
          ? const Center(
              child: CircularProgressIndicator(color: AppColors.teal))
          : RefreshIndicator(
              color: AppColors.teal,
              onRefresh: _loadData,
              child: CustomScrollView(
                slivers: [
                  _buildAppBar(),
                  SliverToBoxAdapter(
                    child: Padding(
                      padding: const EdgeInsets.all(16),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          if (_errorMsg != null) _buildErrorBanner(),
                          _buildScanButton(),
                          const SizedBox(height: 24),
                          _buildSectionTitle('Presensi Hari Ini'),
                          const SizedBox(height: 12),
                          if (_todayList.isEmpty)
                            _buildEmpty()
                          else
                            ..._todayList.map(_buildCard),
                          const SizedBox(height: 24),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: 0,
        onTap: (i) {
          if (i == 1) {
            Navigator.pushNamed(context, '/history').then((_) => _loadData());
          }
        },
        selectedItemColor: AppColors.teal,
        unselectedItemColor: AppColors.txtLight,
        backgroundColor: AppColors.white,
        elevation: 8,
        items: const [
          BottomNavigationBarItem(
              icon: Icon(Icons.home_rounded), label: 'Beranda'),
          BottomNavigationBarItem(
              icon: Icon(Icons.history_rounded), label: 'Riwayat'),
        ],
      ),
    );
  }

  SliverAppBar _buildAppBar() {
    final firstName = _student?.nama ?? 'Mahasiswa';

    return SliverAppBar(
      expandedHeight: 160,
      floating: false,
      pinned: true,
      automaticallyImplyLeading: false,
      backgroundColor: AppColors.teal,
      flexibleSpace: FlexibleSpaceBar(
        background: Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [AppColors.tealDark, AppColors.teal],
            ),
          ),
          padding: const EdgeInsets.fromLTRB(20, 80, 20, 20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisAlignment: MainAxisAlignment.end,
            children: [
              Row(
                children: [
                  Container(
                    width: 44,
                    height: 44,
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.15),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: const Icon(Icons.person_rounded,
                        color: Colors.white, size: 24),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(firstName,
                            style: const TextStyle(
                                color: Colors.white,
                                fontSize: 18,
                                fontWeight: FontWeight.w800)),
                        const SizedBox(height: 2),
                        Text(
                            '${_student?.kelas ?? ''} • ${_student?.nim ?? ''}',
                            style: TextStyle(
                                color: Colors.white.withOpacity(0.75),
                                fontSize: 13)),
                      ],
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildErrorBanner() {
    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: AppColors.redBg,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppColors.red.withOpacity(0.3)),
      ),
      child: Row(
        children: [
          const Icon(Icons.error_outline, color: AppColors.red, size: 18),
          const SizedBox(width: 8),
          Expanded(
              child: Text(_errorMsg!,
                  style: const TextStyle(color: AppColors.red, fontSize: 13))),
        ],
      ),
    );
  }

  Widget _buildScanButton() {
    return GestureDetector(
      onTap: () async {
        await Navigator.pushNamed(context, '/scanner');
        _loadData();
      },
      child: Container(
        width: double.infinity,
        padding: const EdgeInsets.symmetric(vertical: 22),
        decoration: BoxDecoration(
          gradient: const LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [AppColors.tealDark, AppColors.teal],
          ),
          borderRadius: BorderRadius.circular(18),
          boxShadow: [
            BoxShadow(
                color: AppColors.teal.withOpacity(0.35),
                blurRadius: 16,
                offset: const Offset(0, 8)),
          ],
        ),
        child: Column(
          children: [
            Container(
              width: 60,
              height: 60,
              decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.15),
                  borderRadius: BorderRadius.circular(16)),
              child: const Icon(Icons.qr_code_scanner_rounded,
                  size: 34, color: Colors.white),
            ),
            const SizedBox(height: 12),
            const Text('Scan QR Code Presensi',
                style: TextStyle(
                    color: Colors.white,
                    fontSize: 16,
                    fontWeight: FontWeight.w700)),
            const SizedBox(height: 4),
            Text('Pastikan GPS aktif sebelum scan',
                style: TextStyle(
                    color: Colors.white.withOpacity(0.7), fontSize: 12)),
          ],
        ),
      ),
    );
  }

  Widget _buildSectionTitle(String title) {
    return Text(title,
        style: const TextStyle(
            fontSize: 16, fontWeight: FontWeight.w700, color: AppColors.txt));
  }

  Widget _buildEmpty() {
    return Container(
      padding: const EdgeInsets.all(28),
      decoration: BoxDecoration(
          color: AppColors.white,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: AppColors.border)),
      child: const Center(
        child: Column(
          children: [
            Icon(Icons.event_available_rounded,
                size: 44, color: AppColors.tealLight),
            SizedBox(height: 10),
            Text('Belum ada presensi hari ini',
                style: TextStyle(color: AppColors.txtGray, fontSize: 14)),
          ],
        ),
      ),
    );
  }

  Widget _buildCard(AttendanceRecord att) {
    final color = att.statusColor;
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
          color: AppColors.white,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(color: AppColors.border)),
      child: Row(
        children: [
          Container(
            width: 44,
            height: 44,
            decoration: BoxDecoration(
                color: color.withOpacity(0.1),
                borderRadius: BorderRadius.circular(12)),
            child: Icon(Icons.book_rounded, color: color, size: 22),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(att.subject,
                    style: const TextStyle(
                        fontWeight: FontWeight.w600,
                        fontSize: 14,
                        color: AppColors.txt)),
                if (att.time != null)
                  Text(
                    '${att.time} • ${att.distance != null ? att.distance!.toStringAsFixed(0) : '-'}m dari kelas',
                    style: const TextStyle(
                      color: AppColors.txtGray,
                      fontSize: 12,
                    ),
                  ),
              ],
            ),
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
            decoration: BoxDecoration(
                color: color.withOpacity(0.1),
                borderRadius: BorderRadius.circular(20)),
            child: Text(att.statusLabel,
                style: TextStyle(
                    color: color, fontSize: 11, fontWeight: FontWeight.w700)),
          ),
        ],
      ),
    );
  }
}
