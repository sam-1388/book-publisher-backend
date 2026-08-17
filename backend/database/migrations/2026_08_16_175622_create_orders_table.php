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
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('contacts')->nullable();
                $table->string('email')->nullable();
                $table->string('address')->nullable();
                $table->string('phone_number')->nullable();
                $table->enum('payment', ['cash', 'paypal', 'visa', 'master card'])->default('cash');
                $table->string('status')->default('pending');
                $table->unsignedInteger('final_price_in_cents')->nullable();
                $table->text('notes')->nullable();
                $table->date('arrival_date')->nullable();
                $table->timestamps();
            });

            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->cascadeOnDelete();
                $table->json('files')->nullable();
                $table->boolean('purchase')->default(false);
                $table->boolean('print')->default(false);
                $table->boolean('publish')->default(false);
                $table->boolean('translate')->default(false);
                $table->boolean('other')->default(false);
                $table->string('book_title')->nullable();
                $table->foreignId('book_id')->nullable()->constrained()->nullOnDelete();
                $table->unsignedInteger('unit_price_in_cents')->nullable();
                $table->unsignedInteger('total_price_in_cents')->nullable();
                $table->unsignedInteger('quantity')->default(1);
                $table->text('comment')->nullable();
                $table->timestamps();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('order_items');
            Schema::dropIfExists('orders');
        }
    };
