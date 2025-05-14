<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Student Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Student Management</h1>

        <!-- Error Message -->
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <!-- Create/Update Form -->
        <form id="studentForm" class="mb-4">
            <input type="hidden" id="studentId">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" required>
            </div>
            <div class="mb-3">
                <label for="class" class="form-label">Class</label>
                <input type="text" class="form-control" id="class" required>
            </div>
            <div class="mb-3">
                <label for="marks" class="form-label">Marks</label>
                <input type="number" class="form-control" id="marks" min="0" max="100" required>
            </div>
            <button type="submit" class="btn btn-primary" id="submitBtn">Save Student</button>
            <button type="button" class="btn btn-secondary" id="resetBtn">Reset</button>
        </form>

        <!-- Students Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Class</th>
                    <th>Marks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="studentTable">
                @foreach ($students as $student)
                    <tr data-id="{{ $student->id }}">
                        <td>{{ $student->id }}</td>
                        <td>{{ $student->name }}</td>
                        <td>{{ $student->class }}</td>
                        <td>{{ $student->marks }}</td>
                        <td>
                            <button class="btn btn-sm btn-warning editBtn">Edit</button>
                            <button class="btn btn-sm btn-danger deleteBtn">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            // Set CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Reset form
            $('#resetBtn').click(function () {
                $('#studentForm')[0].reset();
                $('#studentId').val('');
                $('#submitBtn').text('Save Student');
            });

            // Submit form (Create/Update)
            $('#studentForm').submit(function (e) {
                e.preventDefault();
                let id = $('#studentId').val();
                let url = id ? `/students/${id}` : '/students';
                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    data: {
                        name: $('#name').val(),
                        class: $('#class').val(),
                        marks: $('#marks').val(),
                    },
                    success: function (response) {
                        if (response.success) {
                            if (method === 'POST') {
                                // Add new row
                                $('#studentTable').append(`
                                    <tr data-id="${response.student.id}">
                                        <td>${response.student.id}</td>
                                        <td>${response.student.name}</td>
                                        <td>${response.student.class}</td>
                                        <td>${response.student.marks}</td>
                                        <td>
                                            <button class="btn btn-sm btn-warning editBtn">Edit</button>
                                            <button class="btn btn-sm btn-danger deleteBtn">Delete</button>
                                        </td>
                                    </tr>
                                `);
                            } else {
                                // Update existing row
                                let row = $(`tr[data-id="${id}"]`);
                                row.find('td:eq(1)').text(response.student.name);
                                row.find('td:eq(2)').text(response.student.class);
                                row.find('td:eq(3)').text(response.student.marks);
                            }
                            $('#studentForm')[0].reset();
                            $('#studentId').val('');
                            $('#submitBtn').text('Save Student');
                            alert(response.message);
                        }
                    },
                    error: function (xhr) {
                        let errors = xhr.responseJSON.errors || { message: [xhr.responseJSON.message] };
                        let errorMsg = Object.values(errors).flat().join('\n');
                        alert('Error: ' + errorMsg);
                    }
                });
            });

            // Edit button click
            $(document).on('click', '.editBtn', function () {
                let row = $(this).closest('tr');
                let id = row.data('id');
                $('#studentId').val(id);
                $('#name').val(row.find('td:eq(1)').text());
                $('#class').val(row.find('td:eq(2)').text());
                $('#marks').val(row.find('td:eq(3)').text());
                $('#submitBtn').text('Update Student');
            });

            // Delete button click
            $(document).on('click', '.deleteBtn', function () {
                if (confirm('Are you sure you want to delete this student?')) {
                    let row = $(this).closest('tr');
                    let id = row.data('id');
                    $.ajax({
                        url: `/students/${id}`,
                        method: 'DELETE',
                        success: function (response) {
                            if (response.success) {
                                row.remove();
                                alert(response.message);
                            }
                        },
                        error: function (xhr) {
                            alert('Error: ' + xhr.responseJSON.message);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>