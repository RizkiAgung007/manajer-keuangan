public function up(): void
{
    Schema::table('tags', function (Blueprint $table) {
        // Tambahkan kolom ini setelah 'name'
        $table->integer('order_column')->default(0)->after('name');
    });
}

public function down(): void
{
    Schema::table('tags', function (Blueprint $table) {
        $table->dropColumn('order_column');
    });
}
