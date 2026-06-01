/* ═══════════════════════════════════════════════════════════
   SISTEM REMUNERASI RESTORAN — Main JavaScript
   ═══════════════════════════════════════════════════════════ */

// ─── Toggle Sidebar (Mobile) ──────────────────────────────
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar) {
        sidebar.classList.toggle('show');
    }
}

// ─── Auto-hide alerts ─────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    // Auto hide flash alerts after 4 seconds
    const alerts = document.querySelectorAll('.alert-auto-hide');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(function () { alert.remove(); }, 500);
        }, 4000);
    });

    // Active sidebar item
    const currentPath = window.location.pathname;
    const sidebarItems = document.querySelectorAll('.sidebar-item');
    sidebarItems.forEach(function (item) {
        const href = item.getAttribute('href');
        if (href && currentPath.startsWith(href) && href !== '/') {
            item.classList.add('active');
        }
    });
});

// ─── Confirm Delete ───────────────────────────────────────
function confirmDelete(message, formId) {
    if (confirm(message || 'Apakah Anda yakin ingin menghapus data ini?')) {
        document.getElementById(formId).submit();
    }
}

// ─── Format Currency ──────────────────────────────────────
function formatRupiah(number) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(number);
}

// ─── Loading State ────────────────────────────────────────
function setLoading(btn, loading = true) {
    if (loading) {
        btn.disabled = true;
        btn.dataset.originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
    } else {
        btn.disabled = false;
        btn.innerHTML = btn.dataset.originalText;
    }
}