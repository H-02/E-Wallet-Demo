<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTriggerUpdateWalletBalance extends Migration
{
    public function up()
    {
        // Create the trigger function
        DB::unprepared('
            CREATE OR REPLACE FUNCTION update_wallet_balance_and_transaction_status()
            RETURNS TRIGGER AS $$
            BEGIN
                -- Update the wallet balance and updated_at timestamp for the user
                UPDATE users
                SET wallet_balance = wallet_balance + NEW.amount,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = NEW.user_id;

                -- Update the is_user_updated column in the transactions table
                UPDATE transactions
                SET is_user_updated = TRUE
                WHERE id = NEW.id;

                RETURN NULL; -- No need to return NEW in AFTER triggers
            END;
            $$ LANGUAGE plpgsql;
        ');

        // Create the trigger
        DB::unprepared('
            CREATE TRIGGER update_wallet_and_status_on_transaction
            AFTER INSERT ON transactions
            FOR EACH ROW
            EXECUTE FUNCTION update_wallet_balance_and_transaction_status();
        ');
    }

    public function down()
    {
        // Drop the trigger and function if they exist
        DB::unprepared('DROP TRIGGER IF EXISTS update_wallet_and_status_on_transaction ON transactions;');
        DB::unprepared('DROP FUNCTION IF EXISTS update_wallet_balance_and_transaction_status;');
    }
}
