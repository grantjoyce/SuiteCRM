<?php

class ProspectListTest extends SuiteCRM\StateCheckerPHPUnitTestCaseAbstract
{
    public function setUp()
    {
        parent::setUp();

        global $current_user;
        get_sugar_config_defaults();
        $current_user = new User();
    }
    

	public function testcreate_export_query()
	{

		$prospectList = new ProspectList();

		//test with empty string params
		$expected = "SELECT
                                prospect_lists.*,
                                users.user_name as assigned_user_name FROM prospect_lists LEFT JOIN users
                                ON prospect_lists.assigned_user_id=users.id  WHERE  prospect_lists.deleted=0 ORDER BY prospect_lists.name";
		$actual = $prospectList->create_export_query('','');
		$this->assertSame($expected,$actual);


		//test with valid string params
		$expected = "SELECT
                                prospect_lists.*,
                                users.user_name as assigned_user_name FROM prospect_lists LEFT JOIN users
                                ON prospect_lists.assigned_user_id=users.id  WHERE users.user_name = \"\" AND  prospect_lists.deleted=0 ORDER BY prospect_lists.id";
		$actual = $prospectList->create_export_query('prospect_lists.id','users.user_name = ""');
		$this->assertSame($expected,$actual);

    }

    
	public function testcreate_list_query()
	{

		$prospectList = new ProspectList();

		//test with empty string params
		$expected = "SELECT users.user_name as assigned_user_name, prospect_lists.* FROM prospect_lists LEFT JOIN users
					ON prospect_lists.assigned_user_id=users.id where prospect_lists.deleted=0 ORDER BY prospect_lists.name";
		$actual = $prospectList->create_list_query('','');
		$this->assertSame($expected,$actual);


		//test with valid string params
		$expected = "SELECT users.user_name as assigned_user_name, prospect_lists.* FROM prospect_lists LEFT JOIN users
					ON prospect_lists.assigned_user_id=users.id where users.user_name = \"\" AND prospect_lists.deleted=0 ORDER BY prospect_lists.id";
		$actual = $prospectList->create_list_query('prospect_lists.id','users.user_name = ""');
		$this->assertSame($expected,$actual);

	}

	public function testProspectList()
	{

		//execute the contructor and check for the Object type and  attributes
		$prospectList = new ProspectList();

		$this->assertInstanceOf('ProspectList',$prospectList);
		$this->assertInstanceOf('SugarBean',$prospectList);

		$this->assertAttributeEquals('prospect_lists', 'table_name', $prospectList);
		$this->assertAttributeEquals('ProspectLists', 'module_dir', $prospectList);
		$this->assertAttributeEquals('ProspectList', 'object_name', $prospectList);

		$this->assertAttributeEquals("prospect_lists_prospects", 'rel_prospects_table', $prospectList);

	}

	public function testget_summary_text()
	{
        $state = new SuiteCRM\StateSaver();
        
        
		//error_reporting(E_ERROR | E_PARSE);

		$prospectList = new ProspectList();

		//test without setting name
		$this->assertEquals(Null,$prospectList->get_summary_text());

		//test with name set
		$prospectList->name = "test";
		$this->assertEquals('test',$prospectList->get_summary_text());
        
        // clean up
        
        
	}


    /**
     * @todo: NEEDS FIXING!
     */
	public function testcreate_export_members_query()
	{

        $this->assertTrue(true, "NEEDS FIXING!");
	}

	public function testsave() {

	// save state

        $state = new \SuiteCRM\StateSaver();
        $state->pushTable('aod_index');
        $state->pushTable('aod_indexevent');
        $state->pushTable('prospect_lists');
        $state->pushTable('tracker');
        $state->pushGlobals();

	// test
        
		$prospectList = new ProspectList();

		$prospectList->name = "test";
		$prospectList->description ="test description";

		$result = $prospectList->save();

		//test for record ID to verify that record is saved
		$this->assertTrue(isset($prospectList->id));
		$this->assertEquals(36, strlen($prospectList->id));


		//test set_prospect_relationship method
		$this->set_prospect_relationship($prospectList->id);


		//test set_prospect_relationship_single method
		$this->set_prospect_relationship_single($prospectList->id);


		//mark the record as deleted and verify that this record cannot be retrieved anymore.
		$prospectList->mark_deleted($prospectList->id);
		$result = $prospectList->retrieve($prospectList->id);
		$this->assertEquals(null,$result);

        // clean up
        
        $state->popGlobals();
        $state->popTable('tracker');
        $state->popTable('prospect_lists');
        $state->popTable('aod_indexevent');
        $state->popTable('aod_index');


	}


	public function testsave_relationship_changes()
    {
    	$this->markTestIncomplete('Error in query: columns mismatch | Error in methodd call params: 2nd param should be array but string given');
    }

	public function set_prospect_relationship($id)
	{
		$prospectList = new ProspectList();

		//preset the required attributes, retrive the bean by id and verify the count of records
		$link_ids = array('1','2');

		$prospectList->retrieve($id);
		$prospectList->set_prospect_relationship($id, $link_ids ,'related');

		$expected_count = $prospectList->get_entry_count();

		$this->assertEquals(2,$expected_count);


		//test clear_prospect_relationship method with expected counts
		$this->clear_prospect_relationship($id, '1');
		$this->clear_prospect_relationship($id, '2');

	}

	public function set_prospect_relationship_single($id)
	{
		$prospectList = new ProspectList();

		$prospectList->retrieve($id);
		$prospectList->set_prospect_relationship_single($id, '3' ,'related');

		$expected_count = $prospectList->get_entry_count();

		$this->assertEquals(1,$expected_count);

		$this->clear_prospect_relationship($id, '3');

	}


	public function clear_prospect_relationship($id, $related_id)
	{
		$prospectList = new ProspectList();

		//retrieve the bean and get counts before and after method execution for comparison.

		$prospectList->retrieve($id);

		$initial_count = (int)$prospectList->get_entry_count();

		$prospectList->clear_prospect_relationship($id, $related_id, 'related');

		$expected_count = (int)$prospectList->get_entry_count();
		$this->assertEquals($initial_count - 1 , $expected_count);
	}


	public function testmark_relationships_deleted()
	{
        $state = new SuiteCRM\StateSaver();
        
        
        //error_reporting(E_ERROR | E_PARSE);
        
        
		$prospectList = new ProspectList();

		//execute the method and test if it works and does not throws an exception.
		try {
			$prospectList->mark_relationships_deleted('');
			$this->assertTrue(true);
		}
		catch (Exception $e) {
			$this->fail($e->getMessage() . "\nTrace:\n" . $e->getTraceAsString());
		}

		$this->markTestIncomplete('Method has no implementation');
        
        // clean up
        
        

	}

	public function testfill_in_additional_list_fields()
	{
        $state = new SuiteCRM\StateSaver();
        
        
        //error_reporting(E_ERROR | E_PARSE);
        
        
		$prospectList = new ProspectList();

		//execute the method and test if it works and does not throws an exception.
		try {
			$prospectList->fill_in_additional_list_fields();
			$this->assertTrue(true);
		}
		catch (Exception $e) {
			$this->fail($e->getMessage() . "\nTrace:\n" . $e->getTraceAsString());
		}

		$this->markTestIncomplete('Method has no implementation');
        
        // clean up
        
        

	}

	public function testfill_in_additional_detail_fields()
	{
		$prospectList = new ProspectList();

		$prospectList->fill_in_additional_detail_fields();
		$this->assertEquals(0,$prospectList->entry_count);

	}


	public function testupdate_currency_id()
	{
        $state = new SuiteCRM\StateSaver();
        
        
        //error_reporting(E_ERROR | E_PARSE);
        
        

		$prospectList = new ProspectList();

		//execute the method and test if it works and does not throws an exception.
		try {
			$prospectList->update_currency_id('','');
			$this->assertTrue(true);
		}
		catch (Exception $e) {
			$this->fail($e->getMessage() . "\nTrace:\n" . $e->getTraceAsString());
		}

		$this->markTestIncomplete('Method has no implementation');
        
        // clean up
        
        

	}


	public function testget_entry_count()
	{
		$prospectList = new ProspectList();

		$result = $prospectList->get_entry_count();
		$this->assertEquals(0, $result);

	}


	public function testget_list_view_data(){

		$prospectList = new ProspectList();

		$expected = array( "DELETED"=> 0, "ENTRY_COUNT"=> '0' );
		$actual = $prospectList->get_list_view_data();
		$this->assertSame($expected, $actual);

	}

	public function testbuild_generic_where_clause()
	{

		$prospectList = new ProspectList();

		//test with empty string params
		$expected = "prospect_lists.name like '%'";
		$actual = $prospectList->build_generic_where_clause('');
		$this->assertSame($expected,$actual);


		//test with valid string params
		$expected = "prospect_lists.name like '1%'";
		$actual = $prospectList->build_generic_where_clause('1');
		$this->assertSame($expected,$actual);

	}


	public function testbean_implements(){

		$prospectList = new ProspectList();

		$this->assertEquals(false, $prospectList->bean_implements('')); //test with blank value
		$this->assertEquals(false, $prospectList->bean_implements('test')); //test with invalid value
		$this->assertEquals(true, $prospectList->bean_implements('ACL')); //test with valid value

	}

}





?>
