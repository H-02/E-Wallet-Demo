@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Admin Dashboard</h1>
    
    <!-- User Management Card -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light">
            <strong>User Management</strong>
            <input type="text" id="search-user" class="form-control mt-2" placeholder="Search by name or email">
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Wallet Balance</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="user-table">
                    <!-- User data will be populated via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
    // Fetch and populate user data via AJAX
    function loadUsers() {
        $.ajax({
            url: '/api/admin/users', // API endpoint for fetching users
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Include CSRF token
            },
            success: function(response) {
                console.log("Users Data:", response); // Log the data to check if it's correct
                $('#user-table').html('');
                
                if (response.users && response.users.length > 0) {
                    response.users.forEach(function(user) {
                        $('#user-table').append(`
                            <tr>
                                <td>${user.name}</td>
                                <td>${user.email}</td>
                                <td>$${user.wallet_balance}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm view-transactions" data-id="${user.id}">View Transactions</button>
                                    <button class="btn btn-success btn-sm manage-wallet" data-id="${user.id}" data-action="deposit">Deposit</button>
                                    <button class="btn btn-danger btn-sm manage-wallet" data-id="${user.id}" data-action="withdraw">Withdraw</button>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    $('#user-table').append('<tr><td colspan="4" class="text-center">No users found</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                console.log("Error fetching users: ", error);
            }
        });
    }

    // Load users on page load
    loadUsers();

    // Handle search functionality
    $('#search-user').keyup(function() {
        let query = $(this).val().toLowerCase();
        $('#user-table tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(query) > -1);
        });
    });

    // Handle "View Transactions" button click
    $(document).on('click', '.view-transactions', function() {
        let userId = $(this).data('id');
        $.get(`/api/admin/users/${userId}/transactions`, function(response) {
            // Show user details and transaction history in a modal or a separate section
            console.log(response);
            // You can append the transaction history here or show it in a modal.
        });
    });

    // Handle "Deposit" button click
    $(document).on('click', '.manage-wallet[data-action="deposit"]', function() {
        let userId = $(this).data('id');
        let amount = prompt('Enter the amount to deposit:');
        if (amount && !isNaN(amount) && amount > 0) {
            $.post(`/api/admin/users/${userId}/deposit`, { amount: amount, _token: '{{ csrf_token() }}' }, function(response) {
                if (response.status === 'success') {
                    // Update wallet balance dynamically
                    alert('Deposit successful! New balance: $' + response.wallet_balance);
                    loadUsers(); // Reload users to update the wallet balance in the table
                } else {
                    alert('Deposit failed.');
                }
            });
        }
    });

    // Handle "Withdraw" button click
    $(document).on('click', '.manage-wallet[data-action="withdraw"]', function() {
        let userId = $(this).data('id');
        let amount = prompt('Enter the amount to withdraw:');
        if (amount && !isNaN(amount) && amount > 0) {
            $.post(`/api/admin/users/${userId}/withdraw`, { amount: amount, _token: '{{ csrf_token() }}' }, function(response) {
                if (response.status === 'success') {
                    // Update wallet balance dynamically
                    alert('Withdrawal successful! New balance: $' + response.wallet_balance);
                    loadUsers(); // Reload users to update the wallet balance in the table
                } else {
                    alert('Withdrawal failed: ' + response.message);
                }
            });
        }
    });
});
</script>
@endsection
