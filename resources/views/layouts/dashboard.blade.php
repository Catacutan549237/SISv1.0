<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard - SIS')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --forest-green: #2d5016;
            --sage-green: #4a7c2c;
            --light-sage: #6b9b47;
            --mint-green: #8fb569;
            --cream: #f5f7f2;
            --dark-text: #1a1a1a;
            --gray-text: #666;
            --light-gray: #e2e8f0;
            --white: #ffffff;
            --error-red: #c53030;
            --success-green: #38a169;
            --warning-yellow: #d69e2e;
            --info-blue: #3182ce;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--cream);
            color: var(--dark-text);
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, var(--forest-green) 0%, var(--sage-green) 100%);
            color: var(--white);
            padding: 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 30px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: var(--white);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
            color: var(--forest-green);
        }

        .logo-text {
            font-size: 20px;
            font-weight: 700;
        }


        .user-info {
            font-size: 13px;
            opacity: 0.9;
        }

        .user-info:hover {
            background: rgba(255, 255, 255, 0.1) !important;
            opacity: 1;
        }

        .user-name {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .user-role {
            opacity: 0.8;
            text-transform: capitalize;
        }


        .sidebar-nav {
            padding: 20px 0;
        }

        .nav-category {
            margin-bottom: 8px;
        }

        .category-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
            color: rgba(255, 255, 255, 0.6);
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.3s ease;
            user-select: none;
        }

        .category-header:hover {
            color: rgba(255, 255, 255, 0.9);
            background: rgba(255, 255, 255, 0.05);
        }

        .category-icon {
            font-size: 10px;
            transition: transform 0.3s ease;
        }

        .category-icon.collapsed {
            transform: rotate(-90deg);
        }

        .category-items {
            max-height: 1000px;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .category-items.collapsed {
            max-height: 0;
        }

        .nav-item {
            margin-bottom: 4px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px 12px 32px;
            color: var(--white);
            text-decoration: none;
            transition: all 0.3s ease;
            opacity: 0.8;
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            opacity: 1;
        }

        .nav-icon {
            font-size: 18px;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 30px;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--dark-text);
            margin-bottom: 8px;
        }

        .page-subtitle {
            color: var(--gray-text);
            font-size: 14px;
        }

        /* Cards */
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: var(--white);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .stat-card {
            border-left: 4px solid var(--sage-green);
        }

        .stat-card.warning {
            border-left-color: var(--warning-yellow);
        }

        .stat-card.error {
            border-left-color: var(--error-red);
        }

        .stat-card.info {
            border-left-color: var(--info-blue);
        }

        .stat-label {
            font-size: 13px;
            color: var(--gray-text);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: var(--dark-text);
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
            color: var(--dark-text);
        }

        /* Table */
        .table-container {
            background: var(--white);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 12px;
            background: var(--cream);
            color: var(--dark-text);
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid var(--light-gray);
        }

        tr:last-child td {
            border-bottom: none;
        }

        /* Buttons */
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: var(--sage-green);
            color: var(--white);
        }

        .btn-primary:hover {
            background: var(--forest-green);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(74, 124, 44, 0.3);
        }

        .btn-secondary {
            background: var(--light-gray);
            color: var(--dark-text);
        }

        .btn-secondary:hover {
            background: #cbd5e0;
        }

        .btn-danger {
            background: var(--error-red);
            color: var(--white);
        }

        .btn-danger:hover {
            background: #9b2c2c;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }

        /* Badge */
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success {
            background: #c6f6d5;
            color: #22543d;
        }

        .badge-warning {
            background: #feebc8;
            color: #7c2d12;
        }

        .badge-error {
            background: #fed7d7;
            color: #742a2a;
        }

        .badge-info {
            background: #bee3f8;
            color: #2c5282;
        }

        /* Alert */
        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: #c6f6d5;
            color: #22543d;
            border: 1px solid #9ae6b4;
        }

        .alert-error {
            background: #fed7d7;
            color: #742a2a;
            border: 1px solid #fc8181;
        }

        .alert-warning {
            background: #feebc8;
            color: #7c2d12;
            border: 1px solid #fbd38d;
        }

        .alert-info {
            background: #bee3f8;
            color: #2c5282;
            border: 1px solid #90cdf4;
        }

        /* Form */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: var(--dark-text);
            margin-bottom: 8px;
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid var(--light-gray);
            border-radius: 8px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
        }

        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--sage-green);
            box-shadow: 0 0 0 3px rgba(74, 124, 44, 0.1);
        }

        .form-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--sage-green);
        }

        /* Logout Button */
        .logout-btn {
            margin: 20px;
            width: calc(100% - 40px);
            background: rgba(255, 255, 255, 0.1);
            color: var(--white);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <div class="logo-icon">SIS</div>
                    <div class="logo-text">Student Portal</div>
                </div>
                @php
                    $changePasswordRoute = match(auth()->user()->role) {
                        'professor' => route('professor.password.change'),
                        'student' => route('student.password.change'),
                        default => '#'
                    };
                @endphp
                <a href="{{ $changePasswordRoute }}" class="user-info" style="text-decoration: none; color: inherit; display: block; padding: 12px; margin: -12px; border-radius: 8px; transition: background 0.3s;">
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">{{ auth()->user()->role }}</div>
                </a>
            </div>
            
            <nav class="sidebar-nav">
                @yield('sidebar')
            </nav>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn logout-btn">Logout</button>
            </form>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        // Handle collapsible sidebar categories
        document.addEventListener('DOMContentLoaded', function() {
            const categoryHeaders = document.querySelectorAll('.category-header');
            
            categoryHeaders.forEach(header => {
                header.addEventListener('click', function() {
                    const categoryItems = this.nextElementSibling;
                    const icon = this.querySelector('.category-icon');
                    
                    categoryItems.classList.toggle('collapsed');
                    icon.classList.toggle('collapsed');
                    
                    // Save state to localStorage
                    const categoryName = this.textContent.trim().replace('▼', '').replace('▶', '').trim();
                    const isCollapsed = categoryItems.classList.contains('collapsed');
                    localStorage.setItem('sidebar-' + categoryName, isCollapsed);
                });
                
                // Restore state from localStorage
                const categoryName = header.textContent.trim().replace('▼', '').replace('▶', '').trim();
                const isCollapsed = localStorage.getItem('sidebar-' + categoryName) === 'true';
                if (isCollapsed) {
                    const categoryItems = header.nextElementSibling;
                    const icon = header.querySelector('.category-icon');
                    categoryItems.classList.add('collapsed');
                    icon.classList.add('collapsed');
                }
            });
        });
    </script>

    @yield('scripts')
</body>
</html>
