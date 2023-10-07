@extends('layouts.app')
<style>
table {
    border-collapse: collapse;
    width: 100%;
}

th, td {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
}

th {
    background-color: #f2f2f2;
}
</style>

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="csv_file">Choose a CSV File:</label>
                            <input type="file" name="csv_file" id="csv_file" class="form-control-file">
                        </div>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Recent Uploads</div>

                <div class="card-body">
                    <table id="files-table" class="table">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>

<script>
    $(document).ready(function() {
        // Function to load and update the table with data
        function loadFilesTable() {
            $.ajax({
                url: '/get-files', 
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    var tableBody = $('#files-table tbody');
                    tableBody.empty();

                    $.each(data, function(index, file) {
                        var newDate = new Date(file.updated_at);
                        var row = '<tr>';
                        row += '<td>' + moment(newDate).format('YYYY-MM-DD HH:mm A'); + '</td>';
                        row += '<td>' + file.name + '</td>';
                        row += '<td>' + file.status + '</td>';
                        row += '</tr>';

                        tableBody.append(row);
                    });
                }
            });
        }

        // Initial load of the table
        loadFilesTable();

        setInterval(function() {
            loadFilesTable();
        }, 5000); 
    });
</script>
