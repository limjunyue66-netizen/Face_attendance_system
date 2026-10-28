<?php
use App\Helpers\Url;
use App\Helpers\Auth;

$user = Auth::user();
$role = $user['role'] ?? 'guest';

// Normalize current request route accurately (stripping project base subdirectory)
$rawUri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '/';
$baseUrl = parse_url(Url::to(''), PHP_URL_PATH) ?: '';
$route = '/' . ltrim(substr($rawUri, strlen($baseUrl)), '/');

// Precise Active route checker ensuring ONLY ONE menu item is active
$isActive = function(string $targetPath, bool $exact = false) use ($route): bool {
    $r = '/' . trim($route, '/');
    $t = '/' . trim($targetPath, '/');
    if ($exact || $t === '/') {
        return $r === $t;
    }
    return str_starts_with($r, $t);
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= !empty($title) ? htmlspecialchars($title) : 'Face Attendance System' ?></title>
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%232563eb'><path d='M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm1 14.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-4.5a1 1 0 0 1-2 0V7a1 1 0 0 1 2 0z'/></svg>">
    
    <!-- Offline Local CSS Assets -->
    <link rel="stylesheet" href="<?= Url::asset('assets/vendor/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= Url::asset('assets/vendor/bootstrap-icons/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= Url::asset('assets/css/app.css') ?>">
</head>
<body>
<div class="app-wrapper">

    <!-- Sidebar Navigation -->
    <aside class="app-sidebar" id="appSidebar">
        <a href="<?= Url::to('/') ?>" class="app-sidebar-brand">
            <div class="brand-icon-wrapper">
                <i class="bi bi-person-bounding-box"></i>
            </div>
            <div>
                <div class="brand-text-title">FacePass</div>
                <div class="brand-text-subtitle">Attendance & Biometrics</div>
            </div>
        </a>

        <div class="app-sidebar-nav">
            <?php if ($role === 'admin'): ?>
                <!-- Administrator Menu -->
                <div class="nav-section-title"><i class="bi bi-grid-fill"></i> Core Management</div>
                <a href="<?= Url::to('/admin/dashboard') ?>" class="sidebar-link <?= $isActive('/admin/dashboard') ? 'active' : '' ?>">
                    <span class="sidebar-link-icon-box"><i class="bi bi-grid-1x2-fill"></i></span>
                    <span>Dashboard</span>
                </a>
                <a href="<?= Url::to('/admin/students') ?>" class="sidebar-link <?= $isActive('/admin/students') ? 'active' : '' ?>">
                    <span class="sidebar-link-icon-box"><i class="bi bi-people-fill"></i></span>
                    <span>Students</span>
                </a>
                <a href="<?= Url::to('/admin/teachers') ?>" class="sidebar-link <?= $isActive('/admin/teachers') ? 'active' : '' ?>">
                    <span class="sidebar-link-icon-box"><i class="bi bi-person-workspace"></i></span>
                    <span>Teachers</span>
                </a>
                <a href="<?= Url::to('/admin/classes') ?>" class="sidebar-link <?= $isActive('/admin/classes') ? 'active' : '' ?>">
                    <span class="sidebar-link-icon-box"><i class="bi bi-mortarboard-fill"></i></span>
                    <span>Classes</span>
                </a>
                <a href="<?= Url::to('/admin/subjects') ?>" class="sidebar-link <?= $isActive('/admin/subjects') ? 'active' : '' ?>">
                    <span class="sidebar-link-icon-box"><i class="bi bi-journal-bookmark-fill"></i></span>
                    <span>Subjects</span>
                </a>
                <a href="<?= Url::to('/admin/assignments') ?>" class="sidebar-link <?= $isActive('/admin/assignments') ? 'active' : '' ?>">
                    <span class="sidebar-link-icon-box"><i class="bi bi-diagram-3-fill"></i></span>
                    <span>Assignments</span>
                </a>

                <div class="nav-section-title"><i class="bi bi-calendar-range-fill"></i> Attendance & Leaves</div>
                <a href="<?= Url::to('/admin/attendance') ?>" class="sidebar-link <?= $isActive('/admin/attendance') ? 'active' : '' ?>">
                    <span class="sidebar-link-icon-box"><i class="bi bi-calendar2-check-fill"></i></span>
                    <span>Attendance Records</span>
                </a>
                <a href="<?= Url::to('/admin/leaves') ?>" class="sidebar-link <?= $isActive('/admin/leaves') ? 'active' : '' ?>">
                    <span class="sidebar-link-icon-box"><i class="bi bi-envelope-paper-fill"></i></span>
                    <span>Leave Requests</span>
                </a>

                <div class="nav-section-title"><i class="bi bi-shield-lock-fill"></i> System & Security</div>
                <a href="<?= Url::to('/kiosk') ?>" target="_blank" class="sidebar-link">
                    <span class="sidebar-link-icon-box" style="background: rgba(37, 99, 235, 0.2); color: #60a5fa;"><i class="bi bi-camera-video-fill"></i></span>
                    <span class="text-white fw-semibold">Open Kiosk <i class="bi bi-box-arrow-up-right small ms-auto text-info"></i></span>
                </a>
                <a href="<?= Url::to('/admin/settings') ?>" class="sidebar-link <?= $isActive('/admin/settings') ? 'active' : '' ?>">
                    <span class="sidebar-link-icon-box"><i class="bi bi-sliders"></i></span>
                    <span>Face & Quality</span>
                </a>
                <a href="<?= Url::to('/admin/audit') ?>" class="sidebar-link <?= $isActive('/admin/audit') ? 'active' : '' ?>">
                    <span class="sidebar-link-icon-box"><i class="bi bi-shield-shaded"></i></span>
                    <span>Audit Logs</span>
                </a>
                <a href="<?= Url::to('/admin/users') ?>" class="sidebar-link <?= $isActive('/admin/users') ? 'active' : '' ?>">
                    <span class="sidebar-link-icon-box"><i class="bi bi-person-gear"></i></span>
                    <span>User Accounts</span>
                </a>

            <?php elseif ($role === 'teacher'): ?>
                <!-- Teacher Menu -->
                <div class="nav-section-title"><i class="bi bi-person-badge-fill"></i> Teacher Portal</div>
                <a href="<?= Url::to('/teacher/dashboard') ?>" class="sidebar-link <?= $isActive('/teacher/dashboard') ? 'active' : '' ?>">
                    <span class="sidebar-link-icon-box"><i class="bi bi-grid-1x2-fill"></i></span>
                    <span>Dashboard</span>
                </a>
                <a href="<?= Url::to('/teacher/classes') ?>" class="sidebar-link <?= $isActive('/teacher/classes') ? 'active' : '' ?>">
                    <span class="sidebar-link-icon-box"><i class="bi bi-mortarboard-fill"></i></span>
                    <span>My Classes</span>
                </a>
                <a href="<?= Url::to('/teacher/attendance') ?>" class="sidebar-link <?= $isActive('/teacher/attendance') ? 'active' : '' ?>">
                    <span class="sidebar-link-icon-box"><i class="bi bi-calendar2-check-fill"></i></span>
                    <span>Student Attendance</span>
                </a>
                <a href="<?= Url::to('/kiosk') ?>" target="_blank" class="sidebar-link">
                    <span class="sidebar-link-icon-box" style="background: rgba(37, 99, 235, 0.2); color: #60a5fa;"><i class="bi bi-camera-video-fill"></i></span>
                    <span class="text-white fw-semibold">Attendance Kiosk <i class="bi bi-box-arrow-up-right small ms-auto text-info"></i></span>
                </a>

            <?php elseif ($role === 'student'): ?>
                <!-- Student Menu -->
                <div class="nav-section-title"><i class="bi bi-person-workspace"></i> Student Portal</div>
                <a href="<?= Url::to('/student/dashboard') ?>" class="sidebar-link <?= $isActive('/student/dashboard') ? 'active' : '' ?>">
                    <span class="sidebar-link-icon-box"><i class="bi bi-grid-1x2-fill"></i></span>
                    <span>Dashboard</span>
                </a>
                <a href="<?= Url::to('/student/scan') ?>" class="sidebar-link <?= $isActive('/student/scan') ? 'active' : '' ?>">
                    <span class="sidebar-link-icon-box" style="background: rgba(37, 99, 235, 0.2); color: #60a5fa;"><i class="bi bi-camera-fill"></i></span>
                    <span class="text-white fw-semibold">Scan My Face</span>
                </a>
                <a href="<?= Url::to('/student/history') ?>" class="sidebar-link <?= $isActive('/student/history') ? 'active' : '' ?>">
                    <span class="sidebar-link-icon-box"><i class="bi bi-clock-history"></i></span>
                    <span>Attendance History</span>
                </a>
                <a href="<?= Url::to('/student/statistics') ?>" class="sidebar-link <?= $isActive('/student/statistics') ? 'active' : '' ?>">
                    <span class="sidebar-link-icon-box"><i class="bi bi-pie-chart-fill"></i></span>
                    <span>My Statistics</span>
                </a>
                <a href="<?= Url::to('/student/leaves') ?>" class="sidebar-link <?= $isActive('/student/leaves') ? 'active' : '' ?>">
                    <span class="sidebar-link-icon-box"><i class="bi bi-file-earmark-medical-fill"></i></span>
                    <span>Apply for Leave</span>
                </a>
                <a href="<?= Url::to('/student/profile') ?>" class="sidebar-link <?= $isActive('/student/profile') ? 'active' : '' ?>">
                    <span class="sidebar-link-icon-box"><i class="bi bi-person-badge-fill"></i></span>
                    <span>My Profile & Face</span>
                </a>
            <?php endif; ?>

            <div class="nav-section-title"><i class="bi bi-info-circle-fill"></i> Information</div>
            <a href="<?= Url::to('/privacy') ?>" class="sidebar-link <?= $isActive('/privacy') ? 'active' : '' ?>">
                <span class="sidebar-link-icon-box"><i class="bi bi-shield-check"></i></span>
                <span>Privacy & Biometrics</span>
            </a>
        </div>

        <!-- User Profile Footer -->
        <div class="sidebar-user-footer d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2 overflow-hidden">
                <div class="avatar bg-primary text-white flex-shrink-0" style="border-color: rgba(255, 255, 255, 0.15);">
                    <?= strtoupper(substr($user['username'] ?? 'U', 0, 1)) ?>
                </div>
                <div class="overflow-hidden">
                    <div class="text-truncate fw-bold text-white" style="font-size: 0.85rem;"><?= htmlspecialchars($user['username'] ?? 'User') ?></div>
                    <span class="badge bg-primary text-white text-uppercase" style="font-size: 0.65rem; font-weight: 700; letter-spacing: 0.05em;"><?= htmlspecialchars($role) ?></span>
                </div>
            </div>
            <a href="<?= Url::to('/logout') ?>" class="btn btn-sm btn-action-icon text-danger border-0" style="background: rgba(255,255,255,0.08);" title="Sign Out">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="app-main">
        <!-- Topbar Navigation -->
        <header class="app-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light d-lg-none p-2 border" id="sidebarToggle" type="button" aria-label="Toggle navigation">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="d-none d-md-flex align-items-center gap-2 text-muted small">
                    <i class="bi bi-calendar-event text-primary fs-6"></i>
                    <span id="currentDateDisplay" class="fw-medium"><?= date('l, F j, Y') ?></span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <a href="<?= Url::to('/kiosk') ?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill d-none d-sm-inline-flex align-items-center gap-2">
                    <i class="bi bi-camera-video"></i>
                    <span>Live Attendance Kiosk</span>
                </a>

                <div class="dropdown">
                    <button class="user-pill-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="userMenuButton">
                        <div class="avatar bg-primary text-white" style="width: 30px; height: 30px; font-size: 0.82rem; border-color: #2563eb;">
                            <?= strtoupper(substr($user['username'] ?? 'U', 0, 1)) ?>
                        </div>
                        <span class="small fw-bold text-dark d-none d-sm-inline"><?= htmlspecialchars($user['username'] ?? 'User') ?></span>
                        <i class="bi bi-chevron-down small text-muted"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end mt-2" aria-labelledby="userMenuButton">
                        <li><h6 class="dropdown-header"><i class="bi bi-person-check-fill me-1 text-primary"></i> Signed in as <strong><?= htmlspecialchars($user['username'] ?? '') ?></strong></h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= Url::to('/change-password') ?>"><i class="bi bi-key-fill text-muted"></i> Change Password</a></li>
                        <li><a class="dropdown-item" href="<?= Url::to('/privacy') ?>"><i class="bi bi-shield-check text-muted"></i> Biometric Privacy</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= Url::to('/logout') ?>"><i class="bi bi-box-arrow-right"></i> Sign Out</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Content Body -->
        <main class="app-content">
            <!-- Flash Message Alerts -->
            <?php if (!empty($flashSuccess)): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                    <div class="fw-medium"><?= htmlspecialchars($flashSuccess) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($flashError)): ?>
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                    <div class="fw-medium"><?= htmlspecialchars($flashError) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($flashWarning)): ?>
                <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-exclamation-circle-fill fs-5 text-warning"></i>
                    <div class="fw-medium"><?= htmlspecialchars($flashWarning) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($flashInfo)): ?>
                <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-info-circle-fill fs-5 text-info"></i>
                    <div class="fw-medium"><?= htmlspecialchars($flashInfo) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Injected View Content -->
            <?= $content ?>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-top py-3 px-4 text-center text-muted small mt-auto d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <i class="bi bi-shield-lock-fill text-primary me-1"></i> &copy; <?= date('Y') ?> <strong>FacePass</strong> Attendance System. On-device facial biometric engine.
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="<?= Url::to('/privacy') ?>" class="text-muted"><i class="bi bi-file-earmark-text me-1"></i>Privacy Policy</a>
                <span class="text-muted">&bull;</span>
                <span class="text-success"><i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>System Online</span>
            </div>
        </footer>
    </div>
</div>

<!-- Offline Local JS Vendor Libraries -->
<script src="<?= Url::asset('assets/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= Url::asset('assets/vendor/chartjs/chart.umd.min.js') ?>"></script>
<script>
    // Responsive sidebar toggle
    document.getElementById('sidebarToggle')?.addEventListener('click', function() {
        document.getElementById('appSidebar')?.classList.toggle('show');
    });

    // Sidebar Scroll Position Persistence (prevents scrollbar jumping to top)
    (function() {
        const sidebarNav = document.querySelector('.app-sidebar-nav');
        if (!sidebarNav) return;

        // Restore saved position
        const savedPos = sessionStorage.getItem('sidebar_scroll_pos');
        if (savedPos !== null) {
            sidebarNav.scrollTop = parseInt(savedPos, 10);
            // In case layout paints slightly later, ensure restore
            requestAnimationFrame(() => {
                sidebarNav.scrollTop = parseInt(savedPos, 10);
            });
        } else {
            const activeLink = sidebarNav.querySelector('.sidebar-link.active');
            if (activeLink) {
                activeLink.scrollIntoView({ block: 'nearest' });
            }
        }

        // Keep position updated on scroll
        sidebarNav.addEventListener('scroll', function() {
            sessionStorage.setItem('sidebar_scroll_pos', sidebarNav.scrollTop);
        }, { passive: true });

        // Ensure captured immediately when any navigation link is clicked
        sidebarNav.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link) {
                sessionStorage.setItem('sidebar_scroll_pos', sidebarNav.scrollTop);
            }
        });

        window.addEventListener('beforeunload', function() {
            if (sidebarNav) {
                sessionStorage.setItem('sidebar_scroll_pos', sidebarNav.scrollTop);
            }
        });
    })();
</script>
</body>
</html>
