<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gray-100">

<div class="max-w-6xl mx-auto mt-10 p-4">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">📚 Posts Dashboard</h1>
        <div class="space-x-2">
            <button onclick="toggleTrashView(false)" id="btnActiveList" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold shadow-sm hidden">Show Active Posts</button>
            <button onclick="toggleTrashView(true)" id="btnTrashList" class="px-4 py-2 bg-gray-600 text-white rounded-lg font-semibold shadow-sm">View Trash 🗑</button>
            <button onclick="openModal('create')" class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 font-semibold shadow-md">+ Create New Post</button>
        </div>
    </div>

    <div class="bg-white p-4 rounded-lg shadow-sm mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="w-full md:w-1/2 flex gap-2">
            <input 
                type="text" 
                id="searchKey"
                placeholder="Search posts dynamically..."
                class="w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                oninput="fetchPosts()"
            >
        </div>
        
        <div class="flex gap-4 items-center w-full md:w-auto">
            <select id="sortField" onchange="fetchPosts()" class="px-4 py-2 border rounded-lg bg-white shadow-sm focus:outline-none">
                <option value="id">Sort by ID</option>
                <option value="title">Sort by Title</option>
                <option value="created_at">Sort by Date</option>
            </select>
            
            <select id="sortDirection" onchange="fetchPosts()" class="px-4 py-2 border rounded-lg bg-white shadow-sm focus:outline-none">
                <option value="desc">Descending</option>
                <option value="asc">Ascending</option>
            </select>
        </div>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-200 text-gray-700">
                <tr>
                    <th class="py-3 px-4">ID</th>
                    <th class="py-3 px-4">Title</th>
                    <th class="py-3 px-4">Description</th>
                    <th class="py-3 px-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="postsTableBody">
                @forelse($posts as $post)
                    <tr class="border-b hover:bg-gray-50 transition" id="post-row-{{ $post->id }}">
                        <td class="py-3 px-4">{{ $post->id }}</td>
                        <td class="py-3 px-4 font-semibold text-gray-800 title-cell">{{ $post->title }}</td>
                        <td class="py-3 px-4 text-gray-600 desc-cell">{{ \Illuminate\Support\Str::limit($post->description, 80) }}</td>
                        <td class="py-3 px-4 text-right space-x-2">
                            <button onclick="editPost({{ $post->id }}, '{{ addslashes($post->title) }}', '{{ addslashes($post->description) }}')" class="text-blue-500 hover:text-blue-700 font-medium">Edit</button>
                            <button onclick="deletePost({{ $post->id }})" class="text-red-500 hover:text-red-700 font-medium">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr id="no-posts-row">
                        <td colspan="4" class="text-center py-6 text-gray-500">No posts found 😢</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="postModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden justify-center items-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full p-6 shadow-2xl">
        <h2 id="modalTitle" class="text-2xl font-bold mb-4 text-gray-800">Create Post</h2>
        <input type="hidden" id="postId">
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Title</label>
            <input type="text" id="postTitle" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Description</label>
            <textarea id="postDesc" rows="4" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
        </div>
        <div class="flex justify-end gap-2">
            <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg">Cancel</button>
            <button onclick="submitPostForm()" class="px-4 py-2 bg-blue-500 text-white rounded-lg">Save</button>
        </div>
    </div>
</div>

<input type="hidden" id="csrfToken" value="{{ csrf_token() }}">

<script>
    let viewTrash = false;

    function fetchPosts() {
        let search = document.getElementById('searchKey').value;
        let sort = document.getElementById('sortField').value;
        let direction = document.getElementById('sortDirection').value;
        let status = viewTrash ? 'trash' : 'active';

        axios.get('/posts', {
            params: { search: search, sort: sort, direction: direction, status: status },
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => {
            if (response.data.success) {
                renderTable(response.data.posts);
            }
        });
    }

    function toggleTrashView(isTrash) {
        viewTrash = isTrash;
        if (isTrash) {
            document.getElementById('btnTrashList').classList.add('hidden');
            document.getElementById('btnActiveList').classList.remove('hidden');
        } else {
            document.getElementById('btnTrashList').classList.remove('hidden');
            document.getElementById('btnActiveList').classList.add('hidden');
        }
        fetchPosts();
    }

    function renderTable(posts) {
        let tbody = document.getElementById('postsTableBody');
        tbody.innerHTML = '';

        if (posts.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center py-6 text-gray-500">No posts found 😢</td></tr>`;
            return;
        }

        posts.forEach(post => {
            let shortDesc = post.description.length > 80 ? post.description.substring(0, 80) + '...' : post.description;
            let actionButtons = '';

            if (viewTrash) {
                actionButtons = `
                    <button onclick="restorePost(${post.id})" class="text-green-500 hover:text-green-700 font-medium">Restore</button>
                    <button onclick="forceDeletePost(${post.id})" class="text-red-600 hover:text-red-800 font-medium">Delete Permanently</button>
                `;
            } else {
                actionButtons = `
                    <button onclick="editPost(${post.id}, \`${post.title.replace(/`/g, '\\`').replace(/"/g, '&quot;')}\`, \`${post.description.replace(/`/g, '\\`').replace(/"/g, '&quot;')}\`)" class="text-blue-500 hover:text-blue-700 font-medium">Edit</button>
                    <button onclick="deletePost(${post.id})" class="text-red-500 hover:text-red-700 font-medium">Delete</button>
                `;
            }

            tbody.innerHTML += `
                <tr class="border-b hover:bg-gray-50 transition" id="post-row-${post.id}">
                    <td class="py-3 px-4">${post.id}</td>
                    <td class="py-3 px-4 font-semibold text-gray-800 title-cell">${post.title}</td>
                    <td class="py-3 px-4 text-gray-600 desc-cell">${shortDesc}</td>
                    <td class="py-3 px-4 text-right space-x-2">${actionButtons}</td>
                </tr>
            `;
        });
    }

    function openModal(mode) {
        let modal = document.getElementById('postModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        if (mode === 'create') {
            document.getElementById('modalTitle').innerText = 'Create Post';
            document.getElementById('postId').value = '';
            document.getElementById('postTitle').value = '';
            document.getElementById('postDesc').value = '';
        }
    }

    function closeModal() {
        let modal = document.getElementById('postModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function editPost(id, title, description) {
        openModal('edit');
        document.getElementById('modalTitle').innerText = 'Edit Post';
        document.getElementById('postId').value = id;
        document.getElementById('postTitle').value = title;
        document.getElementById('postDesc').value = description;
    }

    function submitPostForm() {
        let id = document.getElementById('postId').value;
        let title = document.getElementById('postTitle').value;
        let description = document.getElementById('postDesc').value;
        let token = document.getElementById('csrfToken').value;

        let headers = {
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest'
        };

        if (id) {
            axios.put(`/posts/${id}`, { title: title, description: description }, { headers: headers })
                .then(response => {
                    if (response.data.success) {
                        closeModal();
                        fetchPosts();
                    }
                });
        } else {
            axios.post('/posts', { title: title, description: description }, { headers: headers })
                .then(response => {
                    if (response.data.success) {
                        closeModal();
                        fetchPosts();
                    }
                });
        }
    }

    function deletePost(id) {
        let token = document.getElementById('csrfToken').value;
        if (confirm('Are you sure you want to move this post to trash?')) {
            axios.delete(`/posts/${id}`, {
                headers: { 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => {
                fetchPosts();
            });
        }
    }

    function restorePost(id) {
        let token = document.getElementById('csrfToken').value;
        axios.post(`/posts/restore/${id}`, {}, {
            headers: { 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => {
            fetchPosts();
        });
    }

    function forceDeletePost(id) {
        let token = document.getElementById('csrfToken').value;
        if (confirm('Are you sure you want to permanently delete this post? This cannot be undone.')) {
            axios.delete(`/posts/force/${id}`, {
                headers: { 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => {
                fetchPosts();
            });
        }
    }
</script>
</body>
</html>