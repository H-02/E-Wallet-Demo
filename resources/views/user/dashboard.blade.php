@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Welcome, {{ Auth::user()->name }}</h1>
    <div class="row">
        <div class="col-lg-8">
            <!-- Wallet Balance Card -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h3>Your Wallet Balance</h3>
                    <p class="h4 text-primary"><span id="wallet-balance">Loading...</span></p>
                    <button id="deposit-btn" class="btn btn-success mt-3">Deposit Funds</button>
                    <button id="withdraw-btn" class="btn btn-danger mt-3 ms-2">Withdraw Funds</button>
                </div>
            </div>

            <!-- Transaction History -->
            <div class="card">
                <div class="card-header bg-light">
                    <strong>Transaction History</strong>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody id="transaction-table">
                            <!-- Transactions will be populated via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Function to load wallet balance and transaction history
        function loadWalletData() {
            $.get('{{ route('user.wallet') }}', function (data) {
                if (data.status === 'success') {
                    // Display wallet balance with proper formatting
                    $('#wallet-balance').text('$' + data.wallet_balance);
                } else {
                    alert('Error: Unable to fetch wallet balance.');
                }
            }).fail(function () {
                alert('Error: Unable to fetch wallet balance.');
            });

            loadTransactions();
        }

        // Function to load transaction history
        function loadTransactions() {
            $.get('{{ route('user.wallet.transactions') }}', function (data) {
                $('#transaction-table').html('');
                if (data.transactions.length === 0) {
                    $('#transaction-table').append('<tr><td colspan="3" class="text-center">No transactions found.</td></tr>');
                } else {
                    data.transactions.forEach(function (transaction) {
                        // Display absolute value for transaction amount
                        const absoluteAmount = Math.abs(transaction.amount).toFixed(2);

                        const transactionDate = new Date(transaction.transaction_time);
                        const formattedDate = transactionDate.toLocaleString();

                        $('#transaction-table').append(`
                            <tr>
                                <td>${formattedDate}</td>
                                <td>${transaction.type}</td>
                                <td>$${absoluteAmount}</td>
                            </tr>
                        `);
                    });
                }
            }).fail(function () {
                alert('Error: Unable to fetch transaction history.');
            });
        }

        // Load wallet data and transactions on page load
        loadWalletData();

        // Handle deposit button click
        $('#deposit-btn').click(function () {
            let amount = prompt('Enter the amount to deposit:');
            if (amount && !isNaN(amount) && parseFloat(amount) > 0) {
                $.ajax({
                    url: '{{ route('user.wallet.deposit') }}',
                    method: 'POST',
                    data: {
                        amount: amount,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        alert('Deposit successful!');
                        loadWalletData();
                    },
                    error: function (response) {
                        alert('Error: ' + response.responseJSON.message);
                    }
                });
            } else {
                alert('Please enter a valid amount.');
            }
        });

        // Handle withdraw button click
        $('#withdraw-btn').click(function () {
            let amount = prompt('Enter the amount to withdraw:');
            if (amount && !isNaN(amount) && parseFloat(amount) > 0) {
                $.ajax({
                    url: '{{ route('user.wallet.withdraw') }}',
                    method: 'POST',
                    data: {
                        amount: amount,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        alert('Withdrawal successful!');
                        loadWalletData();
                    },
                    error: function (response) {
                        alert('Error: ' + response.responseJSON.message);
                    }
                });
            } else {
                alert('Please enter a valid amount.');
            }
        });
    });
</script>
@endsection
