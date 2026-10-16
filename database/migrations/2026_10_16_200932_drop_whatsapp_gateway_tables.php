<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('whatsapp_messages');
        Schema::dropIfExists('faq_templates');
        Schema::dropIfExists('message_filter_rules');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to recreate deleted gateway tables
    }
};
