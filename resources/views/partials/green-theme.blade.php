{{-- ETERA White & Green Gradient Theme Override --}}
{{-- Scoped to .wrapper (admin/dashboard layouts) to avoid breaking auth/etera-modern pages --}}
<style>
/* ====== ETERA White & Green Gradient Theme (Dashboard Only) ====== */

/* Sidebar */
.wrapper .sidebar-wrapper { background: linear-gradient(180deg, #ffffff 0%, #e8f5e9 100%) !important; }
.wrapper .sidebar-wrapper .metismenu a { color: #2e7d32 !important; }
.wrapper .sidebar-wrapper .metismenu a:hover,
.wrapper .sidebar-wrapper .metismenu .mm-active > a { background: rgba(40,167,69,0.12) !important; color: #1b5e20 !important; }
.wrapper .sidebar-header { background: transparent !important; }

/* Top bar */
.wrapper .topbar { background: linear-gradient(135deg, #ffffff, #f1f8e9) !important; border-bottom: 2px solid #c8e6c9; }

/* Page background */
.wrapper .page-wrapper { background: linear-gradient(135deg, #fafffe 0%, #e8f5e9 50%, #f1f8e9 100%) !important; min-height: 100vh; }
.wrapper .page-footer { background: transparent !important; color: #2e7d32 !important; }

/* Cards (inside wrapper only) */
.wrapper .card { border: 1px solid #c8e6c9 !important; box-shadow: 0 2px 12px rgba(40,167,69,0.08) !important; }
.wrapper .card-header.bg-primary { background: linear-gradient(135deg, #28a745, #20c997) !important; border: none; }
.wrapper .card-header.bg-success { background: linear-gradient(135deg, #2e7d32, #43a047) !important; border: none; }
.wrapper .card-header.bg-info { background: linear-gradient(135deg, #00897b, #26a69a) !important; border: none; }

/* Buttons (inside wrapper only) */
.wrapper .btn-primary { background: linear-gradient(135deg, #28a745, #20c997) !important; border: none !important; }
.wrapper .btn-primary:hover { background: linear-gradient(135deg, #1e7e34, #17a2b8) !important; }
.wrapper .btn-success { background: linear-gradient(135deg, #2e7d32, #43a047) !important; border: none !important; }

/* Misc dashboard elements */
.wrapper .back-to-top { background: #28a745 !important; }
.wrapper .form-check-input:checked { background-color: #28a745 !important; border-color: #28a745 !important; }
.wrapper .badge.bg-primary { background: linear-gradient(135deg, #28a745, #20c997) !important; }

/* Spare-part / modern dashboard specific */
.ep-sidebar { background: linear-gradient(180deg, #ffffff 0%, #e8f5e9 100%) !important; }
.ep-topbar { background: linear-gradient(135deg, #ffffff, #f1f8e9) !important; border-bottom: 2px solid #c8e6c9; }
.ep-main-content { background: linear-gradient(135deg, #fafffe 0%, #e8f5e9 50%, #f1f8e9 100%) !important; }
</style>

