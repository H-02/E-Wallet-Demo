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

<!-- Modal for User Details and Transaction History -->
<div class="modal fade" id="user-details-modal" tabindex="-1" aria-labelledby="userDetailsModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">  <!-- Increased width by adding modal-lg class -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userDetailsModalLabel">User Details and Transaction History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Dynamic content will be loaded here -->
                <div id="user-details">
                    <!-- User details will be injected here -->
                </div>
                <h5>Transaction History:</h5>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Transaction Type</th>
                        <th>Time</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody id="transaction-history">
                    <!-- Transactions will be populated here via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {
        // Fetch and populate user data via AJAX
        function loadUsers() {
            $.ajax({
                url: '{{ route('admin.users') }}', // Use the named route for users
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Include CSRF token
                },
                success: function (response) {
                    console.log("Users Data:", response); // Log the data to check if it's correct
                    $('#user-table').html(''); // Clear existing data

                    // Check if users data exists and is in correct structure
                    if (response.users && response.users.length > 0) {
                        response.users.forEach(function (user) {
                            $('#user-table').append(`
                                <tr>
                                    <td>${user.name}</td>
                                    <td>${user.email}</td>
                                    <td>${user.wallet_balance}</td>
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
                error: function (xhr, status, error) {
                    console.log("Error fetching users: ", error);
                }
            });
        }

        // Load users on page load
        loadUsers();

        // Handle search functionality
        $('#search-user').keyup(function () {
            let query = $(this).val().toLowerCase();
            $('#user-table tr').filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(query) > -1);
            });
        });

        // Handle "View Transactions" button click
        $(document).on('click', '.view-transactions', function () {
            let userId = $(this).data('id');

            // Perform an AJAX request to fetch user details and transactions
            $.ajax({
                url: '{{ route('admin.users.transactions', ':id') }}'.replace(':id', userId),
                type: 'GET',
                success: function (response) {
                    console.log("User Details:", response.user);
                    console.log("Transactions:", response.transactions);

                    // Display user details
                    let userDetails = `
                        <p><strong>Name:</strong> ${response.user.name}</p>
                        <p><strong>Email:</strong> ${response.user.email}</p>
                        <p><strong>Mobile Number:</strong> ${response.user.mobile_number}</p>
                        <p><strong>Wallet Balance:</strong> $${response.user.wallet_balance}</p>
                        <p><strong>Status:</strong> ${response.user.is_active ? 'Active' : 'Inactive'}</p>
                        <p><strong>Role:</strong> ${response.user.role}</p>
                    `;
                    $('#user-details').html(userDetails);

                    // Prepare the transaction history section
                    let transactionHistory = '';
                    response.transactions.forEach(function (transaction) {
                        let transactionTime = new Date(transaction.transaction_time).toLocaleString();
                        let transactionType = transaction.type === 'DEPOSIT' ? 'Deposit' : 'Withdraw';
                        let transactionAmount = Math.abs(parseFloat(transaction.amount)).toFixed(2); // Show amount as absolute value

                        transactionHistory += `
                            <tr>
                                <td>${transactionType}</td>
                                <td>${transactionTime}</td>
                                <td>$${transactionAmount}</td>
                            </tr>
                        `;
                    });

                    // Inject transaction history into the modal
                    if (transactionHistory) {
                        $('#transaction-history').html(transactionHistory);
                    } else {
                        $('#transaction-history').html('<tr><td colspan="3" class="text-center">No transactions found</td></tr>');
                    }

                    // Open modal using Bootstrap 5 method
                    var myModal = new bootstrap.Modal(document.getElementById('user-details-modal'));
                    myModal.show();
                },
                error: function (xhr, status, error) {
                    console.log("Error fetching user details: ", error);
                }
            });
        });

        // Handle "Deposit" button click
        $(document).on('click', '.manage-wallet[data-action="deposit"]', function () {
            let userId = $(this).data('id');
            let amount = prompt('Enter the amount to deposit:');
            if (amount && !isNaN(amount) && amount > 0) {
                $.ajax({
                    url: '{{ route('admin.users.deposit', ':id') }}'.replace(':id', userId),
                    method: 'POST',
                    data: {
                        amount: amount,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.status === 'success') {
                            alert(response.message); // Display success message
                            loadUsers(); // Reload users to update the wallet balance in the table
                        }
                    },
                    error: function (response) {
                        alert(response.responseJSON.message); // Show error message
                    }
                });
            }
        });

        // Handle "Withdraw" button click
        $(document).on('click', '.manage-wallet[data-action="withdraw"]', function () {
            let userId = $(this).data('id');
            let amount = prompt('Enter the amount to withdraw:');
            if (amount && !isNaN(amount) && amount > 0) {
                $.ajax({
                    url: '{{ route('admin.users.withdraw', ':id') }}'.replace(':id', userId),
                    method: 'POST',
                    data: {
                        amount: amount,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.status === 'success') {
                            alert(response.message); // Display success message
                            loadUsers(); // Reload users to update the wallet balance in the table
                        }
                    },
                    error: function (response) {
                        alert(response.responseJSON.message); // Show error message
                    }
                });
            }
        });
    });
</script>
@endsection
