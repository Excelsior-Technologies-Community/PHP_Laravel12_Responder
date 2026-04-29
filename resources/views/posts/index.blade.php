<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Posts Dashboard</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<div class="max-w-6xl mx-auto mt-10">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">📚 Posts Dashboard</h1>
    </div>

    <!-- SEARCH BOX -->
    <form method="GET" action="/posts" class="mb-6 flex gap-2">
        <input 
            type="text" 
            name="search"
            value="{{ request('search') }}"
            placeholder="Search posts..."
            class="w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
        >

        <button class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
            Search
        </button>
    </form>

    <!-- TABLE -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">

        <table class="w-full text-left">
            <thead class="bg-gray-200 text-gray-700">
                <tr>
                    <th class="py-3 px-4">ID</th>
                    <th class="py-3 px-4">Title</th>
                    <th class="py-3 px-4">Description</th>
                </tr>
            </thead>

            <tbody>
                @forelse($posts as $post)
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="py-3 px-4">{{ $post->id }}</td>
                        <td class="py-3 px-4 font-semibold text-gray-800">
                            {{ $post->title }}
                        </td>
                        <td class="py-3 px-4 text-gray-600">
                            {{ Str::limit($post->description, 80) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-6 text-gray-500">
                            No posts found 😢
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>

</div>

</body>
</html>