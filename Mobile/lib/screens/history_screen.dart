import 'package:flutter/material.dart';
import '../config/theme.dart';
import '../models/student_model.dart';
import '../services/attendance_service.dart';

/// Menampung satu kelompok riwayat untuk satu mata kuliah, beserta
/// ringkasan kehadirannya (dihitung dari daftar record milik mata kuliah
/// tersebut).
class _SubjectGroup {
  final String subject;
  final List<AttendanceRecord> records;

  _SubjectGroup(this.subject, this.records);

  int get total => records.length;
  int get hadir => records.where((r) => r.status == 'hadir').length;
  int get persen => total > 0 ? ((hadir / total) * 100).round() : 0;
}

class HistoryScreen extends StatefulWidget {
  const HistoryScreen({super.key});

  @override
  State<HistoryScreen> createState() => _HistoryScreenState();
}

class _HistoryScreenState extends State<HistoryScreen> {
  final _service = AttendanceService();
  List<AttendanceRecord> _records = [];
  bool _loading = true;

  /// Mata kuliah yang sedang di-expand. Default: semua ter-collapse
  /// supaya ringkasan per mata kuliah langsung terlihat tanpa scroll
  /// panjang — pengguna tinggal tap mata kuliah yang ingin dilihat detailnya.
  final Set<String> _expanded = {};

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    final data = await _service.getHistory();
    if (mounted) {
      setState(() {
        _records = data;
        _loading = false;
      });
    }
  }

  /// Mengelompokkan riwayat presensi per mata kuliah (`subject`).
  /// Urutan mata kuliah mengikuti urutan kemunculan pertama di data asli
  /// (backend mengurutkan dari yang terbaru), sehingga mata kuliah dengan
  /// aktivitas presensi paling baru tampil paling atas. Di dalam tiap
  /// kelompok, urutan record asli (kronologis dari backend) tetap dijaga.
  List<_SubjectGroup> _groupBySubject(List<AttendanceRecord> records) {
    final Map<String, List<AttendanceRecord>> map = {};
    for (final r in records) {
      map.putIfAbsent(r.subject, () => []).add(r);
    }
    return map.entries.map((e) => _SubjectGroup(e.key, e.value)).toList();
  }

  @override
  Widget build(BuildContext context) {
    final total = _records.length;
    final hadir = _records.where((r) => r.status == 'hadir').length;
    final persen = total > 0 ? (hadir / total * 100).toStringAsFixed(0) : '0';
    final pct = int.tryParse(persen) ?? 0;
    final groups = _groupBySubject(_records);

    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(
        title: const Text('Riwayat Presensi'),
        backgroundColor: AppColors.teal,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: _loading
          ? const Center(
              child: CircularProgressIndicator(color: AppColors.teal),
            )
          : RefreshIndicator(
              color: AppColors.teal,
              onRefresh: _load,
              child: CustomScrollView(
                slivers: [
                  // Summary stats keseluruhan di header teal
                  SliverToBoxAdapter(
                    child: Container(
                      color: AppColors.teal,
                      padding: const EdgeInsets.fromLTRB(16, 0, 16, 20),
                      child: Row(
                        children: [
                          _statCard(
                            'Total',
                            '$total',
                            AppColors.white,
                            AppColors.tealLight,
                          ),
                          const SizedBox(width: 10),
                          _statCard(
                            'Hadir',
                            '$hadir',
                            AppColors.white,
                            AppColors.tealLight,
                          ),
                          const SizedBox(width: 10),
                          _statCard(
                            '% Hadir',
                            '$persen%',
                            pct >= 75
                                ? const Color(0xFFD1FAE5)
                                : const Color(0xFFFEE2E2),
                            pct >= 75 ? AppColors.green : AppColors.red,
                          ),
                        ],
                      ),
                    ),
                  ),

                  // Progress bar kehadiran keseluruhan
                  SliverToBoxAdapter(
                    child: Padding(
                      padding: const EdgeInsets.fromLTRB(16, 16, 16, 0),
                      child: Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: AppColors.white,
                          borderRadius: BorderRadius.circular(14),
                          border: Border.all(color: AppColors.border),
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                const Text(
                                  'Tingkat Kehadiran Keseluruhan',
                                  style: TextStyle(
                                    fontSize: 13,
                                    fontWeight: FontWeight.w700,
                                    color: AppColors.txt,
                                  ),
                                ),
                                Text(
                                  '$persen%',
                                  style: TextStyle(
                                    fontSize: 13,
                                    fontWeight: FontWeight.w700,
                                    color: pct >= 75
                                        ? AppColors.green
                                        : AppColors.red,
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 10),
                            ClipRRect(
                              borderRadius: BorderRadius.circular(100),
                              child: LinearProgressIndicator(
                                value: total > 0 ? hadir / total : 0,
                                backgroundColor: AppColors.surface2,
                                color: pct >= 75
                                    ? AppColors.green
                                    : AppColors.red,
                                minHeight: 8,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ),

                  if (_records.isNotEmpty)
                    SliverToBoxAdapter(
                      child: Padding(
                        padding: const EdgeInsets.fromLTRB(16, 20, 16, 4),
                        child: Text(
                          'Per Mata Kuliah (${groups.length})',
                          style: const TextStyle(
                            fontSize: 13,
                            fontWeight: FontWeight.w700,
                            color: AppColors.txt,
                          ),
                        ),
                      ),
                    ),

                  // Daftar dikelompokkan per mata kuliah, atau state kosong.
                  if (_records.isEmpty)
                    const SliverFillRemaining(
                      child: Center(
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Icon(
                              Icons.history_rounded,
                              size: 60,
                              color: AppColors.tealLight,
                            ),
                            SizedBox(height: 14),
                            Text(
                              'Belum ada riwayat presensi',
                              style: TextStyle(
                                color: AppColors.txtGray,
                                fontSize: 14,
                              ),
                            ),
                          ],
                        ),
                      ),
                    )
                  else
                    SliverPadding(
                      padding: const EdgeInsets.fromLTRB(16, 8, 16, 24),
                      sliver: SliverList(
                        delegate: SliverChildBuilderDelegate(
                          (_, i) => _buildSubjectGroup(groups[i]),
                          childCount: groups.length,
                        ),
                      ),
                    ),
                ],
              ),
            ),
    );
  }

  Widget _statCard(String label, String value, Color bg, Color textColor) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 14),
        decoration: BoxDecoration(
          color: bg.withOpacity(0.15),
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: Colors.white.withOpacity(0.2)),
        ),
        child: Column(
          children: [
            Text(
              value,
              style: TextStyle(
                fontSize: 22,
                fontWeight: FontWeight.w800,
                color: textColor,
              ),
            ),
            const SizedBox(height: 2),
            Text(
              label,
              style: TextStyle(
                fontSize: 11,
                color: Colors.white.withOpacity(0.7),
              ),
            ),
          ],
        ),
      ),
    );
  }

  /// Kartu satu mata kuliah: header ringkasan (selalu tampil) + daftar
  /// pertemuan yang bisa di-expand/collapse dengan tap.
  Widget _buildSubjectGroup(_SubjectGroup group) {
    final isOpen = _expanded.contains(group.subject);
    final pctColor = group.persen >= 75 ? AppColors.green : AppColors.red;

    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      decoration: BoxDecoration(
        color: AppColors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          InkWell(
            borderRadius: BorderRadius.circular(14),
            onTap: () {
              setState(() {
                if (isOpen) {
                  _expanded.remove(group.subject);
                } else {
                  _expanded.add(group.subject);
                }
              });
            },
            child: Padding(
              padding: const EdgeInsets.all(14),
              child: Row(
                children: [
                  Container(
                    width: 44,
                    height: 44,
                    decoration: BoxDecoration(
                      color: AppColors.teal.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: const Icon(
                      Icons.book_rounded,
                      color: AppColors.teal,
                      size: 22,
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          group.subject,
                          style: const TextStyle(
                            fontWeight: FontWeight.w700,
                            fontSize: 14,
                            color: AppColors.txt,
                          ),
                        ),
                        const SizedBox(height: 3),
                        Text(
                          '${group.hadir}/${group.total} pertemuan hadir',
                          style: const TextStyle(
                            color: AppColors.txtGray,
                            fontSize: 12,
                          ),
                        ),
                      ],
                    ),
                  ),
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 10,
                      vertical: 5,
                    ),
                    decoration: BoxDecoration(
                      color: pctColor.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Text(
                      '${group.persen}%',
                      style: TextStyle(
                        color: pctColor,
                        fontSize: 12,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                  ),
                  const SizedBox(width: 6),
                  AnimatedRotation(
                    turns: isOpen ? 0.5 : 0,
                    duration: const Duration(milliseconds: 200),
                    child: const Icon(
                      Icons.keyboard_arrow_down_rounded,
                      color: AppColors.txtGray,
                    ),
                  ),
                ],
              ),
            ),
          ),
          AnimatedCrossFade(
            duration: const Duration(milliseconds: 200),
            crossFadeState: isOpen
                ? CrossFadeState.showFirst
                : CrossFadeState.showSecond,
            firstChild: Column(
              children: [
                const Divider(height: 1, color: AppColors.border),
                Padding(
                  padding: const EdgeInsets.fromLTRB(14, 4, 14, 10),
                  child: Column(
                    children: group.records
                        .map((rec) => _buildMeetingRow(rec))
                        .toList(),
                  ),
                ),
              ],
            ),
            secondChild: const SizedBox.shrink(),
          ),
        ],
      ),
    );
  }

  /// Satu baris pertemuan di dalam mata kuliah yang sedang di-expand.
  Widget _buildMeetingRow(AttendanceRecord rec) {
    final color = rec.statusColor;
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Row(
        children: [
          Container(
            width: 8,
            height: 8,
            decoration: BoxDecoration(color: color, shape: BoxShape.circle),
          ),
          const SizedBox(width: 10),
          Expanded(
            child: Text(
              rec.date,
              style: const TextStyle(color: AppColors.txtMid, fontSize: 13),
            ),
          ),
          if (rec.time != null) ...[
            Text(
              rec.time!,
              style: const TextStyle(fontSize: 12, color: AppColors.txtLight),
            ),
            const SizedBox(width: 10),
          ],
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 9, vertical: 3),
            decoration: BoxDecoration(
              color: color.withOpacity(0.1),
              borderRadius: BorderRadius.circular(20),
            ),
            child: Text(
              rec.statusLabel,
              style: TextStyle(
                color: color,
                fontSize: 11,
                fontWeight: FontWeight.w700,
              ),
            ),
          ),
        ],
      ),
    );
  }
}
