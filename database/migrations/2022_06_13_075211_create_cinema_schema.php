<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCinemaSchema extends Migration
{
    /** ToDo: Create a migration that creates all tables for the following user stories

    For an example on how a UI for an api using this might look like, please try to book a show at https://in.bookmyshow.com/.
    To not introduce additional complexity, please consider only one cinema.

    Please list the tables that you would create including keys, foreign keys and attributes that are required by the user stories.

    ## User Stories

     **Movie exploration**
     * As a user I want to see which films can be watched and at what times
     * As a user I want to only see the shows which are not booked out

     **Show administration**
     * As a cinema owner I want to run different films at different times
     * As a cinema owner I want to run multiple films at the same time in different showrooms

     **Pricing**
     * As a cinema owner I want to get paid differently per show
     * As a cinema owner I want to give different seat types a percentage premium, for example 50 % more for vip seat

     **Seating**
     * As a user I want to book a seat
     * As a user I want to book a vip seat/couple seat/super vip/whatever
     * As a user I want to see which seats are still available
     * As a user I want to know where I'm sitting on my ticket
     * As a cinema owner I dont want to configure the seating for every show
     */
    public function up()
    {
        // throw new \Exception('implement in coding task 4, you can ignore this exception if you are just running the initial migrations.');



        Schema::create('exhibitors', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('user_id')
            ->unsigned();
            $table->foreign(['user_id'])
            ->references('id')->on('users')
                  ->onDelete('cascade');
            $table->string('company')->unique();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('address_2')->nullable();
            $table->string('city')->nullable();
            $table->char('state', 2)->nullable();
            $table->string('zip')->nullable();
            $table->string('slug')->unique();
            $table->boolean('terms')->default('0');
            $table->boolean('live')->default('0');
            $table->timestamps();
        });

        Schema::create('exhibitor_role_users', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('role');
            $table->timestamps();
        });

        DB::table('exhibitor_role_users')->insert([
            ['role' => 'owner', 'created_at' => now(), 'updated_at' => now()],
            ['role' => 'manager', 'created_at' => now(), 'updated_at' => now()],
            ['role' => 'staff', 'created_at' => now(), 'updated_at' => now()],
        ]);


        Schema::create('theaters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('exhibitor_id')->unsigned();
            $table->foreign(['exhibitor_id'])->references('id')
                  ->on('exhibitors')->onDelete('cascade');
            $table->string('name');
            $table->string('address');
            $table->string('city');
            $table->char('state', 2);
            $table->string('zip');
            $table->string('phone');
            $table->string('slug');
            $table->datetime('created_at');
            $table->datetime('canceled_at')->nullable();
            $table->softDeletes();
            $table->index('exhibitor_id');
            });
            Schema::create('movies', function (Blueprint $table) {
                $table->increments('id');
                $table->string('title');
                $table->string('rating')->nullable();
                $table->smallInteger('running_time')->unsigned()->nullable();
                $table->text('poster_url')->nullable();
            });

            Schema::create('showtimes', function (Blueprint $table) {
                $table->increments('id');
                $table->bigInteger('theater_id');
                $table->string('movie_id');
                $table->date('date');
                $table->string('time');
                $table->integer('timestamp');
                $table->boolean('reserved_seating');
                $table->boolean('sold_out');
                $table->json('attributes')->nullable();
            });


            Schema::create('orders', function (Blueprint $table) {
                $table->increments('id');
                $table->mediumInteger('grand_total')->nullable()->unsigned();
                $table->string('email');
                $table->bigInteger('mobile')->unsigned()->nullable();
                $table->integer('user_id')->unsigned()->nullable();
                $table->integer('exhibitor_id')->unsigned();
                $table->foreign('exhibitor_id')->references('id')
                      ->on('exhibitors');
                $table->integer('theater_id')->unsigned();
                $table->foreign('theater_id')->references('id')
                      ->on('theaters');
                $table->string('charge_id')->unique()->nullable();
                $table->string('refund_id')->unique()->nullable();
                $table->integer('processor_fee')->unsigned()->default(0);
                $table->string('name_on_card')->nullable();
                $table->string('card_brand')->nullable();
                $table->smallInteger('card_last_four')->unsigned()->nullable();
                $table->mediumInteger('card_first_six')->unsigned()->nullable();
                $table->tinyInteger('card_month')->unsigned()->nullable();
                $table->smallInteger('card_year')->unsigned()->nullable();
                $table->enum('agent', ['exhibitor']);
                $table->timestamps();
            });

            Schema::create('ticket_orders', function (Blueprint $table) {
                $table->increments('id');
                $table->mediumInteger('total')->unsigned()->nullable();
                $table->string('email');
                $table->bigInteger('mobile')->unsigned()->nullable();
                $table->integer('user_id')->unsigned()->nullable();
                $table->integer('movie_id')->unsigned();
                $table->foreign('movie_id')->references('id')->on('movies');
                $table->string('showtime_id');
                $table->dateTime('showtime');
                $table->string('seat_reservation_id')->nullable();
                $table->string('confirmation_code')->nullable();
                $table->string('charge_id')->unique()->nullable();
                $table->string('refund_id')->unique()->nullable();
                $table->string('card_brand')->nullable();
                $table->smallInteger('card_last_four')->unsigned()->nullable();
                $table->mediumInteger('card_first_six')->unsigned()->nullable();
                $table->tinyInteger('card_month')->unsigned()->nullable();
                $table->smallInteger('card_year')->unsigned()->nullable();
                $table->enum('agent', ['exhibitor']);
                $table->timestamps();
                });

                Schema::create('reserved_seats', function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('ticket_order_id')->unsigned();
                    $table->foreign('ticket_order_id')->references('id')
                          ->on('ticket_orders')->onDelete('cascade');
                    $table->string('area_label')->nullable();
                    $table->string('row_label')->nullable();
                    $table->string('seat_label')->nullable();
                });


                // Setting plan can be managed by two ways like One is from the file and other you can save into the database
                Schema::create('theater_plan_seats', function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('theater_id')->unsigned();
                    $table->foreign(['theater_id'])->references('id')
                          ->on('theaters')->onDelete('cascade');
                    $table->json('chair_with_row')->nullable();
                    $table->timestamps();
                });

                Schema::create('payments', function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('order_id')->unsigned();
                    $table->foreign('order_id')->references('id')->on('orders')
                          ->onDelete('cascade');
                    $table->integer('payment_type_id')->unsigned();
                    $table->string('payment_type_type');
                    $table->mediumInteger('total')->unsigned();
                    $table->string('charge_id')->unique()->nullable();
                    $table->string('refund_id')->unique()->nullable();
                    $table->timestamps();
                });







    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        // Schema::dropIfExists('payments');
    }
}
