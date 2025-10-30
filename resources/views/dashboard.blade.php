<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Chaos Planner</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body { margin: 0; font-family: sans-serif; background-color: #f0f2f5; }
        #app { display: flex; min-height: 100vh; }
        .sidebar {
            width: 250px;
            background-color: #f7f7f7;
            padding: 20px;
            border-right: 1px solid #e0e0e0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .sidebar-logo {
            margin-bottom: 30px;
        }
        .sidebar-logo img {
            max-width: 150px;
            height: auto;
        }
        .sidebar-nav ul {
            list-style: none;
            padding: 0;
            width: 100%;
        }
        .sidebar-nav li {
            margin-bottom: 15px;
        }
        .sidebar-nav a {
            text-decoration: none;
            color: #333;
            font-size: 1.1em;
            padding: 10px 15px;
            display: block;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active {
            background-color: #e0e0e0;
        }
        .main-content {
            flex-grow: 1;
            padding: 30px;
            background-color: #e2f0f0; /* Цвет по вашему макету */
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: auto auto;
            gap: 20px;
            box-sizing: border-box;
        }
        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .card h2 {
            margin-top: 0;
            color: #333;
            font-size: 1.4em;
            margin-bottom: 15px;
        }
        .task-list, .habit-list {
            list-style: none;
            padding: 0;
        }
        .task-item, .habit-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            font-size: 1em;
            color: #555;
        }
        .task-item .icon, .habit-item .icon {
            margin-right: 10px;
            color: #28a745; /* Зеленый для завершенных/активных */
            font-size: 1.2em;
        }
        .statistic-value {
            font-size: 2.5em;
            font-weight: bold;
            color: #28a745;
            text-align: center;
            margin: 20px 0;
        }
        .statistic-label {
            text-align: center;
            color: #666;
            margin-bottom: 10px;
        }
        .statistic-detail {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            color: #666;
        }
        .statistic-detail span:first-child {
            font-weight: bold;
        }
        .progress-bar {
            height: 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            margin-top: 5px;
        }
        .progress-fill {
            height: 100%;
            background-color: #28a745;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div id="app">
        <!-- Vue.js будет монтироваться здесь -->
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>