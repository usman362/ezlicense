<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mailbox_messages', function (Blueprint $table) {
            $table->id();
            $table->string('direction', 10)->index();          // inbound | outbound
            $table->string('from_email')->nullable();
            $table->string('from_name')->nullable();
            $table->string('to_email')->nullable()->index();
            $table->string('to_name')->nullable();
            $table->string('cc')->nullable();
            $table->string('reply_to')->nullable();
            $table->string('subject')->nullable();
            $table->longText('body_html')->nullable();
            $table->longText('body_text')->nullable();
            $table->string('preview', 300)->nullable();         // short snippet for the list view
            $table->string('message_id')->nullable()->index();  // RFC Message-ID
            $table->string('in_reply_to')->nullable()->index(); // threading
            $table->string('status', 20)->default('received');  // received | sent | failed
            $table->boolean('is_read')->default(false)->index();
            $table->timestamp('read_at')->nullable();
            $table->boolean('has_attachments')->default(false);
            $table->json('attachments')->nullable();
            $table->json('meta')->nullable();                   // raw provider payload / headers
            $table->text('error')->nullable();
            $table->timestamps();

            $table->index(['direction', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mailbox_messages');
    }
};
