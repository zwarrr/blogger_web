<!-- resources/views/admin/dashboard.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; }
  </style>
</head>
<body class="bg-[#F5F7FB] text-gray-800">

  <div class="ml-64 flex-1 flex flex-col min-h-screen">

    {{-- Sidebar --}}
    @include('admin.sidebar')

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">

      <!-- Topbar -->
      <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-900">Dashboard</h2>
      </header>

      <!-- Page Content -->
      <main class="p-6 space-y-6">

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
          <div class="bg-white rounded-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between">
              <h3 class="text-sm font-medium text-gray-500">Total Posts</h3>
              <span class="text-xs text-green-600">+4.5%</span>
            </div>
            <p class="text-2xl font-bold mt-2">128</p>
            <p class="text-xs text-gray-500 mt-1">vs last 7 days</p>
          </div>
          <div class="bg-white rounded-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between">
              <h3 class="text-sm font-medium text-gray-500">Comments</h3>
              <span class="text-xs text-green-600">+2.1%</span>
            </div>
            <p class="text-2xl font-bold mt-2">342</p>
            <p class="text-xs text-gray-500 mt-1">vs last 7 days</p>
          </div>
          <div class="bg-white rounded-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between">
              <h3 class="text-sm font-medium text-gray-500">Page Views</h3>
              <span class="text-xs text-red-600">-1.2%</span>
            </div>
            <p class="text-2xl font-bold mt-2">15,890</p>
            <p class="text-xs text-gray-500 mt-1">vs last 7 days</p>
          </div>
          <div class="bg-white rounded-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between">
              <h3 class="text-sm font-medium text-gray-500">Active Users</h3>
              <span class="text-xs text-green-600">+0.9%</span>
            </div>
            <p class="text-2xl font-bold mt-2">1,234</p>
            <p class="text-xs text-gray-500 mt-1">vs last 7 days</p>
          </div>
        </div>

        <!-- Charts row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div class="bg-white rounded-lg border border-gray-200 p-5 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-900">Traffic Overview</h3>
              <div class="text-sm text-gray-500">Last 7 days</div>
            </div>
            <!-- Simple bar chart using CSS -->
            <div class="flex items-end gap-3 h-40">
              <div class="w-full h-2/3 bg-orange-200 rounded-t-md"></div>
              <div class="w-full h-1/2 bg-orange-300 rounded-t-md"></div>
              <div class="w-full h-3/4 bg-orange-400 rounded-t-md"></div>
              <div class="w-full h-2/5 bg-orange-300 rounded-t-md"></div>
              <div class="w-full h-[85%] bg-orange-500 rounded-t-md"></div>
              <div class="w-full h-2/3 bg-orange-400 rounded-t-md"></div>
              <div class="w-full h-3/5 bg-orange-300 rounded-t-md"></div>
            </div>
          </div>
          <div class="bg-white rounded-lg border border-gray-200 p-5">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Categories</h3>
            <ul class="space-y-3 text-sm">
              <li class="flex items-center justify-between"><span>Tutorials</span><span class="font-semibold">42%</span></li>
              <li class="flex items-center justify-between"><span>News</span><span class="font-semibold">28%</span></li>
              <li class="flex items-center justify-between"><span>Opinion</span><span class="font-semibold">18%</span></li>
              <li class="flex items-center justify-between"><span>Others</span><span class="font-semibold">12%</span></li>
            </ul>
          </div>
        </div>

        <!-- Recent Posts Table -->
        <div class="bg-white border border-gray-200 rounded-lg">
          <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Posts</h3>
          </div>
          <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-gray-600">Title</th>
                <th class="px-6 py-3 text-gray-600">Date</th>
                <th class="px-6 py-3 text-gray-600">Status</th>
                <th class="px-6 py-3 text-gray-600">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr class="border-t">
                <td class="px-6 py-4">10 Tips for Blogging</td>
                <td class="px-6 py-4">Sep 29, 2025</td>
                <td class="px-6 py-4"><span class="text-green-600">Published</span></td>
                <td class="px-6 py-4"><button class="text-orange-600 hover:underline">Edit</button></td>
              </tr>
              <tr class="border-t">
                <td class="px-6 py-4">Welcome to My Blog</td>
                <td class="px-6 py-4">Sep 25, 2025</td>
                <td class="px-6 py-4"><span class="text-yellow-600">Draft</span></td>
                <td class="px-6 py-4"><button class="text-orange-600 hover:underline">Edit</button></td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Recent Comments -->
        <div class="bg-white border border-gray-200 rounded-lg">
          <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Comments</h3>
          </div>
          <div class="divide-y">
            <div class="p-4">
              <p class="text-sm"><strong>John:</strong> Great post, very helpful!</p>
              <p class="text-xs text-gray-500 mt-1">On "10 Tips for Blogging" • Sep 30</p>
            </div>
            <div class="p-4">
              <p class="text-sm"><strong>Lisa:</strong> Can you write more about SEO?</p>
              <p class="text-xs text-gray-500 mt-1">On "Welcome to My Blog" • Sep 28</p>
            </div>
          </div>
        </div>

      </main>
    </div>
  </div>

</body>
</html>
