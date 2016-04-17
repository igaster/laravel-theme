<?php namespace igaster\laravelTheme\Tests\TestCase;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Orchestra\Testbench\TestCase;

class TestCaseWithDatbase extends TestCase
{

    // -----------------------------------------------
    //  Testcase Initialize: Setup Database/Load .env
    // -----------------------------------------------

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        
        if (file_exists(__DIR__.'/../.env')) {
            $dotenv = new Dotenv\Dotenv(__DIR__.'/../');
            $dotenv->load();
        }
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing'); // sqlite , memory
    }

    // -----------------------------------------------
    //  Helpers
    // -----------------------------------------------

    public function reloadModel(&$model){
        $className = get_class($model);
        $model = $className::find($model->id);
        return $model;
    }

    // -----------------------------------------------
    //  Added functionality
    // -----------------------------------------------

    protected function seeInDatabase($table, array $data, $connection = null)
    {
        $database = $this->database;

        $count = $database->table($table)->where($data)->count();

        $this->assertGreaterThan(0, $count, sprintf(
            'Unable to find row in database table [%s] that matched attributes [%s].', $table, json_encode($data)
        ));

        return $this;
    }

    protected function notSeeInDatabase($table, array $data, $connection = null)
    {
        $database = $this->database;

        $count = $database->table($table)->where($data)->count();

        $this->assertEquals(0, $count, sprintf(
            'Found unexpected records in database table [%s] that matched attributes [%s].', $table, json_encode($data)
        ));

        return $this;
    }

    // -----------------------------------------------
    //  Test Database Connection
    // -----------------------------------------------

    public function testDatabaseConnection()
    {
        $this->assertInstanceOf('Illuminate\Database\SQLiteConnection', \DB::connection());
    }

}    