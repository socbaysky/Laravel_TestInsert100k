<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Model\Test;
use Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TestInsert extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        DB::beginTransaction();
        try {
            $key = 'need_insert';
            $start = microtime(true);
            if (Cache::has($key)) {
                $text = 'insert';

                // retrive from cache and delete for next testing
                $datas = json_decode(Cache::pull($key), true);

                // split large array to small array for optimize speed insert
                $datas = $this->paginate($datas);
                
                // start insert
                foreach ($datas as $data) {
                    DB::table('tests')->insert($data);
                }
            } else {
                $text = "create";
                $data = [];
                // create 
                for ($i=0; $i < 100000; $i++) {
                    $name = $faker->name;
                    $phone = $faker->phoneNumber;
                    $email = $faker->unique()->email;
                    $address = $faker->address;
                    $gender = 'Female';
                    $company = 'CompanyNameGenerator™: A Company Name Generator';
                    $birthday = $faker->dateTimeThisCentury->format('Y-m-d');
                    $lorem = 'Ut itaque et quaerat doloremque eum praesentium. Rerum in saepe dolorem. Explicabo qui consequuntur commodi minima rem.';
                    $user_agent = $faker->userAgent;
                    $avatar = $faker->imageUrl(360, 360, 'cats', true, 'Faker', true);
                    $uuid = $faker->uuid;
                    $html_lorem = $faker->randomHtml(2,3);

                    $data[] = [
                        'name' => $name,
                        'phone' => $phone,
                        'email' => $email,
                        'addess' => $address,
                        'gender' => $gender,
                        'company' => $company,
                        'birthday' => $birthday,
                        'lorem' => $lorem,
                        'user_agent' => $user_agent,
                        'avatar' => $avatar,
                        'uuid' => $uuid,
                        'html_lorem' => $html_lorem
                    ];
                }

                Cache::put($key, json_encode($data));
            }
            DB::commit();

            // measurement
            $time_elapsed_secs = microtime(true) - $start;
            echo $time_elapsed_secs . 's '.$text.'. ';
        } catch (Exception $e) {
            DB::rollBack();
        
            throw new Exception($e->getMessage());
        }
        
    }

    public function paginate($items, $perPage = 10000, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
