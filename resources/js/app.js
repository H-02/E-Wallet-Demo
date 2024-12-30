// resources/js/app.js

$(document).ready(function() {
    // Before making API requests, add the Authorization header
    $(document).ajaxSend(function(e, xhr, options) {
        let token = localStorage.getItem('auth_token');
        if (token) {
            xhr.setRequestHeader('Authorization', 'Bearer ' + token);
        }
    });

    // Deposit button handler
    $('#deposit-btn').on('click', function() {
        let depositAmount = 100; // Example amount, replace with dynamic input if needed

        $.ajax({
            url: '/user/deposit',
            method: 'POST',
            data: {
                amount: depositAmount,
                _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
            },
            success: function(response) {
                // Update the wallet balance dynamically
                $('#wallet-balance').text(response.wallet_balance);

                // Update transaction history dynamically (optional)
                // For example, append the new transaction to the list
                $('#transaction-history').append('<li>Deposit: ' + depositAmount + ' | New Balance: ' + response.wallet_balance + '</li>');
            },
            error: function(xhr, status, error) {
                console.error("Error during deposit: ", error);
                alert("There was an error processing the deposit.");
            }
        });
    });

    // Withdraw button handler
    $('#withdraw-btn').on('click', function() {
        let withdrawAmount = 50; // Example amount, replace with dynamic input if needed

        $.ajax({
            url: '/user/withdraw',
            method: 'POST',
            data: {
                amount: withdrawAmount,
                _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
            },
            success: function(response) {
                // Update the wallet balance dynamically
                $('#wallet-balance').text(response.wallet_balance);

                // Update transaction history dynamically (optional)
                // For example, append the new transaction to the list
                $('#transaction-history').append('<li>Withdraw: ' + withdrawAmount + ' | New Balance: ' + response.wallet_balance + '</li>');
            },
            error: function(xhr, status, error) {
                console.error("Error during withdrawal: ", error);
                alert("There was an error processing the withdrawal.");
            }
        });
    });
});
