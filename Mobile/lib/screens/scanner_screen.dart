// lib/screens/scanner_screen.dart

import 'package:flutter/material.dart';
import 'package:mobile_scanner/mobile_scanner.dart';
import '../config/theme.dart';
import '../services/attendance_service.dart';

class ScannerScreen extends StatefulWidget {
  const ScannerScreen({super.key});

  @override
  State<ScannerScreen> createState() => _ScannerScreenState();
}

class _ScannerScreenState extends State<ScannerScreen>
    with SingleTickerProviderStateMixin {
  final MobileScannerController _camera = MobileScannerController();
  final AttendanceService _service = AttendanceService();

  bool _isProcessing = false;
  bool _hasScanned = false;

  // Animasi garis scan
  late AnimationController _scanCtrl;
  late Animation<double> _scanAnim;

  @override
  void initState() {
    super.initState();
    _scanCtrl =
        AnimationController(vsync: this, duration: const Duration(seconds: 2))
          ..repeat(reverse: true);
    _scanAnim = Tween<double>(begin: 0, end: 1).animate(_scanCtrl);
  }

  @override
  void dispose() {
    _camera.dispose();
    _scanCtrl.dispose();
    super.dispose();
  }

  // Tambahkan validasi dan penanganan error pada proses scan QR.

  Future<void> _onDetect(BarcodeCapture capture) async {
    if (_isProcessing || _hasScanned) return;

    final barcode = capture.barcodes.firstOrNull;
    if (barcode?.rawValue == null) return;

    final rawValue = barcode!.rawValue!;

    String? qrToken;
    try {
      final uri = Uri.parse(rawValue);
      qrToken = uri.queryParameters['token'];
    } catch (_) {
      qrToken = rawValue;
    }

    if (qrToken == null || qrToken.isEmpty) {
      _showResultDialog(false, 'QR Code tidak valid.', null);
      return;
    }

    debugPrint('QR Token: $qrToken');

    setState(() {
      _isProcessing = true;
      _hasScanned = true;
    });

    await _camera.stop();
    _showProcessingDialog();

    try {
      final result = await _service.processQrScan(qrToken);
      if (!mounted) return;

      Navigator.pop(context);
      await _showResultDialog(result.success, result.message, result.data);
    } catch (e) {
      if (!mounted) return;

      Navigator.pop(context);
      await _showResultDialog(
        false,
        'Terjadi kesalahan saat memproses presensi.\n$e',
        null,
      );
    } finally {
      if (mounted) {
        setState(() {
          _isProcessing = false;
        });
      }
    }

    if (mounted) {
      Navigator.pop(context);
    }
  }

  void _showProcessingDialog() {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (_) => Dialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        child: const Padding(
          padding: EdgeInsets.all(28),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              CircularProgressIndicator(color: AppColors.teal),
              SizedBox(height: 20),
              Text('Memverifikasi...',
                  style: TextStyle(
                      fontWeight: FontWeight.w700,
                      fontSize: 15,
                      color: AppColors.txt)),
              SizedBox(height: 6),
              Text('Mengecek lokasi dan QR Code',
                  style: TextStyle(color: AppColors.txtGray, fontSize: 13)),
            ],
          ),
        ),
      ),
    );
  }

  Future<void> _showResultDialog(
      bool success, String message, Map<String, dynamic>? data) async {
    await showDialog(
      context: context,
      barrierDismissible: false,
      builder: (_) => Dialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
        child: Padding(
          padding: const EdgeInsets.all(28),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              // Icon hasil
              Container(
                width: 80,
                height: 80,
                decoration: BoxDecoration(
                  color: success ? AppColors.greenBg : AppColors.redBg,
                  shape: BoxShape.circle,
                ),
                child: Icon(
                  success ? Icons.check_circle_rounded : Icons.cancel_rounded,
                  size: 46,
                  color: success ? AppColors.green : AppColors.red,
                ),
              ),
              const SizedBox(height: 18),
              Text(success ? 'Presensi Berhasil!' : 'Presensi Gagal',
                  style: const TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.w800,
                      color: AppColors.txt)),
              const SizedBox(height: 8),
              Text(message,
                  textAlign: TextAlign.center,
                  style: const TextStyle(
                      color: AppColors.txtGray, fontSize: 13, height: 1.5)),

              // Detail jika sukses
              if (success && data != null) ...[
                const SizedBox(height: 18),
                Container(
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    color: AppColors.surface,
                    borderRadius: BorderRadius.circular(14),
                    border: Border.all(color: AppColors.border),
                  ),
                  child: Column(
                    children: [
                      _detailRow(Icons.book_rounded, 'Mata Pelajaran',
                          data['subject']),
                      _detailRow(Icons.people_rounded, 'Kelas', data['class']),
                      _detailRow(Icons.access_time_rounded, 'Waktu',
                          data['scan_time']),
                      _detailRow(Icons.location_on_rounded, 'Jarak',
                          '${data['distance_meters']}m dari sekolah'),
                    ],
                  ),
                ),
              ],

              const SizedBox(height: 24),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: () => Navigator.pop(context),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: success ? AppColors.teal : AppColors.red,
                    foregroundColor: Colors.white,
                    shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12)),
                    padding: const EdgeInsets.symmetric(vertical: 14),
                  ),
                  child: const Text('OK',
                      style:
                          TextStyle(fontWeight: FontWeight.w700, fontSize: 15)),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _detailRow(IconData icon, String label, String? value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 5),
      child: Row(
        children: [
          Icon(icon, size: 15, color: AppColors.teal),
          const SizedBox(width: 8),
          Text(label,
              style: const TextStyle(fontSize: 12, color: AppColors.txtGray)),
          const Spacer(),
          Text(value ?? '-',
              style: const TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.w700,
                  color: AppColors.txt)),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.black,
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        title: const Text('Scan QR Code'),
        backgroundColor: Colors.transparent,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: Stack(
        children: [
          // Kamera
          MobileScanner(controller: _camera, onDetect: _onDetect),

          // Overlay gelap dengan lubang tengah
          _buildOverlay(),

          // Animasi garis scan
          if (!_isProcessing) _buildScanLine(),

          // Info bawah
          _buildBottomInfo(),

          // Loading overlay
          if (_isProcessing)
            Container(
              color: Colors.black54,
              child: const Center(
                  child: CircularProgressIndicator(color: Colors.white)),
            ),
        ],
      ),
    );
  }

  Widget _buildOverlay() {
    return CustomPaint(
      painter: _ScanOverlayPainter(),
      child: Container(),
    );
  }

  Widget _buildScanLine() {
    return Center(
      child: SizedBox(
        width: 260,
        height: 260,
        child: AnimatedBuilder(
          animation: _scanAnim,
          builder: (_, __) => Stack(
            children: [
              // Sudut-sudut kotak
              ..._buildCorners(),
              // Garis scan bergerak
              Positioned(
                top: _scanAnim.value * 230 + 10,
                left: 10,
                right: 10,
                child: Container(
                  height: 2,
                  decoration: BoxDecoration(
                    gradient: LinearGradient(
                      colors: [
                        Colors.transparent,
                        AppColors.teal.withOpacity(0.8),
                        Colors.transparent,
                      ],
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

  List<Widget> _buildCorners() {
    const size = 24.0;
    const thick = 3.0;
    const radius = 6.0;
    const color = AppColors.teal;

    return [
      // Top-left
      Positioned(
          top: 0,
          left: 0,
          child: _corner(size, thick, radius, color, true, true)),
      // Top-right
      Positioned(
          top: 0,
          right: 0,
          child: _corner(size, thick, radius, color, true, false)),
      // Bottom-left
      Positioned(
          bottom: 0,
          left: 0,
          child: _corner(size, thick, radius, color, false, true)),
      // Bottom-right
      Positioned(
          bottom: 0,
          right: 0,
          child: _corner(size, thick, radius, color, false, false)),
    ];
  }

  Widget _corner(double size, double thick, double radius, Color color,
      bool top, bool left) {
    return SizedBox(
      width: size,
      height: size,
      child: CustomPaint(
        painter: _CornerPainter(thick, radius, color, top, left),
      ),
    );
  }

  Widget _buildBottomInfo() {
    return Positioned(
      bottom: 0,
      left: 0,
      right: 0,
      child: Container(
        padding: const EdgeInsets.fromLTRB(24, 32, 24, 40),
        decoration: BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.bottomCenter,
            end: Alignment.topCenter,
            colors: [Colors.black.withOpacity(0.85), Colors.transparent],
          ),
        ),
        child: Column(
          children: [
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
              decoration: BoxDecoration(
                color: AppColors.teal.withOpacity(0.2),
                borderRadius: BorderRadius.circular(20),
                border: Border.all(color: AppColors.teal.withOpacity(0.4)),
              ),
              child: const Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Icon(Icons.location_on_rounded,
                      color: AppColors.tealLight, size: 16),
                  SizedBox(width: 6),
                  Text('GPS diambil otomatis saat scan',
                      style:
                          TextStyle(color: AppColors.tealLight, fontSize: 13)),
                ],
              ),
            ),
            const SizedBox(height: 10),
            const Text(
              'Arahkan kamera ke QR Code yang ditampilkan guru',
              style: TextStyle(color: Colors.white60, fontSize: 12),
              textAlign: TextAlign.center,
            ),
          ],
        ),
      ),
    );
  }
}

// Custom painter untuk overlay gelap dengan lubang kotak di tengah
class _ScanOverlayPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    const boxSize = 260.0;
    final cx = size.width / 2;
    final cy = size.height / 2;

    final rect = Rect.fromCenter(
        center: Offset(cx, cy), width: boxSize, height: boxSize);

    final path = Path()
      ..addRect(Rect.fromLTWH(0, 0, size.width, size.height))
      ..addRRect(RRect.fromRectAndRadius(rect, const Radius.circular(16)))
      ..fillType = PathFillType.evenOdd;

    canvas.drawPath(path, Paint()..color = Colors.black.withOpacity(0.55));
  }

  @override
  bool shouldRepaint(_) => false;
}

// Custom painter untuk sudut-sudut kotak scan
class _CornerPainter extends CustomPainter {
  final double thick, radius;
  final Color color;
  final bool isTop, isLeft;

  _CornerPainter(this.thick, this.radius, this.color, this.isTop, this.isLeft);

  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..color = color
      ..strokeWidth = thick
      ..style = PaintingStyle.stroke
      ..strokeCap = StrokeCap.round;

    final path = Path();
    final w = size.width, h = size.height;

    if (isTop && isLeft) {
      path.moveTo(0, h);
      path.lineTo(0, radius);
      path.arcToPoint(Offset(radius, 0), radius: Radius.circular(radius));
      path.lineTo(w, 0);
    } else if (isTop && !isLeft) {
      path.moveTo(0, 0);
      path.lineTo(w - radius, 0);
      path.arcToPoint(Offset(w, radius), radius: Radius.circular(radius));
      path.lineTo(w, h);
    } else if (!isTop && isLeft) {
      path.moveTo(0, 0);
      path.lineTo(0, h - radius);
      path.arcToPoint(Offset(radius, h), radius: Radius.circular(radius));
      path.lineTo(w, h);
    } else {
      path.moveTo(0, h);
      path.lineTo(w - radius, h);
      path.arcToPoint(Offset(w, h - radius), radius: Radius.circular(radius));
      path.lineTo(w, 0);
    }

    canvas.drawPath(path, paint);
  }

  @override
  bool shouldRepaint(_) => false;
}
