<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            DROP PROCEDURE IF EXISTS sp_approve_project;
            CREATE PROCEDURE sp_approve_project(IN p_project_id INT, IN p_user_id INT)
            BEGIN
                DECLARE exit handler FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    SELECT 'failure' AS status;
                END;

                START TRANSACTION;

                UPDATE projects
                SET status = 'approved',updated_at = UTC_TIMESTAMP() -- Store time in UTC
                WHERE id = p_project_id;
                
                INSERT INTO audit_logs (user_id, project_id, action, created_at, updated_at)
                VALUES (p_user_id, p_project_id, 'approved', NOW(), NOW());

                COMMIT;

                SELECT 'success' AS status;
            END
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_approve_project');
    }
};
