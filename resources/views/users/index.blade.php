@extends('layouts.app')

@section('style')
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #aaa;
            padding: 8px 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        button {
            padding: 8px 16px;
            background-color: #3490dc;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #2779bd;
        }

        #message-box {
            margin-bottom: 10px;
            padding: 10px;
            display: none;
            border-radius: 4px;
        }
    </style>
@endsection

@section('content')
    <div class="flex justify-center items-around mb-4 gap-4 space-x-4">
        <button onclick="recalculate()">Recalculate</button>
        <button onclick="generate()">Generate</button>
    </div>

    <div class="form">
        <form id="filter-form" method="POST" class="mb-4">
            @csrf
            <input type="number" name="user_id" placeholder="ID" class="border p-2 rounded mr-2">
            <select name="filter" class="border p-2 rounded mr-2">
                <option value="">All</option>
                <option value="day">Day</option>
                <option value="month">Month</option>
                <option value="year">Year</option>
            </select>

            <button type="submit">Filter</button>
            <button type="button" onclick="addPoint()">Add Points</button>
        </form>

        <script>
            function addRandomPoint() {
                const buttons = document.querySelectorAll("button");
                buttons.forEach(btn => btn.disabled = true);

                const randomPoints = Math.floor(Math.random() * 10) + 1; // Random number between 1-10

                fetch('/api/points/add', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            user_id: form.querySelector("input[name='user_id']").value.trim(),
                            points: randomPoints
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        showMessage(data.message, data.status);
                        setTimeout(() => {
                            fetchUser();
                        }, 1000);
                    })
                    .catch(err => {
                        console.error('Add Random Point Error:', err);
                        showMessage('Error adding random points', 'error');
                    })
                    .finally(() => {
                        buttons.forEach(btn => btn.disabled = false);
                    });
            }
        </script>

        <div id="message-box"></div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Points</th>
                    <th>Rank</th>
                </tr>
            </thead>
            <tbody id="tbody"></tbody>
        </table>
    @endsection

    @section('script')
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                fetchUser();
            });

            function fetchUser(userId = '', filter = '') {
                let url = '/api/users';
                if (userId) url += `/${userId}`;
                if (filter) url += `?filter=${filter}`;

                const tbody = document.getElementById("tbody");
                const buttons = document.querySelectorAll("button");
                buttons.forEach(btn => btn.disabled = true);

                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        tbody.innerHTML = '';
                        if (Array.isArray(data.users)) {
                            data.users.forEach(user => {
                                const tr = document.createElement("tr");
                                tr.innerHTML = `
                        <td>${user.user_id}</td>
                        <td>${user.user?.full_name || 'N/A'}</td>
                        <td>${user.total_points}</td>
                        <td>#${user.rank}</td>
                    `;
                                tbody.appendChild(tr);
                            });
                        }
                        showMessage(data.message, data.status);
                    })
                    .catch(err => {
                        console.error('Fetch Error:', err);
                        showMessage('Error fetching user data', 'error');
                    })
                    .finally(() => {
                        buttons.forEach(btn => btn.disabled = false);
                    });
            }

            function recalculate() {
                const buttons = document.querySelectorAll("button");
                buttons.forEach(btn => btn.disabled = true);

                fetch('/api/users/recalculate')
                    .then(res => res.json())
                    .then(data => {
                        showMessage(data.message, data.status);
                        setTimeout(() => {
                            fetchUser();
                        }, 1000);
                    })
                    .catch(err => {
                        console.error('Recalculate Error:', err);
                        showMessage('Error during recalculation', 'error');
                    })
                    .finally(() => {
                        buttons.forEach(btn => btn.disabled = false);
                    });
            }

            const form = document.getElementById("filter-form");
            form.addEventListener("submit", function(e) {
                e.preventDefault();
                const userId = form.querySelector("input[name='user_id']").value.trim();
                const filter = form.querySelector("select[name='filter']").value;
                fetchUser(userId, filter);
            });

            function generate() {
                const buttons = document.querySelectorAll("button");
                buttons.forEach(btn => btn.disabled = true);

                fetch('/api/users/generate')
                    .then(res => res.json())
                    .then(data => {
                        showMessage(data.message, data.status);
                    })
                    .catch(err => {
                        console.error('Generate Error:', err);
                        showMessage('Error generating data', 'error');
                    })
                    .finally(() => {
                        buttons.forEach(btn => btn.disabled = false);
                    });
            }

            function addPoint() {
                const buttons = document.querySelectorAll("button");
                buttons.forEach(btn => btn.disabled = true);

                fetch('/api/users/points', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            user_id: form.querySelector("input[name='user_id']").value.trim(),
                            points: 1
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        showMessage(data.message, data.status);
                    })
                    .catch(err => {
                        console.error('Add Point Error:', err);
                        showMessage('Error adding point', 'error');
                    })
                    .finally(() => {
                        buttons.forEach(btn => btn.disabled = false);
                    });
            }
        </script>
    @endsection
