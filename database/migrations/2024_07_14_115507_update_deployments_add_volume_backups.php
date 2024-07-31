<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::update("
            UPDATE deployments
            SET data = jsonb_set(
              data,
              '{processes}',
              (
                SELECT jsonb_agg(
                  jsonb_set(
                    process,
                    '{volumes}',
                    COALESCE(
                      (
                        SELECT jsonb_agg(
                          volume_item || jsonb_build_object(
                            'id', 
                            concat('volume-', substr(md5(random()::text), 1, 11))
                          ) || jsonb_build_object(
                            'backupSchedule',
                            '{\"preset\": \"cron-disabled\"}'::jsonb
                          )
                        )
                        FROM jsonb_array_elements(process->'volumes') AS volume_item
                      ),
                      '[]'::jsonb
                    )
                  ) || jsonb_build_object(
                    'backupVolume',
                    CONCAT('{\"id\":\"backups-', substr(md5(random()::text), 1, 11) ,'\",\"name\":\"backups\",\"path\":\"/ptah/backups\",\"dockerName\":\"svc_', deployments.service_id,'_', (select name from services where id = deployments.service_id),'_svc_ptah_backups\",\"backupSchedule\":{\"expr\":null,\"preset\":\"cron-disabled\"}}')::jsonb
                  )
                )
                FROM jsonb_array_elements(data->'processes') AS process
              )
            );
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
