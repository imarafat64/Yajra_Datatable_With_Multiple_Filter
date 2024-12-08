<!doctype html>
<html lang="en">

<head>
    <title>Yajra Datatable</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Data Table CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css" />

</head>

<body>

    <div class="container py-5">
        <div class="card">
            <div class="card-header">
                <h5>Yajra Data Table Implementation In Laravel 11</h5>
            </div>
            <div class="daterange mt-3 mb-3 d-flex border-bottom pb-3">
                <div class="col-md-4 ms-4">
                    <label>Start Date:</label><br>
                    <input type="date" name="startDate" class="form-control daterange" id="start-date" />

                </div>
                <div class="col-md-4 ms-4">
                    <label>End Date:</label><br>
                    <input type="date" name="endDate" class="form-control daterange" id="end-date" />

                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped datatable">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone Number</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        {{-- <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone_number }}</td>
                                    <td>{{ $user->created_at }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">No Data Found!</td>
                                </tr>
                            @endforelse
                        </tbody> --}}
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
    <!-- Data Table Js -->
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>

    <!-- Daterange -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <script type="text/javascript">
        $(document).ready(function() {


            const table = $('.datatable').DataTable({
                serverSide: true,
                processing: true,
                ajax: {
                    url: '{{ route('users.index') }}',
                    data: function(payload) {
                        const startDate = $('#start-date').val();
                        const endDate = $('#end-date').val();

                        payload.startDate = startDate;
                        payload.endDate = endDate;

                    },
                },
                columns: [{

                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false // Disable sorting for this column
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'phone_number',
                        name: 'phone_number'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });


            // Add input areas for searching columns

            const inputSearchRow = $('.datatable dt-input').clone(true);



            $('.dt-input').on('keyup change', function() {
                const columnIndex = $(this).parent().index();
                const searchedTerm = $(this).val().trim();

                //Apply Collumn Search 
                table.column(columnIndex).search(searchedTerm).draw();
                console.log(columnIndex, searchedTerm);


            });

            // Delete User Functionality

            $('table').on('click', '.delete-user', function() {
                const userId = $(this).data('id');

                if (userId) {
                    if (confirm('Are you sure? you want to delete it')) {
                        $.ajax({
                            url: `{{ url('users/delete') }}/${userId}`,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}',
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    table.ajax.reload(null, false);
                                } else {
                                    alert(response.message);
                                }
                            },
                            error: function(error) {
                                alert('something went wrong!');
                            }
                        })

                    }
                }

            });

            // Edit User Functionality

            const editableColumns = [1, 2, 3];
            let currentEditableRow = null;

            $('table').on('click', '.edit-user', function() {
                const userId = $(this).data('id');
                const currentRow = $(this).closest('tr');

                if (currentEditableRow && currentEditableRow !== currentRow) {
                    resetEditableRow(currentEditableRow);
                }

                //calling new editable Row
                makeEditableRow(currentRow);

                currentEditableRow = currentRow;

                //Appending Action Button in the last column
                currentRow.find('td:last').html(
                    `<button class ="btn btn-primary btn-sm btn-update" data-id="${userId}">Update</button>
                    <button class ="btn btn-danger btn-sm" data-id="${userId}">Delete</button>
                    `);

                //Editable Row Function
                function makeEditableRow(currentRow) {

                    currentRow.find('td').each(function(index) {

                        const currentCell = $(this);
                        const currentText = currentCell.text().trim();

                        if (editableColumns.includes(index)) {
                            currentCell.html(
                                `<input type="text" class="form-control editable-input" value="${currentText}"/>`
                            );
                        }

                    });


                }

                //function : Reset Current Row Editable

                function resetEditableRow(currentEditableRow) {
                    currentEditableRow.find('td').each(function(index) {
                        const currentCell = $(this);

                        if (editableColumns.includes(index)) {

                            const currentValue = currentCell.find('input').val();

                            currentCell.html(`${currentValue}`);
                        }
                    });

                    const userId = currentEditableRow.find('.btn-update').data('id');

                    currentEditableRow.find('td:last').html(
                        `<button class ="btn btn-success btn-sm btn-edit" data-id="${userId}">Edit</button>
                    <button class ="btn btn-danger btn-sm" data-id="${userId}">Delete</button>
                    `);

                }
            });
            //update Function

            $('table').on('click', '.btn-update', function() {
                const userId = $(this).data('id');
                const currentRow = $(this).closest('tr');

                const updatedUserData = {};

                currentRow.find('td').each(function(index) {
                    if (editableColumns.includes(index)) {
                        const inputValue = $(this).find('input').val();

                        if (index === 1)
                            updatedUserData.name = inputValue;
                        if (index === 2)
                            updatedUserData.email = inputValue;

                        if (index === 3)
                            updatedUserData.phoneNumber = inputValue;


                    }
                });



                // Call ajax to update user data

                $.ajax({
                    url: '{{ route('users.update') }}',
                    type: 'PUT',
                    data: {
                        id: userId,
                        name: updatedUserData.name,
                        email: updatedUserData.email,
                        phone_number: updatedUserData.phoneNumber,
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            table.ajax.reload(null, false);

                        } else {
                            alert(response.message);
                        }

                    },
                    error: function(errorResponse) {
                        alert(response.message);

                    }

                });
            });

            // Daterange Filter

            $('#start-date, #end-date').on('change', function() {

                const startDate = $('#start-date').val();
                const endDate = $('#end-date').val();

                if (startDate && endDate) {
                    table.draw();
                }
            });



        });
    </script>

</body>

</html>
